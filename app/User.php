<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract {

    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'password', 'photo_path', 'role'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'is_admin'];

    protected $casts = [
        'is_admin' => 'boolean'
    ];

    /**
     * method to check if user owns object
     *
     * @param var passed from view
     * @return boolean
     */
    public function owns($relation) {
        return $relation->user_id == $this->id;
    }

    /**
     * A user has one profile
     * @return HasOne
     */
    public function profile() {
        return $this->hasOne(Profile::class);
    }

    public function locations() {
        return $this->hasMany('App\Location');
    }

    public function loyaltyCards() {
        return $this->hasMany('App\LoyaltyCard');
    }

    public function postAnalytics() {
        return $this->hasMany('App\PostAnalytic');
    }

    public function invites() {
        return $this->hasMany('App\Invite');
    }

    /**
     * current user saves and associates with profile
     *
     * @param Profile $profile
     * @return Profile
     */
    public function publish(Profile $profile) {
        return $this->profile()->save($profile);
    }
}
