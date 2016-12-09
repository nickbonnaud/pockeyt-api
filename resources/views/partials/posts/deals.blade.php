<div v-if="deals.length > 0">
  <div v-for="deal in deals" class="box box-default">
    <div class="box-header with-border">
     <p class="pull-right">Expires on: <b>@{{ deal->end_date }}</b></p>
      <h3 class="box-title" ><a href="#" data-toggle="modal" data-target="#dealModal">{{ str_limit(@{{ deal->message }}, 85) }}</a></h3>
    <i class="fa fa-calendar pull-right"></i>
    </div>
    <div class="box-body">
      <img :src="@{{ deal->photo_path}}">
      <hr>
      <i>This post is redeemable for $@{{ deal->price }}</i>
      @if($signedIn && ($isAdmin || $user->profile->owns(@{{ deal }})))
        @include('partials.posts.delete')
      @endif
    </div>
  </div>
</div>
