<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
  protected $fillable = [
  	'accountUserFirst',
  	'accountUserLast',
  	'accountEmail',
  	'dateOfBirth',
  	'last4',
  	'indivStreetAdress',
  	'indivCity',
  	'indivZip',
  	'indivState',
  	'legalBizName',
  	'bizTaxId',
  	'bizStreetAdress',
  	'bizCity',
  	'bizZip',
  	'bizState',
  	'accountNumber4',
  	'routingNumber4',
    'status'
  ];

  public function profile() {
    return $this->belongsTo('App\Profile');
 	}
}
