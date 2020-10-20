<hr>
<strong>{{ $reply->user->name }} </strong>
<span>
    @if($reply->title)
        started conversation
    @else
        replied
    @endif

    {{ \Carbon\Carbon::parse($reply->updated_at)->diffForHumans() }}
</span>
<span>
    ({{ $reply->updated_at }})
</span>
<p>{{ $reply->body }}</p>
