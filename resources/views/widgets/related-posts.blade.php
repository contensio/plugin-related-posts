@if($title)
<h3 class="contensio-widget-title">{{ $title }}</h3>
@endif

@if($posts->isNotEmpty())
<ul class="related-posts-list">
    @foreach($posts as $post)
        @php $t = $post->translations->first(); @endphp
        @if($t)
        <li class="related-posts-item">
            <a href="{{ route('contensio.post', $t->slug) }}" class="related-posts-link">
                {{ $t->title }}
            </a>
        </li>
        @endif
    @endforeach
</ul>
@else
<p class="text-sm text-gray-400 italic">{{ $note ?? '' }}</p>
@endif
