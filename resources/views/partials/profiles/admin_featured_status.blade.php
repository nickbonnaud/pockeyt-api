<div class="profile-list-item-status text-center alert alert-{{ $profile->featured ? 'info' : 'warning'}}">
    <p>This profile is <strong>{{ $profile->featured ? 'featured' : 'not featured'}}</strong></p>
    <form action="{{ route('profiles.' . ($profile->featured ? 'unfeature' : 'feature'), ['profiles' => $profile->id]) }}" method="post">
        {{ csrf_field() }}
        <input type="submit" class="btn btn-primary btn-sm profile-list-item-status-button" value="{{ $profile->featured ? 'Un-feature' : 'Feature'}}">
    </form>
</div>