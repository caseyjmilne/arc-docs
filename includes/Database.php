<?php
/**
 * ARC Docs Database Handler
 * Place in: arc-docs/includes/Database.php
 */

namespace ARC\Docs;

if (!defined('ABSPATH')) {
    exit;
}

class Database {
    
    /**
     * Check if database needs updating
     */
    public static function check() {
        $current_version = get_option('arc_docs_db_version', '0');
        
        if (version_compare($current_version, ARC_DOCS_VERSION, '<')) {
            self::createTable();
        }
    }
    
    /**
     * Create the docs table
     */
    public static function createTable() {
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
}