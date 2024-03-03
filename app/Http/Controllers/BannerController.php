<?php

namespace App\Http\Controllers;

use App\Http\Resources\BannerResource;
use App\Contracts\BannerRepositoryInterface;
use App\Http\Resources\BannerResourcePaginated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Banner;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BannerController extends Controller
{
    /**
     * BannerRepository
     *
     * @var BannerRepository
     */
    private $bannerRepository;

    public function __construct(BannerRepositoryInterface $bannerRepository) {
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($app, $id)
    {
        $banner = Banner::find($id);
        if(!$banner){
            return $this->responseNotFound();
        }
        return $this->responseSuccess([
            'banner' => BannerResource::make($banner)
        ]);
    }

    /**
     * Find banner by slyg
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBySlug($app, $slug)
    {
        $banner = Banner::whereSlug($slug)->first();
        if(!$banner){
            return $this->responseNotFound();
        }
        return $this->responseSuccess([
            'banner' => BannerResource::make($banner)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getLeft(Request $request, $app)
    {
        $banner = Banner::where('position', 'left')->first();
        return $this->responseSuccess([
            'banner' => $banner ? BannerResource::make($banner) : null
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request, $app)
    {
        $perPage = $request->perPage ?? 20;
        $banners = $this->bannerRepository->getAllFiltered($request->all(), $app);
        $bannerPaginated = $banners->paginate($perPage);
        $bannerResource = BannerResourcePaginated::make($bannerPaginated);
        return $this->responseSuccess([
            'banners' => $bannerResource
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $app)
    {
        $attr = $request->validate([
            'content' => 'required|string',
            'position' => 'required|string',
            'title' => 'nullable|max:255',
            'button_title' => 'nullable|max:64',
            'question_label' => 'nullable|max:255',
            'message' => 'nullable|max:255',
		]);

        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;
        $data['slug'] = Str::slug(Str::limit($data['title'], 128));

        $banner = Banner::create($data);

        return $this->responseSuccess([
            'banner' => BannerResource::make($banner)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $app, $id)
    {
        $attr = $request->validate([
			'content' => 'required|string',
            'position' => 'required|string',
            'title' => 'nullable|max:255',
            'button_title' => 'nullable|max:64',
            'question_label' => 'nullable|max:255',
            'message' => 'nullable|max:255',
		]);

        $user = auth()->user();

        $banner = Banner::find($id);
        if(!$banner){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;
        $data['slug'] = Str::slug(Str::limit($data['title'], 128));

        $banner->update($data);
        return $this->responseSuccess([
            'banner' => BannerResource::make($banner)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($app, $id)
    {
        $banner = Banner::find($id);
        if(!$banner){
            return $this->responseNotFound();
        }
        //delete file
        if(!empty($banner->file_path)){
            Storage::delete($banner->file_path);
        }
        $banner->delete();
        return $this->responseSuccess();
    }
}
