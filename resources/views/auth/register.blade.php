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

                <input v-validate="'confirmed:pw_confirm'" :class="{'input': true, 'is-danger': errors.has('confirm_field') }" name="confirm_field" type="password" placeholder="Enter The Password">
<span v-show="errors.has('confirm_field')" class="help is-danger">@{{ errors.first('confirm_field') }}</span>
<input name="pw_confirm" :class="{'input': true, 'is-danger': errors.has('confirm_field') }" type="password" placeholder="Confirm the password">

                <div class="form-group">
                    <button type="submit" class="btn btn-info pull-right">Next</button>
                </div>
            </form>

            @include ('errors.form')
        </div>
    </div>
@stop