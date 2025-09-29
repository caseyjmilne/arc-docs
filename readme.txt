# ARC Docs Plugin

A document management system built on the ARC Framework, demonstrating the power of combining ARC Forge (Eloquent ORM) and ARC Gateway (Collection Registry) for rapid WordPress development.

## Features

- **Eloquent-powered documents** with full ORM capabilities
- **Automatic slug generation** from titles
- **Author tracking** integrated with WordPress users
- **Published/Draft workflow** with convenience methods
- **Full-text search** across title and content
- **Query scopes** for common filtering operations
- **Custom accessors** for author info and permalinks
- **REST API ready** through ARC Gateway integration

## Requirements

- WordPress 5.0+
- PHP 7.4+
- **ARC Forge** - Eloquent ORM integration
- **ARC Gateway** - Collection registry system

## Installation

1. Install required ARC plugins (Forge and Gateway)
2. Install ARC Docs plugin
3. Activate the plugin
4. Database table is created automatically

## Database Schema

The plugin creates a `wp_docs` table with the following structure:

| Column | Type | Description |
|--------|------|-------------|
| id | bigint(20) | Primary key |
| title | varchar(255) | Document title |
| slug | varchar(255) | URL-friendly slug (unique) |
| content | longtext | Document content |
| excerpt | text | Short description |
| status | varchar(20) | published or draft |
| author_id | bigint(20) | WordPress user ID |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

## Usage

### Creating Documents

```php
use ARC\Gateway\Collection;
use ARC\Docs\Models\Doc;

// Using helper function
$doc = arc_create_doc([
    'title' => 'Getting Started with ARC',
    'content' => 'This is a comprehensive guide...',
    'excerpt' => 'Learn the basics of ARC Framework',
    'status' => 'published'
]);

// Using Collection directly
$doc = Collection::get('docs')->create([
    'title' => 'Advanced Topics',
    'slug' => 'advanced-topics',
    'content' => 'Deep dive into...',
    'status' => 'draft',
    'author_id' => 1
]);

// Using Model directly
$doc = Doc::create([
    'title' => 'API Reference',
    'content' => 'Complete API documentation...',
    'status' => 'published'
]);
```

### Reading Documents

```php
// Get all docs
$allDocs = arc_docs()->all();

// Get specific doc by ID
$doc = arc_get_doc(1);
$doc = Collection::get('docs')->find(1);
$doc = Doc::find(1);

// Get published docs only
$publishedDocs = arc_docs()->query()->published()->get();
$publishedDocs = Doc::published()->get();

// Search docs
$results = arc_docs()->search('ARC framework', ['title', 'content']);

// Filter by status
$drafts = arc_docs()->filter(['status' => 'draft'])->get();
$drafts = Doc::draft()->get();

// Filter by author
$myDocs = Doc::byAuthor(get_current_user_id())->get();

// Sort docs
$sorted = arc_docs()->sort('created_at', 'desc')->get();
$sorted = Doc::orderBy('title', 'asc')->get();
```

### Updating Documents

```php
// Using collection
$doc = arc_docs()->update(1, [
    'title' => 'Updated Title',
    'content' => 'Updated content...'
]);

// Using model
$doc = Doc::find(1);
$doc->title = 'New Title';
$doc->content = 'New content...';
$doc->save();

// Publish/unpublish
$doc = Doc::find(1);
$doc->publish();   // Sets status to 'published'
$doc->unpublish(); // Sets status to 'draft'
```

### Deleting Documents

```php
// Using collection
arc_docs()->delete(1);

// Using model
$doc = Doc::find(1);
$doc->delete();
```

### Advanced Queries

```php
// Complex query with multiple conditions
$docs = Doc::where('status', 'published')
    ->where('author_id', 1)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

// Using query scopes
$recentPublished = Doc::published()
    ->orderBy('created_at', 'desc')
    ->take(5)
    ->get();

// Count documents
$totalDocs = Doc::count();
$publishedCount = Doc::published()->count();
$draftCount = Doc::draft()->count();
```

### Working with Attributes

```php
$doc = Doc::find(1);

// Get author information
$author = $doc->getAuthor();          // Returns WP_User object
$authorName = $doc->author_name;       // Uses accessor

// Get permalink
$url = $doc->permalink;                // Uses accessor

// Check status
if ($doc->isPublished()) {
    echo "Doc is live!";
}

if ($doc->isDraft()) {
    echo "Still working on it...";
}
```

## Display Examples

### Simple Docs List

```php
$docs = Doc::published()->orderBy('created_at', 'desc')->get();

foreach ($docs as $doc) {
    echo '<article>';
    echo '<h2><a href="' . esc_url($doc->permalink) . '">' . esc_html($doc->title) . '</a></h2>';
    echo '<p class="meta">By ' . esc_html($doc->author_name) . ' on ' . $doc->created_at->format('F j, Y') . '</p>';
    echo '<div class="excerpt">' . wp_kses_post($doc->excerpt) . '</div>';
    echo '</article>';
}
```

### User's Draft Dashboard

```php
$myDrafts = Doc::draft()
    ->byAuthor(get_current_user_id())
    ->orderBy('updated_at', 'desc')
    ->get();

foreach ($myDrafts as $draft) {
    echo '<div class="draft-item">';
    echo '<h3>' . esc_html($draft->title) . '</h3>';
    echo '<p>Last updated: ' . $draft->updated_at->diffForHumans() . '</p>';
    echo '<a href="#">Edit</a>';
    echo '</div>';
}
```

## Helper Functions

| Function | Description |
|----------|-------------|
| `arc_docs()` | Get the docs collection instance |
| `arc_get_doc($id)` | Get a specific doc by ID |
| `arc_create_doc($attributes)` | Create a new doc |

## Model Scopes

| Scope | Description |
|-------|-------------|
| `published()` | Filter only published docs |
| `draft()` | Filter only draft docs |
| `byAuthor($authorId)` | Filter docs by author |

## Model Methods

| Method | Description |
|--------|-------------|
| `getAuthor()` | Get WP_User object for author |
| `isPublished()` | Check if doc is published |
| `isDraft()` | Check if doc is draft |
| `publish()` | Set status to published |
| `unpublish()` | Set status to draft |

## Custom Attributes

| Attribute | Description |
|-----------|-------------|
| `author_name` | Get author's display name |
| `permalink` | Get the doc's URL |

## WordPress Hooks

```php
// After collection is registered
do_action('arc_docs_collection_registered');

// After plugin activation
do_action('arc_docs_activated');

// After plugin deactivation
do_action('arc_docs_deactivated');
```

## Management Examples

### Bulk Publish Drafts

```php
$drafts = Doc::draft()->get();
foreach ($drafts as $draft) {
    $draft->publish();
}
```

### Search and Replace

```php
$docs = Doc::where('content', 'LIKE', '%old-url%')->get();
foreach ($docs as $doc) {
    $doc->content = str_replace('old-url', 'new-url', $doc->content);
    $doc->save();
}
```

### Export to JSON

```php
$docs = Doc::published()->get();
$export = $docs->map(function($doc) {
    return [
        'title' => $doc->title,
        'slug' => $doc->slug,
        'content' => $doc->content,
        'author' => $doc->author_name,
        'date' => $doc->created_at->toDateString()
    ];
});

file_put_contents('docs-export.json', json_encode($export, JSON_PRETTY_PRINT));
```

## Part of the ARC Framework

ARC Docs demonstrates the power of the ARC Framework ecosystem:

- **ARC Forge** - Provides Eloquent ORM integration
- **ARC Gateway** - Provides collection registry and REST API
- **ARC Blueprint** - Field management (optional)
- **ARC Sentinel** - Authentication & authorization (optional)

Together, they enable rapid development of complex WordPress applications with modern PHP patterns.

## Best Practices

1. **Always use mass assignment** with `$fillable` array for security
2. **Leverage query scopes** for reusable filters
3. **Use accessors** for computed attributes
4. **Take advantage of Eloquent relationships** when extending
5. **Use helper functions** for cleaner code in themes/plugins

## Extending the Plugin

### Add Custom Scopes

```php
public function scopeFeatured($query)
{
    return $query->where('is_featured', true);
}
```

### Add Relationships

```php
public function categories()
{
    return $this->belongsToMany(Category::class);
}

public function comments()
{
    return $this->hasMany(Comment::class);
}
```

### Add Custom Methods

```php
public function view()
{
    $this->increment('views');
}

public function duplicate()
{
    $new = $this->replicate();
    $new->slug = $this->slug . '-copy';
    $new->save();
    return $new;
}
```

## License

This plugin is part of the ARC Framework and follows the same license.