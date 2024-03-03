<?php

namespace App\Http\Controllers\Plan;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionPlan\SubscriptionPlanCreateRequest;
use App\Http\Requests\SubscriptionPlan\SubscriptionPlanUpdateRequest;
use App\Http\Resources\Plan\SubscriptionPlanResource;
use App\Models\Plan\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->responseSuccess([
            'subscription_plans' => SubscriptionPlanResource::collection(SubscriptionPlan::latest()->get())
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(SubscriptionPlanCreateRequest $request)
    {
        $authUser = auth()->user();

        $data = $request->all();
        $data['created_by'] = $authUser ? $authUser->id : null;
        $user = SubscriptionPlan::create($data);

        return $this->responseSuccess([
            'subscription_plan' => SubscriptionPlanResource::make($user)
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
        $subscriptionPlan = SubscriptionPlan::find($id);

        if (!$subscriptionPlan) {
            return $this->responseNotFound();
        }

        return $this->responseSuccess([
            'subscription_plan' => SubscriptionPlanResource::make($subscriptionPlan)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubscriptionPlanUpdateRequest $request, $id)
    {
        $subscriptionPlan = SubscriptionPlan::find($id);

        if (!$subscriptionPlan) {
            return $this->responseNotFound();
        }

        $authUser = auth()->user();

        $data = $request->all();
        $data['updated_by'] = $authUser ? $authUser->id : null;
        $subscriptionPlan->update($data);

        return $this->responseSuccess([
            'subscription_plan' => SubscriptionPlanResource::make($subscriptionPlan)
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
        $subscriptionPlan = SubscriptionPlan::find($id);

        if (!$subscriptionPlan) {
            return $this->responseNotFound();
        }

        $subscriptionPlan->delete();

        return $this->responseSuccess();
    }
}
