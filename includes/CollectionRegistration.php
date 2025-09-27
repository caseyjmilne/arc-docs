<?php
/**
 * ARC Docs Collection Registration
 * Place in: arc-docs/includes/CollectionRegistration.php
 */

namespace ARC\Docs;

use ARC\Gateway\Collection;

if (!defined('ABSPATH')) {
    exit;
}

class CollectionRegistration {
    
    public static function register() {
        if (!class_exists('ARC\Gateway\Collection')) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p><strong>ARC Docs:</strong> Requires ARC Gateway plugin to be active.</p></div>';
            });
            return;
        }

        $fields = [
            \ARC\Blueprint\Field::text('title')->required()->label('Title'),
            \ARC\Blueprint\Field::text('slug')->required()->label('Slug'),
            \ARC\Blueprint\Field::text('content')->label('Content'),
            \ARC\Blueprint\Field::text('excerpt')->label('Excerpt'),
            \ARC\Blueprint\Field::text('status')->default('draft')->label('Status'),
            \ARC\Blueprint\Field::text('author_id')->default(1)->label('Author ID'),
        ];

        Collection::register('ARC\Docs\Models\Doc', [
            'fields' => $fields,
            'cache_enabled' => true,
            'cache_duration' => 3600,
            'searchable' => ['title', 'content', 'slug'],
            'sortable' => ['title', 'created_at', 'updated_at', 'status'],
            'filters' => ['status', 'author_id'],
            'relations' => [],
            'scopes' => ['published']
        ], 'docs');

        do_action('arc_docs_collection_registered');
    }
}