@extends('layout')

@section('content')
    <div class="row" id="main">
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
                        <input class="form-control" v-validate="{
                            rules: 
                                { 
                                    regex: /^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%=]).*$/,
                                    required: true,
                                    confirmed: 'password_confirmation',
                                    min: 9,
                                    max: 72, 
                                }
                            }"
                            :class="{'input': true, 'is-danger': errors.has('password') }" name="password" type="password" required
                        />
                        <span v-show="errors.has('password')" class="help is-danger">@{{ errors.first('password') }}</span>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password:</label>
                    <input class="form-control" name="password_confirmation" :class="{'input': true, 'is-danger': errors.has('password') }" type="password" 
                           required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-info pull-right">Next</button>
                </div>
            </form>

            @include ('errors.form')
        </div>
    </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vee-validate@latest/dist/vee-validate.js"></script>
<script>
    Vue.use(VeeValidate);
    var main = new Vue({
        el: '#main'
    });
</script>
@stop