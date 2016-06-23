@extends('layout')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <h1>Update Blog Post</h1>

            <hr>

            <div class="col-md-6">
                {!! Form::model($blog, ['method' => 'PATCH', 'route' => ['blogs.update', $blog->id], 'files' => true]) !!}
                    @include ('blogs.form')
                {!! Form::close() !!}
            </div>
        </div>

    </div>
@stop