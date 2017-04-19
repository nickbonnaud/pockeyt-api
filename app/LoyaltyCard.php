<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;

class LoyaltyCard extends Model {
   
    protected $fillable = [
    	'user_id',
    	'program_id',
    	'current_amount',
    	'rewards_achieved'
    ];

    public static function boot() {
        parent::boot();

        static::created(function(LoyaltyCard $loyaltyCard) {
            $loyaltyCard->user->touch();
        });
    }

    /**
     * A Loyalty Card belongs to its user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('App\User');
    }
}