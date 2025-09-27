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
        
        require_once ARC_DOCS_PATH . 'includes/rewrite-rules.php';
        require_once ARC_DOCS_PATH . 'includes/helpers.php';
        require_once ARC_DOCS_PATH . 'includes/Database.php';
        require_once ARC_DOCS_PATH . 'includes/CollectionRegistration.php';
        
        // Load admin pages when in admin
        if (is_admin()) {
            require_once ARC_DOCS_PATH . 'includes/Admin/AdminPages.php';
            new Admin\AdminPages();
        }
        
        register_activation_hook(ARC_DOCS_FILE, [$this, 'activate']);
        register_deactivation_hook(ARC_DOCS_FILE, [$this, 'deactivate']);
    }
    
    public function loadModel()
    {
        require_once ARC_DOCS_PATH . 'models/Doc.php';
    }
    
    public function checkDatabase()
    {
        Database::check();
    }

    public function registerCollection()
    {
        CollectionRegistration::register();
    }

    public function activate()
    {
        Database::createTable();
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