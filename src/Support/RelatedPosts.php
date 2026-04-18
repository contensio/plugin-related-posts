<?php

namespace Contensio\Plugins\RelatedPosts\Support;

use Contensio\Models\Content;
use Illuminate\Support\Collection;

class RelatedPosts
{
    /**
     * Return up to $limit published posts of the same content type,
     * ranked by the number of shared terms with $content.
     *
     * Falls back to most-recent same-type posts when $content has no terms.
     *
     * @return Collection<int, Content>  Each item has ->translations and ->featuredImage eager-loaded.
     */
    public static function for(Content $content, int $limit = 3, ?int $langId = null): Collection
    {
        $termIds = $content->terms()->pluck('terms.id')->toArray();

        $with = [
            'translations' => fn ($q) => $q->where('language_id', $langId),
            'featuredImage',
        ];

        // If the post has terms, rank by shared term count
        if (! empty($termIds)) {
            return Content::select('contents.*')
                ->selectRaw('COUNT(DISTINCT content_terms.term_id) AS shared_terms')
                ->join('content_terms', 'contents.id', '=', 'content_terms.content_id')
                ->whereIn('content_terms.term_id', $termIds)
                ->where('contents.id', '!=', $content->id)
                ->where('contents.status', Content::STATUS_PUBLISHED)
                ->where('contents.content_type_id', $content->content_type_id)
                ->groupBy('contents.id')
                ->with($with)
                ->orderByDesc('shared_terms')
                ->orderByDesc('published_at')
                ->limit($limit)
                ->get();
        }

        // No terms — fall back to most recent of the same type
        return Content::where('status', Content::STATUS_PUBLISHED)
            ->where('id', '!=', $content->id)
            ->where('content_type_id', $content->content_type_id)
            ->with($with)
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }
}
