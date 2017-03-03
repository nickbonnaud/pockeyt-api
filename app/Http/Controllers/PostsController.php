<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeletePostRequest;
use App\Post;
use App\Photo;
use App\Profile;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Database\Eloquent\Collection;
Use Illuminate\HttpResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Requests\EventRequest;
use App\Http\Requests\DealRequest;
use App\Http\Controllers\Controller;

class PostsController extends Controller {

    /**
     * Create a new PostsController instance
     */
    public function __construct() {
        parent::__construct();
        $this->middleware('auth', ['except' => 'show', 'showEvent', ]);
        $this->middleware('auth:admin', ['only' => ['index']]);
    }

    /**
     * Display listing of posts
     *
     * @return Collection
     */
    public function index() {
        $posts = Post::visible()->latest('published_at')->get();

        return view('posts.index', compact('posts'));
    }

    /**
     * Display specified post
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $post = Post::visible()->with(['profile'])->find($id);
        $profile = Profile::approved()->with(['logo'])->find($post->profile_id);
        return view('posts.show', compact('post', 'profile'));
    }

    public function showEvent($id) {
        $post = Post::visible()->with(['profile'])->find($id);
        $profile = Profile::approved()->with(['logo'])->find($post->profile_id);
        return view('posts.event_show', compact('post', 'profile'));
    }

    /**
     * Store new post
     *
     * @param PostRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request) {
        $post = new Post($request->all());
        $file = $request->photo;
        
        if($file != null) {
            $photo = Photo::fromForm($file);
            $photo->save();

            $post['photo_name'] = $photo->name;
            $post['photo_path'] = url($photo->path);
            $post['thumb_path'] = url($photo->thumbnail_path);
        }

        $post['published_at'] = Carbon::now(new DateTimeZone(config('app.timezone')));

        $this->user->profile->posts()->save($post);
        return redirect()->back();
    }

    public function storeEvent(EventRequest $request) {
        $post = new Post($request->all());
        $file = $request->photo;
        
        if($file != null) {
            $photo = Photo::fromForm($file);
            $photo->save();

            $post['photo_name'] = $photo->name;
            $post['photo_path'] = url($photo->path);
            $post['thumb_path'] = url($photo->thumbnail_path);
        }

        $post['published_at'] = Carbon::now(new DateTimeZone(config('app.timezone')));

        $this->user->profile->posts()->save($post);
        return redirect()->back();
    }
    public function storeDeal(DealRequest $request) {
        $post = new Post($request->all());
        $file = $request->photo;
        
        if($file != null) {
            $photo = Photo::fromForm($file);
            $photo->save();

            $post['photo_name'] = $photo->name;
            $post['photo_path'] = url($photo->path);
            $post['thumb_path'] = url($photo->thumbnail_path);
        }

        $post['published_at'] = Carbon::now(new DateTimeZone(config('app.timezone')));
        $post['price'] = $request->price * 100;

        $this->user->profile->posts()->save($post);
        return redirect()->back();
    }

    public function destroy(DeletePostRequest $request, $id) {
        $post = Post::findOrFail($id);
        $post->delete();
        return redirect()->back();
    }

    public function listPosts() {
        $posts = Post::where('profile_id', '=', $this->user->profile->id)->whereNull('event_date')->orderBy('published_at', 'desc')->limit(10)->get();
        return view('posts.list', compact('posts'));
    }

    public function eventPosts() {
        $posts = Post::where('profile_id', '=', $this->user->profile->id)->whereNotNull('event_date')->orderBy('published_at', 'desc')->limit(10)->get();
        return view('posts.events', compact('posts'));
    }

    public function dealPosts() {
        $profile = $this->user->profile;
        $posts = Post::where(function($query) use ($profile) {
            $query->where('profile_id', '=', $profile->id)
            ->where('is_redeemable', '=', true);
        })->orderBy('updated_at', 'desc')->take(5)->get();
        return view('posts.deals', compact('posts'));
    }

}
