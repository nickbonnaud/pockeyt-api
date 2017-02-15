<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushId extends Model
{

	/**
	 * Fillable fields for a tag
	 * @var array
	 */
	protected $fillable = [
		'profile_id',
		'device_type',
		'push_token'
	];
}
