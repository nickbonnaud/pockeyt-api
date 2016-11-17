<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;

class Product extends Model {
    /**
     * Fillable fields for a Product
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'price',
        'description',
        'sku',
        'product_photo_path',
    ];

    public static function boot() {
        parent::boot();

        static::created(function(Product $product) {
            $product->profile->touch();
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