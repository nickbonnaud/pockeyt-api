@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <form method="POST" action="/password/email">
                {!! csrf_field() !!}

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}"
                           required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-warning">Send Reset Link</button>
                </div>
            </form>
            @include ('errors.form')
        </div>
    </div>
@stop