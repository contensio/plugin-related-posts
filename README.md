# Related Posts

Shows a grid of related posts below every post, ranked by the number of shared terms (tags and categories). Falls back to the most recent posts of the same content type when the current post has no terms assigned.

No database, no admin UI, no configuration required.

---

## Requirements

- Contensio 2.0 or later

---

## Installation

### Composer

```bash
composer require contensio/plugin-related-posts
```

### Manual

Copy the plugin directory and register the service provider via the admin plugin manager.

No migrations required.

---

## How it works

### Relevance ranking

The plugin queries the `content_terms` pivot table to count how many terms each candidate post shares with the current post:

```sql
SELECT contents.*, COUNT(DISTINCT content_terms.term_id) AS shared_terms
FROM contents
JOIN content_terms ON contents.id = content_terms.content_id
WHERE content_terms.term_id IN (/* current post's term IDs */)
  AND contents.id != /* current post id */
  AND contents.status = 'published'
  AND contents.content_type_id = /* same type */
GROUP BY contents.id
ORDER BY shared_terms DESC, published_at DESC
LIMIT 3
```

Posts sharing more terms rank higher. Ties are broken by publish date (newest first).

### Fallback

If the current post has no terms, the plugin falls back to the 3 most recently published posts of the same content type.

### Hook placement

Hooks into `contensio/frontend/post-after-content` at **priority 15**:

| Priority | Plugin |
|----------|--------|
| 5 | Author Box |
| 10 | Social Share *(default)* |
| 15 | **Related Posts** |

---

## Customising

### Number of posts

Edit the `RelatedPostsServiceProvider` and pass a different `$limit`:

```php
$related = RelatedPosts::for($content, 5, $langId);  // show 5 instead of 3
```

### Blade view

Override in your theme:

```
resources/views/vendor/related-posts/partials/related-posts.blade.php
```

Available variables: `$related` (Collection of Content models with `->translations` and `->featuredImage` eager-loaded).

### Using `RelatedPosts::for()` directly

```php
use Contensio\Plugins\RelatedPosts\Support\RelatedPosts;

$related = RelatedPosts::for($content, limit: 4, langId: $lang->id);

foreach ($related as $post) {
    $translation = $post->translations->first();
    echo $translation->title . ' - ' . $post->shared_terms . ' shared terms';
}
```

The `shared_terms` attribute is appended by the query (absent in fallback mode).

---

## Widget

The plugin also registers a `related-posts` widget type in the admin widget builder. Because widget areas don't have single-post context, the widget renders a static note rather than a live list - the inline hook version is the primary display mechanism.

---

## Hook reference

| Hook | Type | Args | Priority |
|------|------|------|----------|
| `contensio/frontend/post-after-content` | Render | `Content, ContentTranslation` | 15 |
