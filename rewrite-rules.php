<?php
/**
 * ARC Docs Rewrite Rules
 */

namespace ARC\Docs;

if (!defined('ABSPATH')) {
    exit;
}

// Register query var
add_filter('query_vars', function($vars) {
    $vars[] = 'doc_slug';
    return $vars;
});

// Add the rewrite rule - WordPress uses numbered placeholders, not $matches
add_action('init', function() {
    add_rewrite_rule(
        '^doc/([^/]+)/?$',
        'index.php?doc_slug=$matches[1]',
        'top'
    );
}, 10, 0);

// Template loader
add_action('template_redirect', function() {
    $slug = get_query_var('doc_slug');
    
    if (!empty($slug) && $slug !== '1' && $slug !== '$matches[1]') {
        $template = locate_template(['single-doc.php']);
        if ($template) {
            load_template($template);
            exit;
        }
    }
});