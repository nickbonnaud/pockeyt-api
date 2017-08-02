@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <h1>Register</h1>

            <hr>

            <form method="POST" action="{{ route('auth.register') }}">
                {!! csrf_field() !!}

                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name') }}" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name') }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}"
                           required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <p :class="{ 'control': true }"></p>
                        <input v-validate="'required|confirmed:password_confirmation|min:9|max:72|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%=]).*$/'" :class="{'input': true, 'is-danger': errors.has('password') }" name="password" type="password" required>
                        <span v-show="errors.has('password')" class="help is-danger">{{ errors.first('password') }}</span>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password:</label>
                    <input name="password_confirmation" :class="{'input': true, 'is-danger': errors.has('password') }" type="password" 
                           required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-info pull-right">Next</button>
                </div>
            </form>

            @include ('errors.form')
        </div>
    </div>
@stop