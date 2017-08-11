<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript">
        (function(document,navigator,standalone) {
            // prevents links from apps from oppening in mobile safari
            // this javascript must be the first script in your <head>
            if ((standalone in navigator) && navigator[standalone]) {
                var curnode, location=document.location, stop=/^(a|html)$/i;
                document.addEventListener('click', function(e) {
                    curnode=e.target;
                    while (!(stop).test(curnode.nodeName)) {
                        curnode=curnode.parentNode;
                    }
                    // Condidions to do this only on links to your own app
                    // if you want all links, use if('href' in curnode) instead.
                    if(
                        'href' in curnode && // is a link
                        (chref=curnode.href).replace(location.href,'').indexOf('#') && // is not an anchor
                        (   !(/^[a-z\+\.\-]+:/i).test(chref) ||                       // either does not have a proper scheme (relative links)
                            chref.indexOf(location.protocol+'//'+location.host)===0 ) // or is in the same protocol and domain
                    ) {
                        e.preventDefault();
                        location.href = curnode.href;
                    }
                },false);
            }
        })(document,window.navigator,'standalone');
    </script>
    <meta name="apple-mobile-web-app-title" content="Pockeyt Business">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>Pockeyt Business</title>
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/libs.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/jqueryui/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/select2/select2.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.css">
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/icon" />
    <link rel="apple-touch-startup-image" href="/images/launch.png">
</head>

<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('app.index') }}">
                <img src="{{ asset('/images/pockeyt-logo.png') }}" class="logo">
                @if($isAdmin)
                    <span class="text-primary">(Admin)</span>
                @endif
            </a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                @if ($signedIn)
                    @if($hasProfile)
                        <li><a href="{{ route('profiles.show', ['profiles' => Crypt::encrypt($user->profile->id)]) }}">My Profile</a></li>
                    @elseif(!$isAdmin)
                        <li><a href="{{ route('profiles.create') }}">Create Profile</a></li>
                    @endif

                    @if($isAdmin)
                        <li><a href="{{ route('profiles.index') }}">All Profiles</a></li>
                        <li><a href="{{ route('posts.index') }}">All Posts</a></li>
                        <li><a href="{{ route('blogs.create') }}">Create Blog</a></li>
                        <li><a href="{{ route('blogs.index') }}">All Blogs</a></li>
                    @endif
                    <li><a href="{{ route('auth.logout') }}">Logout</a></li>
                @else 
                    <li><a href="{{ route('password.email') }}">Forgot Password?</a></li>
                @endif
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="wrapper">
    <div class="container">
        @yield('content')
    </div>
</div>

<footer>
    <p>Made in Raleigh, NC</p>
    <p>Mentorship from endUp</p>
</footer>

<script src="{{ asset('/vendor/jquery/jquery-1.12.0.min.js') }}"></script>
<script src="{{ asset('/vendor/bootstrap/js/bootstrap.js') }}"></script>
<script src="{{ asset('/js/libs.js') }}"></script>
<script src="{{ asset('/vendor/jqueryui/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('/vendor/select2/select2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.0.1/vue.js"></script>
<script src="{{ asset('/vendor/vMask/v-mask.min.js') }}"></script>
<script src="{{ asset('/vendor/vMask/v-money.js') }}"></script>
@yield('scripts.footer')
@include('flash')
<style>
    html { display:none; }
</style>
<script>
    
    if (self == top) {
        document.documentElement.style.display = 'block'; 
    } else {
        top.location = self.location;
    }


</script>
</body>
</html>