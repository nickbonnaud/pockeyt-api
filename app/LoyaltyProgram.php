<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;

class LoyaltyProgram extends Model {
    /**
     * Fillable fields for a Product
     *
     * @var array
     */
    protected $fillable = [
    	'profile_id',
    	'is_increment',
    	'purchases_required',
    	'amount_required',
        'reward'
    ];

    public static function boot() {
        parent::boot();

        static::created(function(LoyaltyProgram $loyaltyProgram) {
            $loyaltyProgram->profile->touch();
        });
    }

    /**
     * A Loyalty Program belongs to its profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile() {
        return $this->belongsTo('App\Profile');
    }
}