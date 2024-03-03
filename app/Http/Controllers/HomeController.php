<?php

namespace App\Http\Controllers;

use App\Models\ArticleType;
use App\Models\DocumentType;
use App\Models\QuestionType;
use App\Models\RegulationType;
use Illuminate\Http\Request;

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

    public function getMenu($app)
    {
        //TODO: cache
        /*
            \App\Models\RegulationType::treeOf(function($q){$q->where('app','zzs');})->get()->toTree()
        */
        $regulationTypes = RegulationType::treeOf(function ($q) use ($app) {
            $q->where('app', $app)->whereNull('parent_id');
        })->get()->toTree();
        $documentTypes = DocumentType::treeOf(function ($q) use ($app) {
            $q->where('app', $app)->whereNull('parent_id');
        })->get()->toTree();
        $articleTypes = ArticleType::treeOf(function ($q) use ($app) {
            $q->where('app', $app)->whereNull('parent_id');
        })->get()->toTree();
        $questionTypes = QuestionType::treeOf(function ($q) use ($app) {
            $q->where('app', $app)->whereNull('parent_id');
        })->get()->toTree();

        return $this->responseSuccess([
            'regulation_menu' => $regulationTypes,
            'document_menu' => $documentTypes,
            'article_menu' => $articleTypes,
            'question_menu' => $questionTypes,
        ]);
    }
}
