<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;

class PostAnalytics extends Model {
   
    protected $fillable = [
    	'user_id',
    	'business_id',
    	'post_id',
    	'viewed',
    	'viewed_on',
    	'shared',
    	'shared_on',
    	'bookmarked',
    	'bookmarked_on'
    ];

    public static function boot() {
        parent::boot();

        static::created(function(PostAnalytics $postAnalytics) {
            $postAnalytics->user->touch();
        });
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}