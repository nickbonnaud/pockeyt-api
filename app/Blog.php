<?php

namespace App;

use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'author',
        'description',
        'blog_title',
        'blog_body',
        'blog_hero_name',
        'blog_hero_url',
        'blog_profile_name',
        'blog_profile_url',
        'published_at'
    ];

    protected $dates = ['published_at'];
    protected $appends = ['formatted_body'];


     public function setPublishedAtAttribute($date) {
        $this->attributes['published_at'] = Carbon::parse($date, new DateTimeZone(\Config::get('app.timezone')));
    }

    public function getFormattedBodyAttribute() {
        return html_newlines_to_p($this->blog_body);
    }
}







