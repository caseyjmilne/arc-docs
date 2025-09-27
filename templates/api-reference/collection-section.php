<?php
/**
 * API Reference - Collection Section Template
 * Place in: arc-docs/templates/api-reference/collection-section.php
 * 
 * Available variables:
 * - $collection_name (string)
 * - $collection_data (array)
 * - $base_url (string)
 * - $fields (array)
 */

if (!defined('ABSPATH')) exit;
?>

<div class="api-collection">
    <h2><?php echo esc_html(ucfirst($collection_name)); ?></h2>
    
    <!-- List all items -->
    <?php 
    include ARC_DOCS_PATH . 'templates/api-reference/endpoint.php'; 
    $endpoint = [
        'method' => 'GET',
        'url' => $base_url,
        'description' => 'Get all ' . $collection_name,
        'params' => [
            ['name' => 'per_page', 'type' => 'integer', 'description' => 'Items per page (default: 10)'],
            ['name' => 'page', 'type' => 'integer', 'description' => 'Page number'],
            ['name' => 'search', 'type' => 'string', 'description' => 'Search term'],
            ['name' => 'sort', 'type' => 'string', 'description' => 'Sort field'],
            ['name' => 'order', 'type' => 'string', 'description' => 'Sort order (asc/desc)']
        ]
    ];
    include ARC_DOCS_PATH . 'templates/api-reference/endpoint.php';
    ?>
    
    <!-- Get single item -->
    <?php
    $endpoint = [
        'method' => 'GET',
        'url' => $base_url . '/{id}',
        'description' => 'Get a single ' . rtrim($collection_name, 's'),
        'params' => [
            ['name' => 'id', 'type' => 'integer', 'description' => 'Item ID', 'required' => true]
        ]
    ];
    include ARC_DOCS_PATH . 'templates/api-reference/endpoint.php';
    ?>
    
    <!-- Create item -->
    <?php
    $endpoint = [
        'method' => 'POST',
        'url' => $base_url,
        'description' => 'Create a new ' . rtrim($collection_name, 's'),
        'params' => $this->buildFieldParams($fields, 'create')
    ];
    include ARC_DOCS_PATH . 'templates/api-reference/endpoint.php';
    ?>
    
    <!-- Update item -->
    <?php
    $endpoint = [
        'method' => 'PUT',
        'url' => $base_url . '/{id}',
        'description' => 'Update a ' . rtrim($collection_name, 's'),
        'params' => array_merge(
            [['name' => 'id', 'type' => 'integer', 'description' => 'Item ID', 'required' => true]],
            $this->buildFieldParams($fields, 'update')
        )
    ];
    include ARC_DOCS_PATH . 'templates/api-reference/endpoint.php';
    ?>
    
    <!-- Delete item -->
    <?php
    $endpoint = [
        'method' => 'DELETE',
        'url' => $base_url . '/{id}',
        'description' => 'Delete a ' . rtrim($collection_name, 's'),
        'params' => [
            ['name' => 'id', 'type' => 'integer', 'description' => 'Item ID', 'required' => true]
        ]
    ];
    include ARC_DOCS_PATH . 'templates/api-reference/endpoint.php';
    ?>
</div>