<?php

namespace App\Http\Controllers;

use App\Http\Resources\Attraction\AttractionResource;
use App\Http\Resources\News\NewsCategoryResource;
use App\Http\Resources\News\NewsResource;
use App\Models\Attraction;
use App\Models\Language;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->responseSuccess();
    }

    public function getMenu(Request $request)
    {
        $langId = getLnaguageId($request);
        //TODO: cache
        $newsCategories = NewsCategory::treeOf(function ($q) {
            $q->whereNull('parent_id');
        })
        ->with([
            't' => fn ($query) => $query->where('language_id', $langId),
        ])
        ->get()->toTree();

        return $this->responseSuccess($newsCategories);
        //return $this->responseSuccess(NewsCategoryResource::collection($newsCategories));
    }

    /**
     * Fetch home page data
     *
     * @return \Illuminate\Http\Response
     */
    public function getHomePageData(Request $request)
    {
        $langId = getLnaguageId($request);

        $attractions = Attraction::notSuggested()
            ->orderByRaw('-order_num DESC')
            ->orderByDesc('id')
            ->limit(3)
            ->with([
                'images', 
                'thumbnail',
                'translation' => fn ($query) => $query->where('language_id', $langId),
            ])
            ->get();
        $suggestedAttractions = Attraction::suggested()
            ->orderByDesc('id')
            ->limit(3)
            ->with([
                'images', 
                'thumbnail',
                'translation' => fn ($query) => $query->where('language_id', $langId),
            ])
            ->get();
        $news = News::orderByDesc('id')
            ->limit(3)
            ->with([
                'images', 
                'thumbnail',
            ])
            ->get();

        $counts = DB::select("
            SELECT 'attractions' AS `table`, COUNT(*) AS `count` FROM attractions
            UNION ALL
            SELECT 'news' AS `table`, COUNT(*) AS `count` FROM news
            UNION ALL
            SELECT 'places' AS `table`, COUNT(*) AS `count` FROM places
        ");

        return $this->responseSuccess([
            'attractions' => AttractionResource::collection($attractions),
            'suggested_attractions' => AttractionResource::collection($suggestedAttractions),
            'news' => NewsResource::collection($news),
            'counts' => [
                'attractions' => $counts[0]->count,
                'news' => $counts[1]->count,
                'places' => $counts[2]->count,
            ]
        ]);
    }
}
