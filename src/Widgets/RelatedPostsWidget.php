<?php

namespace Contensio\Plugins\RelatedPosts\Widgets;

use Contensio\Contracts\WidgetInterface;

class RelatedPostsWidget implements WidgetInterface
{
    public function label(): string
    {
        return 'Related Posts';
    }

    public function icon(): string
    {
        return 'bi-collection';
    }

    public function description(): string
    {
        return 'Displays posts related to the current page by shared terms. Falls back to most recent when placed outside a single-post context.';
    }

    public function configSchema(): array
    {
        return [
            'title' => ['type' => 'text',   'label' => 'Widget title',  'default' => 'Related Posts'],
            'limit' => ['type' => 'number', 'label' => 'Number of posts', 'default' => 3],
        ];
    }

    public function render(array $config): string
    {
        // Widget areas don't have direct post context - render a placeholder.
        // The inline hook version is the primary related-posts display.
        return view('contensio-related-posts::widgets.related-posts', [
            'title' => $config['title'] ?? 'Related Posts',
            'posts' => collect(),
            'note'  => 'Related posts are shown automatically below single post content.',
        ])->render();
    }
}
