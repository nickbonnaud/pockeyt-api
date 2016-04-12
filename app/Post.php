<?php

namespace App;

use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Builder;

class Post extends Model {
    /**
     * Fillable fields for a photo
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'body',
        'published_at'
    ];

    /**
     * an instance of the $date
     */
    protected $dates = ['published_at'];

    protected $appends = ['formatted_body'];

    public static function boot() {
        parent::boot();

        static::created(function(Post $post) {
            $post->profile->touch();
        });
    }

    /**
     * Sets the published at attribute
     *
     * @param mixed $date
     */
    public function setPublishedAtAttribute($date) {
        $this->attributes['published_at'] = Carbon::parse($date, new DateTimeZone(\Config::get('app.timezone')));
    }

    public function setBodyAttribute($body) {
        $this->attributes['body'] = clean_newlines($body);
    }

    /**
     * A post belongs to its profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile() {
        return $this->belongsTo('App\Profile');
    }

    public function getFormattedBodyAttribute() {
        return html_newlines_to_p($this->body);
    }

    /**
     * @param Builder|\Illuminate\Database\Eloquent\Builder|Model $query
     * @return mixed
     */
    public function scopeVisible($query) {
        return $query->whereHas('profile', function($query) {
            /** @var Profile $query */
            return $query->visible();
        });
    }
}