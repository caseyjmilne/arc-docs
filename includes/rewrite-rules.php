<?php
/**
 * ARC Docs Rewrite Rules
 */

namespace ARC\Docs;

if (!defined('ABSPATH')) {
    exit;
}

// Register query vars
add_filter('query_vars', function($vars) {
    $vars[] = 'doc_slug';
    $vars[] = 'docs_list';
    return $vars;
});

// Add the rewrite rules - WordPress uses numbered placeholders, not $matches
add_action('init', function() {
    // Docs list page
    add_rewrite_rule(
        '^docs/?$',
        'index.php?docs_list=1',
        'top'
    );

    // Individual doc page
    add_rewrite_rule(
        '^doc/([^/]+)/?$',
        'index.php?doc_slug=$matches[1]',
        'top'
    );

    // Check if we need to flush rewrite rules
    if (get_option('arc_docs_flush_rewrite_rules', false)) {
        flush_rewrite_rules();
        delete_option('arc_docs_flush_rewrite_rules');
    }
}, 10, 0);

// Template loader using template_redirect action
add_action('template_redirect', function() {
    $slug = get_query_var('doc_slug');
    $docs_list = get_query_var('docs_list');

    // Handle docs list page
    if (!empty($docs_list)) {
        $docs_template = ARC_DOCS_PATH . 'templates/doc-main.php';
        if (file_exists($docs_template)) {
            load_template($docs_template);
            exit;
        }
    }

    // Handle individual doc page
    if (!empty($slug) && $slug !== '1' && $slug !== '$matches[1]') {
        $doc_template = ARC_DOCS_PATH . 'templates/single-doc.php';
        if (file_exists($doc_template)) {
            load_template($doc_template);
            exit;
        }
    }
});

// Force flush rewrite rules on plugin activation to ensure new rules are active
register_activation_hook(ARC_DOCS_FILE, function() {
    update_option('arc_docs_flush_rewrite_rules', true);
});