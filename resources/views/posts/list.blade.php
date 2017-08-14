@extends('layoutDashboard')

@section('content')
<div class="content-wrapper-scroll">
  <div class="scroll-main">
    <div class="scroll-main-contents">
    	<section class="content-header">
        <h1>
          Add | Recent Posts
        </h1>
          @if(!$user->profile->connected)
            <a href="#" data-toggle="modal" data-target="#connectSocial">
            	<button class="btn pull-right btn-primary">Enable Auto Posting</button>
            </a>
          @else
            @if($user->profile->connected == 'facebook')
              <span class="icon-fb"></span>
              <div class="auto-post">
                <p class="auto-post-text">Auto Post</p>
              </div>
            @else
              <span class="icon-insta"></span>
              <div class="auto-post">
                <p class="auto-post-text">Auto Post</p>
              </div>
            @endif
          @endif
        <ol class="breadcrumb">
          <li><a href="{{ route('profiles.show', ['profiles' => Crypt::encrypt($user->profile->id)]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Posts</li>
        </ol>
      </section>
    	<section class="content">
      	<div class="col-md-6 col-sm-6 col-xs-12">
      		<div class="box box-primary">
      			<div class="box-header with-border">
      				<h3 class="box-title">Create a New Post</h3>
      			</div>
              @include ('errors.form')
      				{!! Form::open(['method' => 'POST', 'route' => ['posts.store'], 'files' => true]) !!}
      				@include('posts.form')
      				{!! Form::close() !!}
      		</div>
      	</div>
        <div class="scroll-container col-md-6 col-sm-6 col-xs-12">
            <div class="scroll-contents">
              @include('partials.posts.list', ['posts' => $posts, 'no_icons' => true])
            </div>
        </div>
    	</section>
    </div>
  </div>
</div>
<div class="modal fade" id="connectSocial" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="connectSocial">Pull posts from social media</h4>
      </div>
      <div class="modal-body">
  			<p>Which website should Pockeyt pull {{$user->profile->business_name}}'s posts and photos from?</p>
      </div>
      <div class="modal-footer">
	      <a href="{{ action('ConnectController@connectFB') }}" class="btn btn-block btn-social btn-facebook">
	      	<i class="fa fa-facebook"></i>
	      	Connect With Facebook
  			</a>
  			<a href="{{ action('ConnectController@connectInsta') }}" class="btn btn-block btn-social btn-instagram">
	      	<i class="fa fa-instagram"></i>
	      	Connect With Instagram
  			</a>
	    </div>
    </div>
  </div>
</div>
@stop