<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlaceResource;
use App\Http\Resources\PlaceResourcePaginated;
use App\Models\Place;
use App\Repositories\PlaceRepository;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    /**
     * PlaceRepository
     *
     * @var PlaceRepository
     */
    private $placeRepository;

    public function __construct(PlaceRepository $placeRepository) {
        $this->placeRepository = $placeRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->perPage ?? 20;
        $places = $this->placeRepository->getAllFiltered($request->all());

        $places->with(['defaultImage']);
        $placesPaginated = $places->paginate($perPage);
        
        $placeResource = PlaceResourcePaginated::make($placesPaginated);
        return $this->responseSuccess($placeResource);
    }

    /**
     * Display the specified resource.
     */
    public function get(string $id)
    {
        $place = Place::with([
            'images'
        ])->find($id);
        if(!$place){
            return $this->responseNotFound();
        }
        
        return $this->responseSuccess(PlaceResource::make($place));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
