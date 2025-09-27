<?php
/**
 * Plugin Name: ARC Docs
 * Description: Document management system using ARC Framework
 * Version: 1.0.0
 * Author: ARC Software Group
 * Requires PHP: 7.4
 * Namespace: ARC\Docs
 */

namespace ARC\Docs;

use ARC\Gateway\Collection;

if (!defined('ABSPATH')) {
    exit;
}

define('ARC_DOCS_VERSION', '1.0.0');
define('ARC_DOCS_PATH', plugin_dir_path(__FILE__));
define('ARC_DOCS_URL', plugin_dir_url(__FILE__));
define('ARC_DOCS_FILE', __FILE__);

class Plugin
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->init();
    }

    private function init()
    {
        add_action('arc_forge_eloquent_booted', [$this, 'loadModel'], 10);
        add_action('plugins_loaded', [$this, 'checkDatabase'], 5);
        add_action('init', [$this, 'registerCollection'], 15);
        
        require_once ARC_DOCS_PATH . 'rewrite-rules.php';
        
        register_activation_hook(ARC_DOCS_FILE, [$this, 'activate']);
        register_deactivation_hook(ARC_DOCS_FILE, [$this, 'deactivate']);
    }
    
    public function loadModel()
    {
        require_once ARC_DOCS_PATH . 'models/Doc.php';
    }
    
    public function checkDatabase()
    {
        $current_version = get_option('arc_docs_db_version', '0');
        
        if (version_compare($current_version, ARC_DOCS_VERSION, '<')) {
            $this->createTable();
        }
    }

    public function registerCollection()
{
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
        
        // Debug - check if fields were created
        error_log('Fields created: ' . print_r($fields, true));

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

    public function createTable()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'docs';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            content longtext,
            excerpt text,
            status varchar(20) DEFAULT 'draft',
            author_id bigint(20) unsigned NOT NULL,
            created_at timestamp NULL DEFAULT NULL,
            updated_at timestamp NULL DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug),
            KEY author_id (author_id),
            KEY status (status)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        update_option('arc_docs_db_version', ARC_DOCS_VERSION);
    }

    public function activate()
    {
        $this->createTable();
        flush_rewrite_rules();
        do_action('arc_docs_activated');
    }

    public function deactivate()
    {
        flush_rewrite_rules();
        do_action('arc_docs_deactivated');
    }
}

Plugin::getInstance();

function arc_docs()
{
    return Collection::get('docs');
}

function arc_get_doc($id)
{
    return arc_docs()->find($id);
}

function arc_create_doc($attributes)
{
    return arc_docs()->create($attributes);
}