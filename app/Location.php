<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;

class Location extends Model {
    /**
     * Fillable fields for a Product
     *
     * @var array
     */
    protected $fillable = [ 'location_id' ];

    public static function boot() {
        parent::boot();

        static::created(function(Location $location) {
            $location->user->touch();
        });
    }

    /**
     * A Product belongs to its profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('App\User');
    }
}