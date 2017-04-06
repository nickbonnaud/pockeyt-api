<?php


namespace App\Http\Controllers;

use App\Post;
use App\Blog;
use App\Profile;
use App\Tags;
use Carbon\Carbon;
use DateTimeZone;
use App\Http\Requests;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Http\Controllers\Controller;

class APIController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->middleware('cors');
    }

    public function getPosts() {
        return response()->json(Post::visible()->with([])->latest()->get());
    }

    public function getPost($id) {
        $post = Post::visible()->with(['profile'])->find($id);
        if(is_null($post)) {
            return response()->json(['error' => 'Post not found.'], 404);
        } else {
            return response()->json($post);
        }
    }

    public function getProfiles() {
        $profiles = Profile::approved()->with(['logo', 'hero', 'posts', 'tags'])->orderBy('business_name', 'ASC')->get()->map(function(Profile $profile) {
            return $profile->toDetailedArray();
        });
        return response()->json($profiles);
    }

    public function getProfile($id) {
        /** @var Profile $profile */
        $profile = Profile::approved()->with(['logo', 'hero', 'posts', 'tags'])->find($id);
        if(is_null($profile)) {
            return response()->json(['error' => 'Profile not found.'], 404);
        } else {
            return response()->json($profile->toDetailedArray());
        }
    }

    public function getProfilesv1()
    {
        $paginator = Profile::approved()->orderBy('business_name', 'ASC')->paginate(10);
        $profiles = $paginator->getCollection();

        return fractal()
            ->collection($profiles, function(Profile $profile) {
                    return [
                        'id' => (int) $profile->id,
                        'business_name' => $profile->business_name,
                        'website' => $profile->website,
                        'description' => $profile->description,
                        'review_url' => $profile->review_url,
                        'review_intro' => $profile->review_intro,
                        'formatted_description' => $profile->formatted_description,
                        'created_at' => $profile->created_at,
                        'updated_at' => $profile->updated_at,
                        'posts' => $profile->posts->reverse()->take(10),
                        'tags' => $profile->tags,
                        'featured' => $profile->featured,
                        'logo_thumbnail' => is_null($profile->logo) ? '' : $profile->logo->thumbnail_url,
                        'logo' =>  is_null($profile->logo) ? '' : $profile->logo->url,
                        'hero_thumbnail' => is_null($profile->hero) ? '' : $profile->hero->thumbnail_url,
                        'hero' => is_null($profile->hero) ? '' : $profile->hero->url,
                    ];
                })
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->toArray();
    }

    public function getPostsv1() {
        $paginator = Post::visible()->with([])->latest()->paginate(10);
        $posts = $paginator->getCollection();
        return fractal()
            ->collection($posts, function(Post $post) {
                    return [
                        'post_id' => (int) $post->id,
                        'title' => $post->title,
                        'body' => $post->body,
                        'thumbnail_url' => $post->thumb_path,
                        'photo_url' => $post->photo_path,
                        'published_at' => $post->published_at,
                        'id' => $post->profile->id,
                        'business_name' => $post->profile->business_name,
                        'website' => $post->profile->website,
                        'description' => $post->profile->description,
                        'review_url' => $post->profile->review_url,
                        'review_intro' => $post->profile->review_intro,
                        'formatted_description' => $post->profile->formatted_description,
                        'posts' => $post->profile->posts->reverse()->take(10),
                        'tags' => $post->profile->tags,
                        'featured' => $post->profile->featured,
                        'logo_thumbnail' => is_null($post->profile->logo) ? '' : $post->profile->logo->thumbnail_url,
                        'logo' =>  is_null($post->profile->logo) ? '' : $post->profile->logo->url,
                        'hero_thumbnail' => is_null($post->profile->hero) ? '' : $post->profile->hero->thumbnail_url,
                        'hero' => is_null($post->profile->hero) ? '' : $post->profile->hero->url,
                    ];
            })
        ->paginateWith(new IlluminatePaginatorAdapter($paginator))
        ->toArray();
    }

    public function getFavs(Request $request) {
        if ($request->has('profiles')) {
            $input = $request->all();
            $profiles = $input['profiles'];
            $profiles = explode(',', $profiles);

            $paginator = Post::whereIn('profile_id', $profiles)->visible()->with([])->latest()->paginate(10);
            $paginator->appends($input)->render();
            $posts = $paginator->getCollection();
            return fractal()
            ->collection($posts, function(Post $post) {
                    return [
                        'post_id' => (int) $post->id,
                        'title' => $post->title,
                        'body' => $post->body,
                        'thumbnail_url' => $post->thumb_path,
                        'photo_url' => $post->photo_path,
                        'published_at' => $post->published_at,
                        'id' => $post->profile->id,
                        'business_name' => $post->profile->business_name,
                        'website' => $post->profile->website,
                        'description' => $post->profile->description,
                        'review_url' => $post->profile->review_url,
                        'review_intro' => $post->profile->review_intro,
                        'formatted_description' => $post->profile->formatted_description,
                        'posts' => $post->profile->posts->reverse()->take(10),
                        'tags' => $post->profile->tags,
                        'featured' => $post->profile->featured,
                        'logo_thumbnail' => is_null($post->profile->logo) ? '' : $post->profile->logo->thumbnail_url,
                        'logo' =>  is_null($post->profile->logo) ? '' : $post->profile->logo->url,
                        'hero_thumbnail' => is_null($post->profile->hero) ? '' : $post->profile->hero->thumbnail_url,
                        'hero' => is_null($post->profile->hero) ? '' : $post->profile->hero->url,
                    ];
            })
        ->paginateWith(new IlluminatePaginatorAdapter($paginator))
        ->toArray();
        }
    }

    public function getSearch(Request $request) {
        if ($request->has('input')) {
            $search = $request->all();
            $input = $search['input'];

            $paginator = Profile::approved()->where('business_name','LIKE', "%$input%")
            ->orWhereHas('tags', function ($q) use ($input) {
                $q->where('name', 'LIKE', "%$input%");
            })
            ->orderBy('business_name', 'ASC')->paginate(10);

            $paginator->appends($search)->render();
            $profiles = $paginator->getCollection();

            return fractal()
                ->collection($profiles, function(Profile $profile) {
                        return [
                            'id' => (int) $profile->id,
                            'business_name' => $profile->business_name,
                            'website' => $profile->website,
                            'description' => $profile->description,
                            'review_url' => $profile->review_url,
                            'review_intro' => $profile->review_intro,
                            'formatted_description' => $profile->formatted_description,
                            'created_at' => $profile->created_at,
                            'updated_at' => $profile->updated_at,
                            'posts' => $profile->posts->reverse()->take(10),
                            'tags' => $profile->tags,
                            'featured' => $profile->featured,
                            'logo_thumbnail' => is_null($profile->logo) ? '' : $profile->logo->thumbnail_url,
                            'logo' =>  is_null($profile->logo) ? '' : $profile->logo->url,
                            'hero_thumbnail' => is_null($profile->hero) ? '' : $profile->hero->thumbnail_url,
                            'hero' => is_null($profile->hero) ? '' : $profile->hero->url,
                        ];
                    })
                ->paginateWith(new IlluminatePaginatorAdapter($paginator))
                ->toArray();
        }
    }

    public function getEvents(Request $request) {
        if ($request->has('calendar')) {
            $events = $request->all();
            $calendar = $events['calendar'];

            switch ($calendar) {
                case 'today':
                    $today = date("Y-m-d");
                    $paginator = Post::where('event_date', '=', $today)->visible()->with([])->latest()->paginate(10);
                    break;

                case "tomorrow":
                    $tomorrow = date('Y-m-d', strtotime('tomorrow'));
                    $paginator = Post::where('event_date', '=', $tomorrow)->visible()->with([])->latest()->paginate(10);
                    break;

                case "week":
                    $days = [];
                    $i = 0;
                    $date = date("Y-m-d");
                    $endDate = date('Y-m-d', strtotime('saturday'));
                    while (strtotime($date) <= strtotime($endDate)) {
                        $days = array_add($days, $i, $date);
                        $i = $i + 1;
                        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                    }
                    $paginator = Post::whereIn('event_date', $days)->orderBy('event_date', 'asc')->visible()->with([])->paginate(10);
                    break;

                case "weekend":
                    $days = [];
                    $i = 0;
                    $date = date('Y-m-d', strtotime('friday'));
                    $endDate = date('Y-m-d', strtotime('sunday'));
                    while (strtotime($date) <= strtotime($endDate)) {
                        $days = array_add($days, $i, $date);
                        $i = $i + 1;
                        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                    }
                    $paginator = Post::whereIn('event_date', $days)->orderBy('event_date', 'asc')->visible()->with([])->paginate(10);
                    break;
            }

            $paginator->appends($events)->render();
            $posts = $paginator->getCollection();
            return fractal()
            ->collection($posts, function(Post $post) {
                    return [
                        'post_id' => (int) $post->id,
                        'title' => $post->title,
                        'body' => $post->body,
                        'thumbnail_url' => $post->thumb_path,
                        'photo_url' => $post->photo_path,
                        'published_at' => $post->published_at,
                        'event_date' => $post->event_date,
                        'id' => $post->profile->id,
                        'business_name' => $post->profile->business_name,
                        'website' => $post->profile->website,
                        'description' => $post->profile->description,
                        'review_url' => $post->profile->review_url,
                        'review_intro' => $post->profile->review_intro,
                        'formatted_description' => $post->profile->formatted_description,
                        'posts' => $post->profile->posts->reverse()->take(10),
                        'tags' => $post->profile->tags,
                        'featured' => $post->profile->featured,
                        'logo_thumbnail' => is_null($post->profile->logo) ? '' : $post->profile->logo->thumbnail_url,
                        'logo' =>  is_null($post->profile->logo) ? '' : $post->profile->logo->url,
                        'hero_thumbnail' => is_null($post->profile->hero) ? '' : $post->profile->hero->thumbnail_url,
                        'hero' => is_null($post->profile->hero) ? '' : $post->profile->hero->url,
                    ];
            })
                ->paginateWith(new IlluminatePaginatorAdapter($paginator))
                ->toArray();
        }
    }

    public function getBlogs() {
        $paginator = Blog::with([])->latest()->paginate(10);
        $blogs = $paginator->getCollection();
        return fractal()
            ->collection($blogs, function(Blog $blog) {
                    return [
                        'blog_id' => (int) $blog->id,
                        'author' => $blog->author,
                        'description' => $blog->description,
                        'blog_title' => $blog->blog_title,
                        'blog_body' => $blog->blog_body,
                        'blog_hero_name' => $blog->blog_hero_name,
                        'blog_hero_url' => $blog->blog_hero_url,
                        'blog_profile_name' => $blog->blog_profile_name,
                        'blog_profile_url' => $blog->blog_profile_url,
                        'published_at' => $blog->published_at,
                        'blog_formatted_body' => $blog->formatted_body
                    ];
            })
        ->paginateWith(new IlluminatePaginatorAdapter($paginator))
        ->toArray();
    }

    public function getBookmarks(Request $request) {
        if ($request->has('posts')) {
            $input = $request->all();
            $posts = $input['posts'];
            $posts = explode(',', $posts);

            $paginator = Post::whereIn('id', $posts)->visible()->with([])->latest()->paginate(10);
            $paginator->appends($input)->render();
            $posts = $paginator->getCollection();
            return fractal()
            ->collection($posts, function(Post $post) {
                    return [
                        'post_id' => (int) $post->id,
                        'title' => $post->title,
                        'body' => $post->body,
                        'thumbnail_url' => $post->thumb_path,
                        'photo_url' => $post->photo_path,
                        'published_at' => $post->published_at,
                        'id' => $post->profile->id,
                        'business_name' => $post->profile->business_name,
                        'website' => $post->profile->website,
                        'description' => $post->profile->description,
                        'review_url' => $post->profile->review_url,
                        'review_intro' => $post->profile->review_intro,
                        'formatted_description' => $post->profile->formatted_description,
                        'posts' => $post->profile->posts->reverse()->take(10),
                        'tags' => $post->profile->tags,
                        'featured' => $post->profile->featured,
                        'logo_thumbnail' => is_null($post->profile->logo) ? '' : $post->profile->logo->thumbnail_url,
                        'logo' =>  is_null($post->profile->logo) ? '' : $post->profile->logo->url,
                        'hero_thumbnail' => is_null($post->profile->hero) ? '' : $post->profile->hero->thumbnail_url,
                        'hero' => is_null($post->profile->hero) ? '' : $post->profile->hero->url,
                    ];
            })
        ->paginateWith(new IlluminatePaginatorAdapter($paginator))
        ->toArray();
        }
    }

//////////////////////////////v2//////////////////////////////////

    public function getProfilesV2()
    {
        $paginator = Profile::approved()->orderBy('business_name', 'ASC')->paginate(10);
        $profiles = $paginator->getCollection();

        return fractal()
            ->collection($profiles, function(Profile $profile) {
                    return [
                        'profile_id' => (int) $profile->id,
                        'business_name' => $profile->business_name,
                        'tags' => $profile->tags,
                        'logo' =>  is_null($profile->logo) ? '' : $profile->logo->url,
                        'website' => $profile->website,
                        'formatted_description' => $profile->formatted_description,
                        'hero' => is_null($profile->hero) ? '' : $profile->hero->url,
                    ];
                })
            ->paginateWith(new IlluminatePaginatorAdapter($paginator))
            ->toArray();
    }


    public function getPostsV2() {
        $paginator = Post::visible()->with([])->latest()->paginate(10);
        $posts = $paginator->getCollection();
        return fractal()
            ->collection($posts, function(Post $post) {
                    return [
                        'id' => (int) $post->id,
                        'profile_id' => $post->profile_id,
                        'business_name' => $post->profile->business_name,
                        'message' => $post->message,
                        'photo_url' => $post->photo_path,
                        'published_at' => $post->published_at,
                        'event_date' => $post->event_date,
                        'is_redeemable' => $post->is_redeemable,
                        'deal_item' => $post->deal_item,
                        'price' => $post->price,
                        'end_date' => $post->end_date,
                        'tags' => $post->profile->tags,
                        'logo' =>  is_null($post->profile->logo) ? '' : $post->profile->logo->url,
                        'website' => $post->profile->website,
                        'formatted_description' => $post->profile->formatted_description,
                        'hero' => is_null($post->profile->hero) ? '' : $post->profile->hero->url,
                    ];
            })
        ->paginateWith(new IlluminatePaginatorAdapter($paginator))
        ->toArray();
    }

    public function getFavsV2(Request $request) {
        if ($request->has('profiles')) {
            $input = $request->all();
            $profiles = $input['profiles'];
            $profiles = explode(',', $profiles);

            $paginator = Post::whereIn('profile_id', $profiles)->visible()->with([])->latest()->paginate(10);
            $paginator->appends($input)->render();
            $posts = $paginator->getCollection();
            return fractal()
            ->collection($posts, function(Post $post) {
                    return [
                        'id' => (int) $post->id,
                        'profile_id' => $post->profile_id,
                        'business_name' => $post->profile->business_name,
                        'message' => $post->message,
                        'photo_url' => $post->photo_path,
                        'published_at' => $post->published_at,
                        'event_date' => $post->event_date,
                        'is_redeemable' => $post->is_redeemable,
                        'deal_item' => $post->deal_item,
                        'price' => $post->price,
                        'end_date' => $post->end_date,
                        'tags' => $post->profile->tags,
                        'logo' =>  is_null($post->profile->logo) ? '' : $post->profile->logo->url,
                        'website' => $post->profile->website,
                        'formatted_description' => $post->profile->formatted_description,
                        'hero' => is_null($post->profile->hero) ? '' : $post->profile->hero->url,
                    ];
            })
        ->paginateWith(new IlluminatePaginatorAdapter($paginator))
        ->toArray();
        }
    }

    public function getSearchV2(Request $request) {
        if ($request->has('input')) {
            $search = $request->all();
            $input = $search['input'];

            $paginator = Profile::approved()
                ->where('business_name','LIKE', "%$input%")
                ->orWhereHas('tags', function ($q) use ($input) {
                $q->where('name', 'LIKE', "%$input%");
            })
            ->orderBy('business_name', 'ASC')->paginate(10);

            $paginator->appends($search)->render();
            $profiles = $paginator->getCollection();

            return fractal()
                ->collection($profiles, function(Profile $profile) {
                        return [
                        'profile_id' => (int) $profile->id,
                        'business_name' => $profile->business_name,
                        'tags' => $profile->tags,
                        'logo' =>  is_null($profile->logo) ? '' : $profile->logo->url,
                        'website' => $profile->website,
                        'formatted_description' => $profile->formatted_description,
                        'hero' => is_null($profile->hero) ? '' : $profile->hero->url,
                        ];
                    })
                ->paginateWith(new IlluminatePaginatorAdapter($paginator))
                ->toArray();
        }
    }

    public function getEventsV2(Request $request) {
        if ($request->has('calendar')) {
            $events = $request->all();
            $calendar = $events['calendar'];

            switch ($calendar) {
                case 'today':
                    $today = date("Y-m-d");
                    $paginator = Post::where('event_date', '=', $today)->visible()->with([])->latest()->paginate(10);
                    break;

                case "tomorrow":
                    $tomorrow = date('Y-m-d', strtotime('tomorrow'));
                    $paginator = Post::where('event_date', '=', $tomorrow)->visible()->with([])->latest()->paginate(10);
                    break;

                case "week":
                    $days = [];
                    $i = 0;
                    $date = date("Y-m-d");
                    $endDate = date('Y-m-d', strtotime('saturday'));
                    while (strtotime($date) <= strtotime($endDate)) {
                        $days = array_add($days, $i, $date);
                        $i = $i + 1;
                        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                    }
                    $paginator = Post::whereIn('event_date', $days)->orderBy('event_date', 'asc')->visible()->with([])->paginate(10);
                    break;

                case "weekend":
                    $days = [];
                    $i = 0;
                    $date = date('Y-m-d', strtotime('friday'));
                    $endDate = date('Y-m-d', strtotime('sunday'));
                    while (strtotime($date) <= strtotime($endDate)) {
                        $days = array_add($days, $i, $date);
                        $i = $i + 1;
                        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                    }
                    $paginator = Post::whereIn('event_date', $days)->orderBy('event_date', 'asc')->visible()->with([])->paginate(10);
                    break;
            }

            $paginator->appends($events)->render();
            $posts = $paginator->getCollection();
            return fractal()
            ->collection($posts, function(Post $post) {
                    return [
                        'id' => (int) $post->id,
                        'profile_id' => $post->profile_id,
                        'business_name' => $post->profile->business_name,
                        'title' => $post->title,
                        'body' => $post->body,
                        'photo_url' => $post->photo_path,
                        'published_at' => $post->published_at,
                        'event_date' => $post->event_date,
                        'is_redeemable' => $post->is_redeemable,
                        'logo' =>  is_null($post->profile->logo) ? '' : $post->profile->logo->url,
                    ];
            })
                ->paginateWith(new IlluminatePaginatorAdapter($paginator))
                ->toArray();
        }
    }

    public function getBookmarksV2(Request $request) {
        if ($request->has('posts')) {
            $input = $request->all();
            $posts = $input['posts'];
            $posts = explode(',', $posts);

            $paginator = Post::whereIn('id', $posts)->visible()->with([])->latest()->paginate(10);
            $paginator->appends($input)->render();
            $posts = $paginator->getCollection();
            return fractal()
            ->collection($posts, function(Post $post) {
                    return [
                        'id' => (int) $post->id,
                        'profile_id' => $post->profile_id,
                        'business_name' => $post->profile->business_name,
                        'message' => $post->message,
                        'title' => $post->title,
                        'body' => $post->body,
                        'photo_url' => $post->photo_path,
                        'published_at' => $post->published_at,
                        'event_date' => $post->event_date,
                        'is_redeemable' => $post->is_redeemable,
                        'deal_item' => $post->deal_item,
                        'price' => $post->price,
                        'end_date' => $post->end_date,
                        'tags' => $post->profile->tags,
                        'logo' =>  is_null($post->profile->logo) ? '' : $post->profile->logo->url,
                        'website' => $post->profile->website,
                        'formatted_description' => $post->profile->formatted_description,
                        'hero' => is_null($post->profile->hero) ? '' : $post->profile->hero->url,
                    ];
            })
        ->paginateWith(new IlluminatePaginatorAdapter($paginator))
        ->toArray();
        }
    }

    public function getBusinessPostsV2(Request $request) {
        if ($request->has('profile')) {
            $input = $request->all();
            $profile = $input['profile'];
            $profile = explode(',', $profile);

            $paginator = Post::where('profile_id', '=', $profile)->visible()->with([])->latest()->paginate(10);
            $paginator->appends($input)->render();
            $posts = $paginator->getCollection();
            return fractal()
            ->collection($posts, function(Post $post) {
                    return [
                        'id' => (int) $post->id,
                        'profile_id' => $post->profile_id,
                        'business_name' => $post->profile->business_name,
                        'message' => $post->message,
                        'photo_url' => $post->photo_path,
                        'published_at' => $post->published_at,
                        'event_date' => $post->event_date,
                        'is_redeemable' => $post->is_redeemable,
                        'deal_item' => $post->deal_item,
                        'price' => $post->price,
                        'end_date' => $post->end_date,
                        'logo' =>  is_null($post->profile->logo) ? '' : $post->profile->logo->url,
                    ];
            })
        ->paginateWith(new IlluminatePaginatorAdapter($paginator))
        ->toArray();
        }
    }

}