<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddProfilePhotoRequest;
use App\Http\Requests\DeleteProfilePhotoRequest;
use App\Http\Requests\EditProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateBusinessLocationRequest;
use App\Http\Requests\UpdateBusinessTagsRequest;
use App\Photo;
use App\Post;
use App\Profile;
use App\Tax;
use App\GeoLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\AddPhotoRequest;

class ProfilesController extends Controller {

    /**
     * Create a new ProfilesController instance
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('auth:admin', ['only' => ['index', 'postApprove', 'postUnapprove', 'postFeature', 'postUnfeature']]);

        parent::__construct();
    }

    /**************************
     * Resource actions
     */

    public function index() {
        $profiles = Profile::with(['owner', 'posts', 'logo', 'hero'])->latest()->get();

        return view('profiles.index', compact('profiles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        if(!is_null($this->user->profile))
            return redirect()->route('profiles.show', ['profiles' => $this->user->profile->id]);

        $tags = \App\Tag::lists('name', 'id');
        return view('profiles.create', compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProfileRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProfileRequest $request) {
        dd($request);
        if(!is_null($this->user->profile))
            return redirect()->route('profiles.show', ['profiles' => $this->user->profile->id]);

        $profile = $this->user->publish(
            new Profile($request->except(['lat', 'lng', 'county', 'state']))
        );

        $county = $request->county;
        $state = $request->state;

        $taxLocation = Tax::where(function($query) use ($county, $state) {
            $query->where('county', '=', $county)
                ->where('state', '=', $state);
        })->first();

        $profile->tax_rate = $taxLocation->county_tax + $taxLocation->state_tax;
        $profile->save();

        $geoLocation = new GeoLocation;
        $geoLocation->identifier = $request->business_name;
        $geoLocation->latitude = $request->lat;
        $geoLocation->longitude = $request->lng;

        $profile->geoLocation()->save($geoLocation);

        if(is_null($this->user->profile))
            $this->syncTags($profile, $request->input('tags'));
        else
            $this->syncTags($profile, $request->input('tag_list'));

        return redirect()->route('accounts.create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $profile = (!is_null($this->user) && $this->user->is_admin) ? Profile::find($id) : Profile::visible()->find($id);

        return view('profiles.show', compact('profile'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EditProfileRequest $request, $id) {
        $profile = Profile::findOrFail($id);
        $tags = \App\Tag::lists('name', 'id');
        return view('profiles.edit', compact('profile', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $request, $id) {
        /** @var Profile $profile */
        $profile = Profile::findOrFail($id);
        $profile->update($request->all());
        $tagList = $request->input('tag_list');

        if (isset($tag_list)) {
            $this->syncTags($profile, $tag_list);
        }

        return redirect()->route('profiles.edit', compact('profile', 'tags'));
    }

    /**************************
     * Other actions
     */

    public function changeTags(UpdateBusinessTagsRequest $request, $id) {
        /** @var Profile $profile */
        $profile = Profile::findOrFail($id);
        $this->syncTags($profile, $request->input('tag_list'));

        return redirect()->route('profiles.edit', compact('profile', 'tags'));
    }
    

    public function changeLocation(UpdateBusinessLocationRequest $request, $id) {
        /** @var Profile $profile */
        $profile = Profile::findOrFail($id);
        
        $county = $request->county;
        $state = $request->state;

        $taxLocation = Tax::where(function($query) use ($county, $state) {
            $query->where('county', '=', $county)
                ->where('state', '=', $state);
        })->first();

        $profile->tax_rate = $taxLocation->county_tax + $taxLocation->state_tax;
        $profile->save();

        $geoLocation = $this->user->profile->geoLocation;
        $geoLocation->latitude = $request->lat;
        $geoLocation->longitude = $request->lng;
        $geoLocation->identifier = $profile->business_name;
        $geoLocation->save();

        return redirect()->route('profiles.edit', compact('profile', 'tags'));
    }

    public function postPhotos(AddProfilePhotoRequest $request, $profile_id) {
        $file = $request->file('photo');
        $photo = Photo::fromForm($file);
        $photo->save();
        Profile::findOrFail($profile_id)->{$request->get('type')}()->associate($photo)->save();
        return response('ok');
    }

    public function deletePhotos(DeleteProfilePhotoRequest $request, $profile_id) {
        /** @var Profile $profile */
        $profile = Profile::findOrFail($profile_id);
        $type = $request->get('type');
        $photo = $profile->{$type};
        $profile->{$type}()->dissociate()->save();
        $photo->delete();
        return back();
    }

    public function postApprove($profile_id) {
        /** @var Profile $profile */
        $profile = Profile::findOrFail($profile_id);
        $profile->approved = true;
        $profile->save();
        return redirect()->to(\URL::previous() . '#profile-' . $profile->id);
    }

    public function postUnapprove($profile_id) {
        /** @var Profile $profile */
        $profile = Profile::findOrFail($profile_id);
        $profile->approved = false;
        $profile->save();
        return redirect()->to(\URL::previous() . '#profile-' . $profile->id);
    }

    public function postFeature($profile_id) {
        /** @var Profile $profile */
        $profile = Profile::findOrFail($profile_id);
        $profile->featured = true;
        $profile->save();
        return redirect()->to(\URL::previous() . '#profile-' . $profile->id);
    }

    public function postUnfeature($profile_id) {
        /** @var Profile $profile */
        $profile = Profile::findOrFail($profile_id);
        $profile->featured = false;
        $profile->save();
        return redirect()->to(\URL::previous() . '#profile-' . $profile->id);
    }

    /**
     * Sync up the list of tags in the database
     * @param Profile $profile
     * @param array $tags
     */
    private function syncTags(Profile $profile, array $tags)
    {
        $profile->tags()->sync($tags);      
    }

}
