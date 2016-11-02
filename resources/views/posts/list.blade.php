@extends('layoutDashboard')

@section('content')
<div class="content-wrapper">
	<section class="content-header">
    <h1>
      Add | Recent Posts
    </h1>
    <a href="#" data-toggle="modal" data-target="#connectSocial">
    	<h4>Enable Auto Posting</h4>
    </a>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Posts</li>
    </ol>
  </section>
	<section class="content">
	<div class="col-md-6">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Create a New Post</h3>
			</div>
				{!! Form::open(['method' => 'POST', 'route' => ['posts.store'], 'files' => true]) !!}
				@include('posts.form')
				{!! Form::close() !!}
		</div>
	</div>
		@include('partials.posts.list', ['posts' => $posts, 'no_icons' => true])
	</section>
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
	      <a href="{{ action('ConnectController@connectFb') }}" class="btn btn-block btn-social btn-facebook">
	      	<i class="fa fa-facebook"></i>
	      	Connect With Facebook
  			</a>
  			<a href="#" class="btn btn-block btn-social btn-instagram">
	      	<i class="fa fa-instagram"></i>
	      	Connect With Instagram
  			</a>
	    </div>
    </div>
  </div>
</div>

@stop