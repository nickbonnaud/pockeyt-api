<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;

class Invite extends Model {
    
    protected $fillable = [
    	'user_id',
    	'business_id',
    	'invite_code',
    	'used',
    	'date_used'
    ];

    public static function boot() {
      parent::boot();

      static::created(function(Invite $invite) {
        $invite->user->touch();
      });
    }

    public function user() {
      return $this->belongsTo('App\User');
    }
}