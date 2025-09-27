<?php
/**
 * ARC Docs Admin Pages
 * Place in: arc-docs/includes/Admin/AdminPages.php
 */

namespace ARC\Docs\Admin;

use ARC\Blueprint\Forms\FormHelper;
use ARC\Docs\Models\Doc;

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
    
    /**
     * Render list page
     */
    public function renderListPage() {
        $docs = Doc::orderBy('created_at', 'desc')->get();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Docs</h1>
            <a href="<?php echo admin_url('admin.php?page=arc-docs-create'); ?>" class="page-title-action">Add New</a>
            <hr class="wp-header-end">
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Author</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($docs->isEmpty()): ?>
                        <tr>
                            <td colspan="6">No docs found. <a href="<?php echo admin_url('admin.php?page=arc-docs-create'); ?>">Create your first doc</a></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($docs as $doc): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <a href="<?php echo admin_url('admin.php?page=arc-docs-edit&id=' . $doc->id); ?>">
                                            <?php echo esc_html($doc->title); ?>
                                        </a>
                                    </strong>
                                </td>
                                <td><?php echo esc_html($doc->slug); ?></td>
                                <td><?php echo esc_html($doc->status ?? 'draft'); ?></td>
                                <td><?php echo esc_html(get_userdata($doc->author_id)->display_name ?? 'Unknown'); ?></td>
                                <td><?php echo esc_html($doc->created_at); ?></td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=arc-docs-edit&id=' . $doc->id); ?>">Edit</a> |
                                    <a href="#" class="arc-delete-doc" data-id="<?php echo $doc->id; ?>" style="color: #b32d2e;">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('.arc-delete-doc').on('click', function(e) {
                e.preventDefault();
                if (!confirm('Are you sure you want to delete this doc?')) {
                    return;
                }
                
                const id = $(this).data('id');
                const row = $(this).closest('tr');
                
                $.ajax({
                    url: '<?php echo rest_url('arc-gateway/v1/docs/'); ?>' + id,
                    method: 'DELETE',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', '<?php echo wp_create_nonce('wp_rest'); ?>');
                    },
                    success: function() {
                        row.fadeOut(function() {
                            row.remove();
                        });
                    },
                    error: function() {
                        alert('Error deleting doc');
                    }
                });
            });
        });
        </script>
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