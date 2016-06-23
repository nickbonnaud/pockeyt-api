@extends('layout')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <h1>Published Blog Posts</h1>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            @include('partials.blogs.list')

        </div>
    </div>


@stop