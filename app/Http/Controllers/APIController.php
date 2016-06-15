<?php


namespace App\Http\Controllers;

use App\Post;
use App\Profile;
use App\Tags;
use App\Http\Requests;
use Illuminate\Http\Request;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

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
                    $paginator = Post::where('event_date', '=', $today)->visible()->with([])->latest()->paginate(2);
                    break;

                case "tomorrow":
                    $tomorrow = date('Y-m-d', strtotime('tomorrow'));
                    $paginator = Post::where('event_date', '=', $tomorrow)->visible()->with([])->latest()->paginate(2);
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
                    $paginator = Post::whereIn('event_date', $days)->visible()->with([])->paginate(2);
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
                    $paginator = Post::whereIn('event_date', $days)->visible()->with([])->paginate(2);
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
}