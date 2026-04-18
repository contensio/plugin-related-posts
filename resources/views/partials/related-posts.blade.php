<div class="mt-10 pt-8 border-t border-gray-200">
    <h2 class="text-lg font-bold text-gray-900 mb-6">Related Posts</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($related as $post)
            @php
                $postTranslation = $post->translations->first();
                if (! $postTranslation) continue;
                $url = route('contensio.post', $postTranslation->slug);
            @endphp

            <a href="{{ $url }}"
               class="group flex flex-col rounded-xl border border-gray-200 overflow-hidden hover:border-gray-300 hover:shadow-sm transition-all">

                {{-- Featured image --}}
                @if($post->featuredImage)
                    <div class="aspect-video overflow-hidden bg-gray-100">
                        <img src="{{ Storage::disk($post->featuredImage->disk)->url($post->featuredImage->file_path) }}"
                             alt="{{ $postTranslation->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                @else
                    <div class="aspect-video bg-gray-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif

                <div class="flex-1 p-4">
                    <p class="text-xs text-gray-400 mb-1">
                        {{ $post->published_at?->format('M d, Y') }}
                    </p>
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-ember-600 leading-snug transition-colors line-clamp-2">
                        {{ $postTranslation->title }}
                    </h3>
                    @if($postTranslation->excerpt)
                        <p class="mt-1.5 text-xs text-gray-500 line-clamp-2 leading-relaxed">
                            {{ $postTranslation->excerpt }}
                        </p>
                    @endif
                </div>

            </a>
        @endforeach
    </div>
</div>
