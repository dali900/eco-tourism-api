<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Exports\ExportUsers;
use Illuminate\Http\Request;
use App\Models\Plan\FreeTrial;
use App\Models\Plan\FreeTrialPlan;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Events\FreeTrialStartedEvent;
use Illuminate\Support\Facades\Validator;
use App\Contracts\UserRepositoryInterface;
use App\Http\Resources\UserResourcePaginated;
use App\Contracts\FreeTrialRepositoryInterface;
use App\Contracts\UserProfileRepositoryInterface;
use App\Http\Resources\Admin\UserProfileResource;
use App\Notifications\FreeTrialStartedNotification;
use App\Notifications\ContactConfirmationNotification;
use App\Http\Resources\Admin\UserProfileResourceQueried;
use App\Http\Resources\Admin\UserProfileResourcePaginated;
use App\Models\App;

class UserController extends Controller
{
    /**
     * UserRepository
     *
     * @var UserRepositoryInterface
     */
    private $userRepository;
    
    /**
     * userProfileRepository
     *
     * @var UserProfileRepositoryInterface
     */
    private $userProfileRepository;
    
    /**
     * userProfileRepository
     *
     * @var FreeTrialRepositoryInterface
     */
    private $freeTrialRepository;

    public function __construct(
        UserRepositoryInterface $userRepository, 
        FreeTrialRepositoryInterface $freeTrialRepository,
        UserProfileRepositoryInterface $userProfileRepository
        )
    {
        $this->userRepository = $userRepository;
        $this->freeTrialRepository = $freeTrialRepository;
        $this->userProfileRepository = $userProfileRepository;
    }

    /**
     * Return user resource
     *
     * @param Request $requset
     * @return \Illuminate\Http\Response
     */
    public function getAuthUser(Request $requset)
    {
        $user = auth()->user();
        $userResource = null;
        if ($user) {
            $userResource = UserResource::make($user);
        }

        return $this->responseSuccess([
            'user' => $userResource,
            'csrf_token' => csrf_token()
        ]);
    }
    
    /**
     * Return user resource
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getUser($id)
    {
        $user = User::find($id);
        if(!$user){
            return $this->responseNotFound();
        }

        return $this->responseSuccess([
            'user' => UserResource::make($user),
            'roles' => User::getRoles()
        ]);
    }
    
    /**
     * Return user profile resource form admin 
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getUserProfile($id)
    {
        $authUser = auth()->user();
        $user = User::find($id);
        if(!$user){
            return $this->responseNotFound();
        }
        
        if ($authUser->hasAdminAccess()) {
            //admin resourece
            $userResource = UserProfileResource::make($user);
        } else {
            $userResource = UserResource::make($user);
        }

        return $this->responseSuccess([
            'user' => $userResource,
            'roles' => User::getRoles()
        ]);
    }
    
    public function getUserProfiles(Request $request)
    {
        $filters = $request->all();
        $usersQuery = $this->userProfileRepository->getAllFiltered($filters);
        return $this->responseSuccess([
            'user_profiles' => UserProfileResourcePaginated::make($usersQuery->paginate($request->perPage ?? 30)),
            'roles' => User::getRoles()
        ]);
    }

    public function getFakeUser()
    {
        $user = \App\Models\User::factory()->make();
        return $this->responseSuccess([
            'user' => UserResource::make($user),
            'roles' => User::getRoles()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request)
    {
        $perPage = $request->perPage ?? 20;
        $users = $this->userRepository->getAllFiltered($request->all());

        $userPaginated = $users->paginate($perPage);

        return $this->responseSuccess([
            'users' => UserResourcePaginated::make($userPaginated),
            'roles' => User::getRoles()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = $this->createUser($request);

        return $this->responseSuccess([
            'user' => UserResource::make($user)
        ]);
    }
    
    /**
     * API for admin users
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function adminCreate(Request $request)
    {
        $user = $this->createUser($request);

        $userProfile = $this->userProfileRepository->getByUserId($user->id);

        return $this->responseSuccess([
            'user' => UserProfileResource::make($user),
            'user_profile' => UserProfileResourceQueried::make($userProfile)
        ]);
        

    }

    private function createUser (Request $request) {
        $attr = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'company_name' => 'required|string',
            'phone_number' => 'required|string',
            'position' => 'required|string',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|min:8|same:password',
        ], [
            'email.unique' => 'Ovaj email je već iskorišćen.',
        ]);

        $authUser = auth()->user();

        $data = $request->all();
        $data['name'] = $data['first_name'].' '.$data['last_name'];
        $data['user_id'] = $authUser ? $authUser->id : null;
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->update($data);

        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if(!$user){
            return $this->responseNotFound();
        }
        $data = $request->all();
        if ($data['password'] === null) {
            unset($data['password']);
        }

        Validator::make($data, [
            'first_name' => 'sometimes|required|string',
            'last_name' => 'sometimes|required|string',
            'email' => 'sometimes|required|string|unique:users,email,'.$id,
            'company_name' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'password' => 'sometimes|required|string|min:8|confirmed',
            //'apps' => "required|array|min:1",
            //'apps.*' => "in:$appsString"
        ],
        [],
        ['apps' => 'Portali']
        )->validate();

        $authUser = auth()->user();

        $data['user_id'] = $authUser->id;
        $data['name'] = ($data['first_name'] ?? $user->first_name).' '.($data['last_name'] ?? $user->last_name);
        $data['app'] = null;
        if (!empty($data['apps'])){
            $data['app'] = implode(',', $data['apps']);
        } 

        //password
        if(!empty($data['password'])){
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        if ($authUser->getAccessLevel() === User::ROLE_LEVEL_SUPER_ADMIN) {
            //admin resourece
            $userResource = UserProfileResource::make($user);
        } else {
            $userResource = UserResource::make($user);
        }

        return $this->responseSuccess([
            'user' => $userResource
        ]);
    }

    /**
     * Update user password
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request, $id)
    {
        $user = User::find($id);
        if(!$user){
            return $this->responseNotFound();
        }
        
        $data = $request->all();
        Validator::make($data, [
            'password' => 'sometimes|required|string|min:8|confirmed',
            ])->validate();

        $authUser = auth()->user();      
        $data['user_id'] = $authUser->id;
        //password
        if(!empty($data['password'])){
            $data['password'] = Hash::make($data['password']);
        }
        
        $user->update($data);
        return $this->responseSuccess();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->responseNotFound();
        }

        $user->delete();
        return $this->responseSuccess();
    }

    /**
     * Get user roles
     *
     * @return \Illuminate\Http\Response
     */
    public function getRoles()
    {
        return $this->responseSuccess([
            'roles' => User::getRoles()
        ]);
    }
  
}
