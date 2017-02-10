<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;

class GeoLocation extends Model {
  /**
   * Fillable fields for a Product
   *
   * @var array
   */
  protected $fillable = [
  	'identifier',
  	'profile_id',
  	'latitude',
  	'longitude'
  ];

  public static function boot() {
      parent::boot();

      static::created(function(GeoLocation $geoLocation) {
          $geoLocation->profile->touch();
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