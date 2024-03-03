<?php

namespace App\Http\Controllers\Plan;

use App\Http\Controllers\Controller;
use App\Http\Requests\FreeTrialPlan\FreeTrialPlanCreateRequest;
use App\Http\Requests\FreeTrialPlan\FreeTrialPlanUpdateRequest;
use App\Http\Resources\Plan\FreeTrialPlanResource;
use App\Models\Plan\FreeTrialPlan;
use Illuminate\Http\Request;

class FreeTrialPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->responseSuccess([
            'free_trial_plans' => FreeTrialPlanResource::collection(FreeTrialPlan::latest()->get())
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(FreeTrialPlanCreateRequest $request)
    {
        $authUser = auth()->user();

        $data = $request->all();
        $data['created_by'] = $authUser ? $authUser->id : null;
        $freeTrialPlan = FreeTrialPlan::create($data);

        return $this->responseSuccess([
            'free_trial_plan' => FreeTrialPlanResource::make($freeTrialPlan)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        $freeTrialPlan = FreeTrialPlan::find($id);

        if (!$freeTrialPlan) {
            return $this->responseNotFound();
        }

        return $this->responseSuccess([
            'free_trial_plan' => FreeTrialPlanResource::make($freeTrialPlan)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FreeTrialPlanUpdateRequest $request, $id)
    {
        $freeTrialPlan = FreeTrialPlan::find($id);

        if (!$freeTrialPlan) {
            return $this->responseNotFound();
        }

        $authUser = auth()->user();

        $data = $request->all();
        $data['updated_by'] = $authUser ? $authUser->id : null;
        $freeTrialPlan->update($data);

        return $this->responseSuccess([
            'free_trial_plan' => FreeTrialPlanResource::make($freeTrialPlan)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $freeTrialPlan = FreeTrialPlan::find($id);

        if (!$freeTrialPlan) {
            return $this->responseNotFound();
        }

        $freeTrialPlan->delete();

        return $this->responseSuccess();
    }
}
