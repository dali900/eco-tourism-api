<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegulationType;
use Illuminate\Support\Facades\DB;
use App\Contracts\RegulationTypeRepositoryInterface;
use App\Http\Resources\Regulation\RegulationTypeResource;
use App\Http\Resources\Regulation\RegulationTypeResourcePaginated;

class RegulationTypeController extends Controller
{
    /**
     * RegulationTypeRepository
     *
     * @var RegulationTypeRepositoryInterface
     */
    private $regulationTypeRepository;

    public function __construct(RegulationTypeRepositoryInterface $regulationTypeRepository) {
        $this->regulationTypeRepository = $regulationTypeRepository;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($app, $id)
    {
        $regulationType = RegulationType::find($id);
        if(!$regulationType){
            return $this->responseNotFound();
        }
        return $this->responseSuccess([
            'regulation' => RegulationTypeResource::make($regulationType)
        ]);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request,$app)
    {
        $perPage = $request->perPage ?? 20;
        $regulationTypes = $this->regulationTypeRepository->getAllFiltered($request->all(), $app);
        //$regulationTypes->groupBy('name')->select(DB::raw("max(id),max(created_at)"));
        $regulationTypes->with('parent');
        $regulationTypesPaginated = $regulationTypes->paginate($perPage);
        return $this->responseSuccess([
            'regulation_types' => RegulationTypeResourcePaginated::make($regulationTypesPaginated)
        ]);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getRoots(Request $request, $app)
    {
        return $this->responseSuccess([
            'regulation_root_types' => RegulationType::whereApp($app)->whereNull('parent_id')->get()
        ]);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getTree(Request $request, $app)
    {
        $regulationTypes = RegulationType::select('id as key', 'laravel_cte.*')
        ->treeOf(function ($q) use ($app) {
            $q->where('app', $app)
                ->whereNull('parent_id');
        })
        ->orderBy('id', 'asc');

        return $this->responseSuccess([
            'tree' => $regulationTypes->get()->toTree(),
            'count' => $regulationTypes->count()
        ]);
    }
    //If first version of getTree is not working when selection additional fields
    /* public function getTree(Request $request, $app)
    {
        $regulationTypes = RegulationType::with(['allChildren'])->whereApp($app)->whereNull('parent_id')->get();

        return $this->responseSuccess([
            'tree' => RegulationTypeResource::collection($regulationTypes),
            //'tree' => $regulationTypes
        ]);
    } */
    /* public function filterTree(Request $request, $app)
    {
        $regulationTypes = RegulationType::withQueryConstraint(function($query){
            $query->where('regulation_types.name','LIKE', '%Zakon%');
        }, function() use ($app){
            RegulationType::select('id as key', 'laravel_cte.*')
            ->treeOf(function ($q) use ($app) {
                $q->where('app', $app);
            })
            ->orderBy('id', 'asc')
            ->get();
        });

        return $this->responseSuccess([
            'tree' => $regulationTypes,
        ]);
    } */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $app)
    {
        $attr = $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|numeric'
		]);
        
        $user = auth()->user();

        $data = $request->all();
        $data['app'] = $app;
        $data['user_id'] = $user->id;

        $regulationType = RegulationType::create($data);
        $regulationType->load('parent');
        return $this->responseSuccess([
            'regulation_type' => RegulationTypeResource::make($regulationType)
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
            'name' => 'required|string',
            'parent_id' => 'nullable|numeric'
		]);
       
        $user = auth()->user();

        $regulationType = RegulationType::find($id);
        if(!$regulationType){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;
        //prevent seting parent as parent
        if (!empty($data['parent_id'])) {
            //set parents child as its parent (set child as parent of its parent)
            $parentChildParent = RegulationType::where('id', $data['parent_id'])->where('parent_id', $id)->exists();
            if ($data['parent_id'] == $id || $parentChildParent) {
                $data['parent_id'] = null;
            } 
        }
        if(!$regulationType->update($data)){
            return $this->responseErrorSavingModel();
        }
        $regulationType->load('parent');
        return $this->responseSuccess([
            'regulation_type' => RegulationTypeResource::make($regulationType)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $app, $id)
    {
        $regulationType = RegulationType::find($id);
        if(!$regulationType){
            return $this->responseNotFound();
        }
        $regulationType->delete();

        $perPage = $request->perPage ?? 20;
        $regulationTypes = $this->regulationTypeRepository->getAllFiltered($request->all(), $app);
        $regulationTypes->with('parent');
        $regulationTypesPaginated = $regulationTypes->paginate($perPage);
        return $this->responseSuccess([
            'regulation_types' => RegulationTypeResourcePaginated::make($regulationTypesPaginated)
        ]);
    }
}
