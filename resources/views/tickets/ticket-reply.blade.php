<hr>
<strong>{{ $reply->user->name }} </strong>
<span>
    @if($reply->parent_id==0)
        conversation started
    @else
        replied
    @endif

    {{ \Carbon\Carbon::parse($reply->updated_at)->diffForHumans() }}
</span>
<span>
    ({{ $reply->updated_at }})
</span>
<p>{{ $reply->body }}</p>
