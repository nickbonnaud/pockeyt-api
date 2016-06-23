<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Blog;
use App\Photo;
use DateTimeZone;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Requests\BlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Http\Requests\EditBlogRequest;
use App\Http\Requests\DeleteBlogRequest;

use App\Http\Controllers\Controller;

class BlogsController extends Controller
{

    public function __construct() {
        $this->middleware('auth', ['except' => ['show']]);
        $this->middleware('auth:admin', ['only' => ['index', 'create']]);

        parent::__construct();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogs = Blog::with([])->latest('published_at')->get();

        return view('blogs.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('blogs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BlogRequest $request)
    {
        $blog = new Blog($request->all());
        $file = $request->blog_hero;
        $photo = Photo::fromForm($file);
        $photo->save();
        $blog['blog_hero_name'] = $photo->name;
        $blog['blog_hero_url'] = url($photo->path);

        $file = $request->blog_profile;
        $photo = Photo::fromForm($file);
        $photo->save();
        $blog['blog_profile_name'] = $photo->name;
        $blog['blog_profile_url'] = url($photo->path);

        $blog['published_at'] = Carbon::now(new DateTimeZone(config('app.timezone')));

        $blog->save();
        return redirect()->route('blogs.show', ['blogs' => $blog->id]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $blog = Blog::with([])->find($id);
        return view('blogs.show', compact('blog'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EditBlogRequest $request, $id)
    {
        $blog = Blog::findOrFail($id);
        return view('blogs.edit', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBlogRequest $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $blog->update($request->all());
        return redirect()->route('blogs.show', ['blogs' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteBlogRequest $request, $id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();
        return redirect()->route('blogs.index');
    }
}
