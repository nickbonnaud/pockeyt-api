@extends('layout')


@section('content')

    <div class="row">
        <div class="col-md-12">

            <h1>All Posts</h1>
            
            <hr>

            <div class="alert alert-info">
                Only posts from approved profiles will be shown here.
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            @include('partials.posts.list')

        </div>
    </div>


@stop