<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class Account extends Model {

    /**
     * Fillable fields for a account
     *
     * @var
     */
    protected $fillable = [
        'user_photo_id',
    ];

    public function toDetailedArray() {
        $data = array_only($this->toArray(), ['id', 'created_at']);
        $data['user_photo'] = is_null($this->user_photo) ? '' : $this->user_photo->url;

        return $data;
    }

    public function userPhoto() {
        return $this->belongsTo('App\Photo', 'user_photo_id');
    }

    /**
     * An account is owned by a user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner() {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * Determine if the given user created the account
     * @param User $user
     * @return boolean
     */
    public function ownedBy(User $user) {
        return $this->user_id == $user->id;
    }

    /**
     * method to check if account owns object
     *
     * @param var passed from view
     * @return boolean
     */
    public function owns($relation) {
        return $relation->account_id == $this->id;
    }
}
