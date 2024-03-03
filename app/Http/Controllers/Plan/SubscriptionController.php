<?php

namespace App\Http\Controllers\Plan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Plan\Subscription;
use App\Contracts\SubscriptionRepositoryInterface;
use App\Http\Resources\Plan\SubscriptionResource;
use App\Http\Requests\Subscription\SubscriptionCreateRequest;
use App\Http\Requests\Subscription\SubscriptionUpdateRequest;
use App\Http\Resources\Plan\SubscriptionResourcePaginated;
use App\Models\Plan\FreeTrial;
use App\Models\User;

class SubscriptionController extends Controller
{
    private $subscriptionRepository;

    public function __construct(SubscriptionRepositoryInterface $subscriptionRepository) {
        $this->subscriptionRepository = $subscriptionRepository;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $app)
    {
        $perPage = $request->perPage ?? 20;
        $subscriptions = $this->subscriptionRepository->getAllFiltered($request->all(), $app);

        $subscriptions = $subscriptions->with(['plan','createdByUser']);
        $subscriptionsPaginated = $subscriptions->paginate($perPage);
        
        return $this->responseSuccess([
            'subscriptions' => SubscriptionResourcePaginated::make($subscriptionsPaginated)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(SubscriptionCreateRequest $request, $app)
    {
        $authUser = auth()->user();

        $data = $request->all();
        $data['created_by'] = $authUser ? $authUser->id : null;
        $newData = $this->subscriptionRepository->prepareData($data);
        $subscription = Subscription::create($newData);
        $subscription->load('plan');

        $freeTrials = FreeTrial::where("user_id", $subscription->user_id)
            ->where('app', $app)
            ->update(['status' => FreeTrial::STATUS_CANCELED]);
        
        //creating subscription for different selected app
        if ($app !== $data['app']) {
            return $this->responseSuccessMsg();
        } 
        return $this->responseSuccess([
            'subscription' => SubscriptionResource::make($subscription)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($app, $id)
    {
        $subscription = Subscription::with(['createdByUser', 'updatedByUser', 'plan'])->find($id);

        if (!$subscription) {
            return $this->responseNotFound();
        }

        return $this->responseSuccess([
            'subscription' => SubscriptionResource::make($subscription)
        ]);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUserSubscriptions(Request $request, $app, $userId)
    {
        if (!User::whereId($userId)->exists()) {
            return $this->responseNotFoundMsg('Cannot find user');
        }
        $perPage = $request->perPage ?? 20;
        $subscriptions = $this->subscriptionRepository->getAllFiltered($request->all(), $app);
        $subscriptions = $subscriptions->where('user_id', $userId);
        $subscriptions->with(['plan', 'createdByUser']);
        $subscriptionsPaginated = $subscriptions->paginate($perPage);
        $subscriptionsResource = SubscriptionResourcePaginated::make($subscriptionsPaginated);
        return $this->responseSuccess($subscriptionsResource);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubscriptionUpdateRequest $request, $app, $id)
    {
        $subscription = Subscription::with('plan')->find($id);

        if (!$subscription) {
            return $this->responseNotFound();
        }

        $authUser = auth()->user();

        $data = $request->all(['subscription_plan_id', 'start_date', 'end_date', 'status', 'active', 'note']);
        $data['updated_by'] = $authUser ? $authUser->id : null;
        $data = $this->subscriptionRepository->prepareDataForModel($subscription, $data);
        $subscription->update($data);
        $subscription->load(['createdByUser', 'updatedByUser', 'plan']);

        return $this->responseSuccess([
            'subscription' => SubscriptionResource::make($subscription)
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
        $subscription = Subscription::find($id);

        if (!$subscription) {
            return $this->responseNotFound();
        }

        $subscription->delete();

        return $this->responseSuccess();
    }
}
