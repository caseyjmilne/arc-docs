<?php
/**
 * ARC Docs Main Template
 * Description: Display a list of documentation
 */

use ARC\Docs\Models\Doc;

get_header();
?>

<div class="docs-page-wrapper">
    <div class="container">
        <header class="docs-header">
            <h1>Documentation</h1>
            <p>Browse our documentation library</p>
        </header>

        <?php
        // Get search query if exists
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        ?>

        <!-- Search Form -->
        <div class="docs-search">
            <form method="get" action="">
                <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Search documentation...">
                <button type="submit">Search</button>
            </form>
        </div>

        <?php
        // Get published docs
        if (!empty($search)) {
            // Search mode
            $docs = Doc::published()
                ->where(function($query) use ($search) {
                    $query->where('title', 'LIKE', "%{$search}%")
                          ->orWhere('content', 'LIKE', "%{$search}%")
                          ->orWhere('excerpt', 'LIKE', "%{$search}%");
                })
                ->orderBy('created_at', 'desc')
                ->get();
            
            echo '<p class="search-results">Found ' . $docs->count() . ' result(s) for "' . esc_html($search) . '"</p>';
        } else {
            // Normal mode - all published docs
            $docs = Doc::published()
                ->orderBy('created_at', 'desc')
                ->get();
        }
        ?>

        <?php if ($docs->count() > 0): ?>
            <div class="docs-list">
                <?php foreach ($docs as $doc): ?>
                    <article class="doc-item">
                        <h2 class="doc-title">
                            <a href="<?php echo esc_url($doc->permalink); ?>">
                                <?php echo esc_html($doc->title); ?>
                            </a>
                        </h2>
                        
                        <div class="doc-meta">
                            <span class="doc-author">
                                By <?php echo esc_html($doc->author_name); ?>
                            </span>
                            <span class="doc-date">
                                <?php echo $doc->created_at->format('F j, Y'); ?>
                            </span>
                            <span class="doc-updated">
                                Last updated: <?php echo $doc->updated_at->diffForHumans(); ?>
                            </span>
                        </div>

                        <?php if ($doc->excerpt): ?>
                            <div class="doc-excerpt">
                                <?php echo wp_kses_post($doc->excerpt); ?>
                            </div>
                        <?php endif; ?>

                        <a href="<?php echo esc_url($doc->permalink); ?>" class="doc-read-more">
                            Read More →
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-docs">
                <p>No documentation found. <?php if ($search): ?><a href="<?php echo esc_url(get_permalink()); ?>">View all docs</a><?php endif; ?></p>
            </div>
        <?php endif; ?>

        <!-- Stats Section -->
        <div class="docs-stats">
            <div class="stat-item">
                <strong><?php echo Doc::published()->count(); ?></strong>
                <span>Published Docs</span>
            </div>
            <div class="stat-item">
                <strong><?php echo Doc::count(); ?></strong>
                <span>Total Docs</span>
            </div>
        </div>
    </div>
</div>

<style>
.docs-page-wrapper {
    padding: 40px 20px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

.docs-header {
    text-align: center;
    margin-bottom: 40px;
}

.docs-header h1 {
    font-size: 2.5em;
    margin-bottom: 10px;
}

.docs-search {
    margin-bottom: 30px;
    text-align: center;
}

.docs-search input[type="text"] {
    padding: 10px 15px;
    width: 100%;
    max-width: 500px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

.docs-search button {
    padding: 10px 30px;
    background: #0073aa;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    margin-left: 10px;
}

.docs-search button:hover {
    background: #005177;
}

.search-results {
    margin-bottom: 20px;
    font-style: italic;
    color: #666;
}

.docs-list {
    display: grid;
    gap: 30px;
}

.doc-item {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 25px;
    background: white;
    transition: box-shadow 0.3s;
}

.doc-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.doc-title {
    margin: 0 0 15px 0;
    font-size: 1.8em;
}

.doc-title a {
    color: #0073aa;
    text-decoration: none;
}

.doc-title a:hover {
    color: #005177;
}

.doc-meta {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
    font-size: 0.9em;
    color: #666;
}

.doc-meta span {
    display: flex;
    align-items: center;
}

.doc-excerpt {
    margin-bottom: 15px;
    color: #333;
    line-height: 1.6;
}

.doc-read-more {
    color: #0073aa;
    text-decoration: none;
    font-weight: 600;
}

.doc-read-more:hover {
    text-decoration: underline;
}

.no-docs {
    text-align: center;
    padding: 40px;
    background: #f5f5f5;
    border-radius: 8px;
}

.docs-stats {
    display: flex;
    gap: 30px;
    justify-content: center;
    margin-top: 50px;
    padding-top: 30px;
    border-top: 1px solid #e0e0e0;
}

.stat-item {
    text-align: center;
}

.stat-item strong {
    display: block;
    font-size: 2em;
    color: #0073aa;
    margin-bottom: 5px;
}

.stat-item span {
    color: #666;
    font-size: 0.9em;
}
</style>

<?php
get_footer();
?>