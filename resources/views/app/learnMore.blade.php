<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pockeyt Business</title>
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/libs.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/jqueryui/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.css">
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2-rc.1/css/select2.min.css"  />
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
                        <li><a href="{{ route('profiles.show', ['profiles' => $user->profile->id]) }}">My Profile</a></li>
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
  <div class="container" style="background: #ecf0f5; border-radius: 20px; padding: 50px;">
		<div class="learn-top">
			<h3>Automatically sync your Pockeyt transactions with QuickBooks.</h3>
		</div>
		<div class="row">
			<div class="learn-middle">
				<div class="col-md-6">
					<p>Keep your QuickBooks account current and up-to-date with Pockeyt Sync.</p>
					<img src="{{ asset('/images/sync-image.png') }}">
					<h4>Features:</h4>
					<ul>
						<li>Syncs every transaction on Pockeyt to your QuickBooks Account.</li>
						<li>Every transaction synced creates an invoice and closes the invoice once payment confirmed.</li>
						<li>Pockeyt easily tracks sales, tips, and taxes and automatically syncs them with your QuickBooks account for every transaction.</li>
						<li>Seamless and automatic once connected!</li>
					</ul>
				</div>
				<div class="col-md-6">
					<img style="margin-top: 50px;" src="{{ asset('/images/qboConnectScreen.png') }}">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="learn-bottom">
				<h3 style="margin-top: 20px;">To connect to your Pockeyt account, login to the <a href="{{ route('app.index') }}" target="_blank">Pockeyt Dashboard</a> and click Connect to QuickBooks in the Payment Account Info sub-tab of Your Business Info tab.</h3>
				<a href="{{ route('app.index') }}" target="_blank">
					<button class="btn btn-primary" style="background-color: #337ab7; border-color: #2e6da4;">Create Account</button>
				</a>
			</div>
		</div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2-rc.1/js/select2.min.js"></script>
@yield('scripts.footer')

@include('flash')
</body>
</html>
