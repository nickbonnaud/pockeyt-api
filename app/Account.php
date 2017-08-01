<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
  protected $fillable = [
  	'legalBizName',
    'businessType',
    'bizTaxId',
    'established',
    'annualCCSales',
    'bizStreetAdress',
    'bizCity',
    'bizZip',
    'bizState',
    'phone',
    'accountEmail',
    'accountUserFirst',
  	'accountUserLast',
  	'dateOfBirth',
    'ownership',
    'indivStreetAdress',
    'indivCity',
    'indivZip',
    'indivState',
    'ownerEmail',
  	'ssn',
    'method',
  	'accountNumber',
  	'routing',
    'status'
  ];

  public function profile() {
    return $this->belongsTo('App\Profile');
 	}

  public function ownedBy(Profile $profile) {
    return $this->profile_id == $profile->id;
  }
}
