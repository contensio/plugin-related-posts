<?php

namespace Contensio\Plugins\RelatedPosts;

use Contensio\Models\Content;
use Contensio\Models\ContentTranslation;
use Contensio\Models\Language;
use Contensio\Plugins\RelatedPosts\Support\RelatedPosts;
use Contensio\Plugins\RelatedPosts\Widgets\RelatedPostsWidget;
use Contensio\Support\Hook;
use Contensio\Support\WidgetRegistry;
use Illuminate\Support\ServiceProvider;

class RelatedPostsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'related-posts');

        $this->app->booted(function () {
            WidgetRegistry::register('related-posts', RelatedPostsWidget::class);
        });

        // Inject below post content — priority 15 (after author box at 5, share buttons at 10)
        Hook::add('contensio/frontend/post-after-content', function (Content $content, ContentTranslation $translation): string {
            $langId  = Language::where('is_default', true)->value('id');
            $related = RelatedPosts::for($content, 3, $langId);

            if ($related->isEmpty()) {
                return '';
            }

            return view('related-posts::partials.related-posts', compact('related', 'translation'))->render();
        }, 15);
    }
}
