<?php

namespace App\Models;

use App\Http\Resources\Plan\FreeTrialResource;
use App\Http\Resources\Plan\SubscriptionResource;
use App\Models\Plan\FreeTrial;
use App\Models\Plan\Subscription;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable;

	/**
	 * Sanctum auth token name
	 */
	const AUTH_TOKEN = 'eko-auth';
	/**
	 * App super admin user
	 */
	const ROLE_SUPER_ADMIN = 'super_admin';
	/**
	 * App admin user
	 */
	const ROLE_ADMIN = 'admin';
	/**
	 * App admin user
	 */
	const ROLE_MANAGER = 'manager';
	/**
	 * App editor user
	 */
	const ROLE_EDITOR = 'editor';
	/**
	 * App author user
	 */
	const ROLE_AUTHOR = 'author';
	/**
	 * Regular user
	 */
	const ROLE_USER = 'user';

	const ROLE_LEVEL_SUPER_ADMIN = 5;
	const ROLE_LEVEL_ADMIN = 4;
	//const ROLE_LEVEL_MANAGER = 3;
	const ROLE_LEVEL_EDITOR = 2;
	const ROLE_LEVEL_AUTHOR = 1;
	const ROLE_LEVEL_USER = 0;


	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'name',
		'first_name',
		'last_name',
		'email',
		'password',
		'user_id',
		'role',
		'active',
		'note'
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	/**
	 * Getter. Returns subscription or free_trial if exists
	 *
	 * @return Subscription|FreeTrial|null
	 */
	/* public function getAccountPlanAttribute()
	{
		if ($this->subscriptions()->exists()) {
			return $this->lastSubscription;
		} else if ($this->freeTrial()->exists()) {
			return $this->freeTrial;
		}
		return null;
	} */

	/**
	 * Get last subscription by app name
	 *
	 * @param string $app
	 * @return Subscription
	 */
	function getLastSubsription($app) {
		return $this->subscriptions()->where('app', $app)->latest()->first();
	}
	
	/**
	 * Get last subscription by app name
	 *
	 * @param string $app
	 * @return Subscription
	 */
	function getLastFreeTrial($app) {
		return $this->freeTrials()->where('app', $app)->latest()->first();
	}
	
	public function hasActivePlan($app)
	{
		if ($this->hasAuthorAccess()) {
			return true;
		}
		$lastSubscription = $this->getLastSubsription($app);
		if ($lastSubscription) {
			return $lastSubscription->isActive();
		} else {
			$freeTrial = $this->getLastFreeTrial($app);
			if ($freeTrial) {
				return $freeTrial->isActive();
			}
			return false;
		}
		return false;
	}

	public function getAccessLevel()
	{
		return self::getRoleAccessLevel($this->role);
	}

	/**
	 * check if user has author access level
	 *
	 * @return boolean
	 */
	public function hasAuthorAccess(): bool {
		return self::getRoleAccessLevel($this->role) >= self::ROLE_LEVEL_AUTHOR;
	}
	
	/**
	 * check if user has editor access level
	 *
	 * @return boolean
	 */
	public function hasEditorAccess(): bool {
		return self::getRoleAccessLevel($this->role) >= self::ROLE_LEVEL_EDITOR;
	}
	/**
	 * check if user has admin access level
	 *
	 * @return boolean
	 */
	public function hasAdminAccess(): bool {
		return self::getRoleAccessLevel($this->role) >= self::ROLE_LEVEL_ADMIN;
	}
	/**
	 * check if user has super admin access level
	 *
	 * @return boolean
	 */
	public function hasSuperAdminAccess(): bool {
		return self::getRoleAccessLevel($this->role) >= self::ROLE_LEVEL_SUPER_ADMIN;
	}

	/**
	 * @param $roleName
	 * @return int
	 */
	public static function getRoleAccessLevel($roleName)
	{
		switch ($roleName) {
			case self::ROLE_SUPER_ADMIN:
				return self::ROLE_LEVEL_SUPER_ADMIN;
				break;
			case self::ROLE_ADMIN:
				return self::ROLE_LEVEL_ADMIN;
				break;
			case self::ROLE_EDITOR:
				return self::ROLE_LEVEL_EDITOR;
				break;
			case self::ROLE_AUTHOR:
				return self::ROLE_LEVEL_AUTHOR;
				break;
			case self::ROLE_PAID:
			case self::ROLE_FREE_TRIAL:
				return self::ROLE_LEVEL_USER;
				break;

			default:
				return 0;
				break;
		}
	}

	protected function roleName(): Attribute
    {
        return Attribute::make(
            get: function () {
				if ($this->role === self::ROLE_SUPER_ADMIN) {
					return 'Super Admin';
				}
				else if ($this->role === self::ROLE_ADMIN) {
					return 'Admin';
				}
				else if ($this->role === self::ROLE_EDITOR) {
					return 'Editor';
				}
				else if ($this->role === self::ROLE_AUTHOR) {
					return 'Autor';
				}
				else if ($this->role === self::ROLE_USER) {
					return 'Korisnik';
				}
				return $this->role;
			}
        );
    }

	public static function getRoles()
	{
		$roles = [
			//['name' => 'Menadžer', 'value' => self::ROLE_MANAGER],
			['name' => 'Editor', 'value' => self::ROLE_EDITOR],
			['name' => 'Autor', 'value' => self::ROLE_AUTHOR],
			//['name' => 'Korisnik', 'value' => self::ROLE_USER]
		];
		$user = auth()->user();
		if ($user->hasAdminAccess()) {
			array_unshift($roles, ['name' => 'Admin', 'value' => self::ROLE_ADMIN]);
		}
		if ($user->hasSuperAdminAccess()) {
			array_unshift($roles, ['name' => 'Super admin', 'value' => self::ROLE_SUPER_ADMIN]);
		}
		return $roles;
	}

	/**
	 * Subscriptions
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\	public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany

	 */
	public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(Subscription::class);
	}
	
	/**
	 * Last Subscription
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function lastSubscription(): \Illuminate\Database\Eloquent\Relations\HasOne
	{
		return $this->HasOne(Subscription::class)->orderBy('id', 'DESC');
	}

	/**
	 * Free trial
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function freeTrials(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(FreeTrial::class);
	}

	/**
	 * Last free trial
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function lastFreeTrial(): \Illuminate\Database\Eloquent\Relations\HasOne
	{
		return $this->HasOne(FreeTrial::class)->orderBy('id', 'DESC');
	}
}
