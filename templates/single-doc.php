<?php

use ARC\Docs\Models\Doc;

get_header();

// Get doc by slug from URL
$slug = get_query_var('doc_slug') ?: $_GET['doc'] ?? '';

if (empty($slug)) {
    echo '<p>No documentation specified.</p>';
    get_footer();
    exit;
}

$doc = Doc::where('slug', $slug)->first();

if (!$doc || $doc->status !== 'published') {
    echo '<p>Documentation not found.</p>';
    get_footer();
    exit;
}
?>

<div class="single-doc-wrapper">
    <div class="container">
        <!-- Breadcrumbs -->
        <nav class="doc-breadcrumbs">
            <a href="<?php echo home_url(); ?>">Home</a>
            <span>/</span>
            <a href="<?php echo home_url('/docs'); ?>">Documentation</a>
            <span>/</span>
            <span><?php echo esc_html($doc->title); ?></span>
        </nav>

        <article class="single-doc">
            <header class="doc-header">
                <h1><?php echo esc_html($doc->title); ?></h1>
                
                <div class="doc-meta">
                    <span class="doc-author">
                        By <?php echo esc_html($doc->author_name); ?>
                    </span>
                    <span class="doc-date">
                        Published <?php echo $doc->created_at->format('F j, Y'); ?>
                    </span>
                    <span class="doc-updated">
                        Updated <?php echo $doc->updated_at->diffForHumans(); ?>
                    </span>
                </div>
            </header>

            <?php if ($doc->excerpt): ?>
                <div class="doc-excerpt">
                    <?php echo wp_kses_post($doc->excerpt); ?>
                </div>
            <?php endif; ?>

            <div class="doc-content">
                <?php echo wp_kses_post(wpautop($doc->content)); ?>
            </div>

            <!-- Related/Navigation -->
            <footer class="doc-footer">
                <div class="doc-navigation">
                    <?php
                    // Get previous doc
                    $prev = Doc::published()
                        ->where('id', '<', $doc->id)
                        ->orderBy('id', 'desc')
                        ->first();
                    
                    // Get next doc
                    $next = Doc::published()
                        ->where('id', '>', $doc->id)
                        ->orderBy('id', 'asc')
                        ->first();
                    ?>
                    
                    <?php if ($prev): ?>
                        <a href="<?php echo esc_url($prev->permalink); ?>" class="doc-nav-prev">
                            ← <?php echo esc_html($prev->title); ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($next): ?>
                        <a href="<?php echo esc_url($next->permalink); ?>" class="doc-nav-next">
                            <?php echo esc_html($next->title); ?> →
                        </a>
                    <?php endif; ?>
                </div>

                <div class="back-to-docs">
                    <a href="<?php echo home_url('/docs'); ?>">← Back to all documentation</a>
                </div>
            </footer>
        </article>

        <!-- Sidebar (optional - table of contents) -->
        <aside class="doc-sidebar">
            <div class="doc-toc">
                <h3>On This Page</h3>
                <div id="table-of-contents">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>

            <!-- Recent Docs -->
            <div class="recent-docs">
                <h3>Recent Documentation</h3>
                <ul>
                    <?php
                    $recent = Doc::published()
                        ->where('id', '!=', $doc->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                    
                    foreach ($recent as $recentDoc):
                    ?>
                        <li>
                            <a href="<?php echo esc_url($recentDoc->permalink); ?>">
                                <?php echo esc_html($recentDoc->title); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    </div>
</div>

<style>
.single-doc-wrapper {
    padding: 40px 20px;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 40px;
}

.doc-breadcrumbs {
    grid-column: 1 / -1;
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 30px;
    font-size: 0.9em;
    color: #666;
}

.doc-breadcrumbs a {
    color: #0073aa;
    text-decoration: none;
}

.doc-breadcrumbs a:hover {
    text-decoration: underline;
}

.single-doc {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 40px;
}

.doc-header h1 {
    font-size: 2.5em;
    margin: 0 0 20px 0;
    color: #1a1a1a;
}

.doc-meta {
    display: flex;
    gap: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
    font-size: 0.9em;
    color: #666;
}

.doc-excerpt {
    background: #f8f9fa;
    padding: 20px;
    border-left: 4px solid #0073aa;
    margin: 30px 0;
    font-size: 1.1em;
    line-height: 1.6;
}

.doc-content {
    margin: 30px 0;
    line-height: 1.8;
    color: #333;
}

.doc-content h2 {
    font-size: 1.8em;
    margin-top: 40px;
    margin-bottom: 15px;
    color: #1a1a1a;
}

.doc-content h3 {
    font-size: 1.4em;
    margin-top: 30px;
    margin-bottom: 10px;
    color: #1a1a1a;
}

.doc-content p {
    margin-bottom: 20px;
}

.doc-content code {
    background: #f5f5f5;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
}

.doc-content pre {
    background: #1e1e1e;
    color: #d4d4d4;
    padding: 20px;
    border-radius: 6px;
    overflow-x: auto;
    margin: 20px 0;
}

.doc-content pre code {
    background: none;
    padding: 0;
    color: inherit;
}

.doc-footer {
    margin-top: 50px;
    padding-top: 30px;
    border-top: 1px solid #e0e0e0;
}

.doc-navigation {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 20px;
}

.doc-nav-prev,
.doc-nav-next {
    padding: 15px 20px;
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    text-decoration: none;
    color: #0073aa;
    transition: background 0.2s;
    flex: 1;
}

.doc-nav-prev:hover,
.doc-nav-next:hover {
    background: #e9ecef;
}

.doc-nav-next {
    text-align: right;
}

.back-to-docs {
    text-align: center;
}

.back-to-docs a {
    color: #0073aa;
    text-decoration: none;
    font-weight: 600;
}

.back-to-docs a:hover {
    text-decoration: underline;
}

.doc-sidebar {
    position: sticky;
    top: 20px;
    height: fit-content;
}

.doc-toc,
.recent-docs {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.doc-toc h3,
.recent-docs h3 {
    margin: 0 0 15px 0;
    font-size: 1.2em;
    color: #1a1a1a;
}

.doc-toc ul,
.recent-docs ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.doc-toc li,
.recent-docs li {
    margin-bottom: 8px;
}

.doc-toc a,
.recent-docs a {
    color: #0073aa;
    text-decoration: none;
    font-size: 0.95em;
}

.doc-toc a:hover,
.recent-docs a:hover {
    text-decoration: underline;
}

@media (max-width: 1024px) {
    .container {
        grid-template-columns: 1fr;
    }
    
    .doc-sidebar {
        position: static;
    }
}
</style>

<script>
// Auto-generate table of contents from headings
document.addEventListener('DOMContentLoaded', function() {
    const content = document.querySelector('.doc-content');
    const toc = document.getElementById('table-of-contents');
    
    if (!content || !toc) return;
    
    const headings = content.querySelectorAll('h2, h3');
    
    if (headings.length === 0) {
        toc.parentElement.style.display = 'none';
        return;
    }
    
    const ul = document.createElement('ul');
    
    headings.forEach((heading, index) => {
        // Add ID to heading for anchor links
        const id = 'heading-' + index;
        heading.id = id;
        
        const li = document.createElement('li');
        const a = document.createElement('a');
        a.href = '#' + id;
        a.textContent = heading.textContent;
        
        if (heading.tagName === 'H3') {
            li.style.marginLeft = '15px';
        }
        
        li.appendChild(a);
        ul.appendChild(li);
    });
    
    toc.appendChild(ul);
});
</script>

<?php
get_footer();
?>