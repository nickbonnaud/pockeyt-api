<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;

class Transaction extends Model {
    /**
     * Fillable fields for a Product
     *
     * @var array
     */
    protected $fillable = [
    	'profile_id',
    	'user_id',
    	'paid',
    	'products',
    	'total'
    ];

    public static function boot() {
        parent::boot();

        static::created(function(Transaction $transaction) {
            $transaction->profile->touch();
        });
    }

    /**
     * A Product belongs to its profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile() {
        return $this->belongsTo('App\Profile');
    }
}