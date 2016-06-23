@extends('layout')

@section('content')

{!! Form::open(['route' => 'blogs.store', 'files' => true]) !!}
  @include('errors.form')
  @include('blogs.form')
{!! Form::close() !!}

@stop