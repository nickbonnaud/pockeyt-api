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
use App\Http\Controllers\Controller;

class PostsController extends Controller {

    /**
     * Create a new PostsController instance
     */
    public function __construct() {
        parent::__construct();
        $this->middleware('auth', ['only' => 'store']);
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
        $post = Post::visible()->find($id);

        return view('posts.show', compact('post'));
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

        flash()->success('Success', 'Your post has been created!');
        return redirect()->route('profiles.show', ['profiles' => $this->user->profile->id]);
    }

    public function destroy(DeletePostRequest $request, $id) {
        $post = Post::findOrFail($id);
        $profile_path = profile_path($post->profile);
        $post->delete();
        return redirect()->to($profile_path);
    }
}
