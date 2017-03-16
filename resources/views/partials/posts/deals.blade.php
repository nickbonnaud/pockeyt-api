@if(count($posts) > 0)
  @foreach($posts as $post)
    <div class="box box-default">
      <div class="box-header with-border">
       <p class="pull-right">Expires on: <b>{{ $post->end_date }}</b></p>
        <h3 class="box-title" v-on:click="getPurchasedDeals({{ $post->id }})"><a href="#" data-toggle="modal" data-target="#dealModal">{{ str_limit($post->message, 85) }}</a></h3>
      <i class="fa fa-calendar pull-right"></i>
      </div>
      <div class="box-body">
        @if(! is_null($post->photo_path))
            <img src="{{ $post->photo_path}}">
            <hr>
        @endif
        <i>This post is redeemable for ${{ $post->price / 100 }}</i>
        @if($signedIn && ($isAdmin || $user->profile->owns($post)))
          @include('partials.posts.delete')
        @endif
      </div>
    </div>
  @endforeach
@endif