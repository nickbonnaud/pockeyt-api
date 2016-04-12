<div class="profile-list-item-status text-center alert {{ $profile->approved ? 'alert-success' : 'alert-danger'}}">
    <p>This profile is <strong>{{ $profile->approved ? 'approved' : 'not approved'}}</strong></p>
    <form action="{{ route('profiles.' . ($profile->approved ? 'unapprove' : 'approve'), ['profiles' => $profile->id]) }}" method="post">
        {{ csrf_field() }}
        <input type="submit" class="btn btn-primary btn-sm profile-list-item-status-button" value="{{ $profile->approved ? 'Un-approve' : 'Approve'}}">
    </form>
</div>