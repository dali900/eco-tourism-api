<?php

namespace App\Http\Controllers\Plan;

use App\Contracts\FreeTrialRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\FreeTrial\FreeTrialCreateRequest;
use App\Http\Requests\FreeTrial\FreeTrialUpdateRequest;
use App\Http\Resources\Plan\FreeTrialResource;
use App\Http\Resources\Plan\FreeTrialResourcePaginated;
use App\Models\Plan\FreeTrial;
use App\Models\User;
use App\Notifications\FreeTrialStartedNotification;
use Illuminate\Http\Request;

class FreeTrialController extends Controller
{
    private $freeTrialRepository;

    public function __construct(FreeTrialRepositoryInterface $freeTrialRepository) {
        $this->freeTrialRepository = $freeTrialRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $app)
    {
        $perPage = $request->perPage ?? 20;
        $freeTrials = $this->freeTrialRepository->getAllFiltered($request->all(), $app);

        $freeTrials = $freeTrials->with(['plan', 'createdByUser']);
        $freeTrialsPaginated = $freeTrials->paginate($perPage);
        
        return $this->responseSuccess([
            'free_trials' => FreeTrialResourcePaginated::make($freeTrialsPaginated)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(FreeTrialCreateRequest $request, $app)
    {
        $authUser = auth()->user();

        $data = $request->all();
        $data['created_by'] = $authUser ? $authUser->id : null;
        $data['app'] = $data['app'] ?? $app;
        $newData = $this->freeTrialRepository->prepareData($data);
        $freeTrial = FreeTrial::create($newData);
        $freeTrial->load('plan');

        //send email only in production or if in local then only to mailtrap
        if(config('app.env') === 'production' || str_contains(config('mail.mailers.smtp.host'), 'mailtrap') ){
            $user = User::find($request->user_id);
            $user->notify((new FreeTrialStartedNotification($freeTrial, $data['app'])));
        }

        //creating subscription for different selected app
        if ($app !== $data['app']) {
            return $this->responseSuccessMsg();
        } 

        return $this->responseSuccess([
            'free_trial' => FreeTrialResource::make($freeTrial)
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
        $freeTrial = FreeTrial::with(['createdByUser', 'updatedByUser', 'plan'])->find($id);

        if (!$freeTrial) {
            return $this->responseNotFound();
        }

        return $this->responseSuccess([
            'free_trial' => FreeTrialResource::make($freeTrial)
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUserFreeTrial(Request $request, $app, $userId)
    {
        if (!User::whereId($userId)->exists()) {
            return $this->responseNotFoundMsg('Cannot find user');
        }
        
        $freeTrial = FreeTrial::with(['plan', 'createdByUser'])
            ->where('user_id', $userId)
            ->where('app', $app)
            ->latest()
            ->first();
        if (!$freeTrial) {
            return $this->responseNotFoundMsg('Cannot find free trial');
        }
        return $this->responseSuccess(FreeTrialResource::make($freeTrial));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FreeTrialUpdateRequest $request, $app, $id)
    {
        $freeTrial = FreeTrial::with('plan')->find($id);

        if (!$freeTrial) {
            return $this->responseNotFound();
        }

        $authUser = auth()->user();

        $data = $request->only(['free_trial_plan_id', 'start_date', 'end_date', 'status', 'active']);
        $data['updated_by'] = $authUser ? $authUser->id : null;
        $data = $this->freeTrialRepository->prepareDataForModel($freeTrial, $data);
        $freeTrial->update($data);
        $freeTrial->load(['createdByUser', 'updatedByUser', 'plan']);

        return $this->responseSuccess([
            'free_trial' => FreeTrialResource::make($freeTrial)
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
        $freeTrial = FreeTrial::find($id);

        if (!$freeTrial) {
            return $this->responseNotFound();
        }

        $freeTrial->delete();

        return $this->responseSuccess();
    }
}
