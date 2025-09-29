<?php
/**
 * ARC Docs Admin Pages
 * Place in: arc-docs/includes/Admin/AdminPages.php
 */

namespace ARC\Docs\Admin;

use ARC\Blueprint\Forms\FormHelper;
use ARC\Docs\Models\Doc;
use ARC\Lens\Render as LensRender;

class AdminPages {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'registerMenuPages']);
    }
    
    /**
     * Register admin menu pages
     */
    public function registerMenuPages() {
        // Main menu page - List all docs
        add_menu_page(
            'Docs',                    // Page title
            'Docs',                    // Menu title
            'manage_options',          // Capability
            'arc-docs',               // Menu slug
            [$this, 'renderListPage'], // Callback
            'dashicons-media-document', // Icon
            20                         // Position
        );
        
        // Create new doc
        add_submenu_page(
            'arc-docs',               // Parent slug
            'Create Doc',             // Page title
            'Create New',             // Menu title
            'manage_options',         // Capability
            'arc-docs-create',        // Menu slug
            [$this, 'renderCreatePage'] // Callback
        );
        
        // Edit doc (hidden from menu)
        add_submenu_page(
            null,                     // Hidden - no parent
            'Edit Doc',               // Page title
            'Edit Doc',               // Menu title (not shown)
            'manage_options',         // Capability
            'arc-docs-edit',          // Menu slug
            [$this, 'renderEditPage'] // Callback
        );
    }
    
    public function renderListPage() {
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Docs</h1>
            <a href="<?php echo admin_url('admin.php?page=arc-docs-create'); ?>" class="page-title-action">Add New</a>
            <hr class="wp-header-end">

            <?php            
            // Render the FilterSet (filters + results container)
            arc_lens_render('docs'); 
            ?>
            
        </div>
        <?php
    }
    
    /**
     * Render create page
     */
    public function renderCreatePage() {
        ?>
        <div class="wrap">
            <h1>Create New Doc</h1>
            <?php
            // Use FormHelper to render the form
            FormHelper::renderForm('create', 'docs', [
                'title' => false, // Don't show title, we have h1 above
                'submitText' => 'Create Doc',
                'jsOptions' => [
                    'successMessage' => 'Doc created successfully!',
                    'redirectOnSuccess' => admin_url('admin.php?page=arc-docs')
                ]
            ]);
            ?>
        </div>
        <?php
    }
    
    /**
     * Render edit page
     */
    public function renderEditPage() {
        $doc_id = $_GET['id'] ?? null;
        
        if (!$doc_id) {
            echo '<div class="wrap"><p>Invalid doc ID</p></div>';
            return;
        }
        
        $doc = Doc::find($doc_id);
        
        if (!$doc) {
            echo '<div class="wrap"><p>Doc not found</p></div>';
            return;
        }
        
        ?>
        <div class="wrap">
            <h1>Edit Doc: <?php echo esc_html($doc->title); ?></h1>
            <?php
            // Use FormHelper to render the form
            FormHelper::renderForm('edit', 'docs', [
                'title' => false,
                'data' => $doc,
                'submitText' => 'Update Doc',
                'endpoint' => rest_url('arc-gateway/v1/docs/' . $doc->id),
                'jsOptions' => [
                    'method' => 'PUT',
                    'successMessage' => 'Doc updated successfully!',
                    'resetOnSuccess' => false,
                    'redirectOnSuccess' => admin_url('admin.php?page=arc-docs')
                ]
            ]);
            ?>
        </div>
        <?php
    }
}