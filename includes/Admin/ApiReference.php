<?php
/**
 * API Reference Admin Page
 * Place in: arc-docs/includes/Admin/ApiReference.php
 */

namespace ARC\Docs\Admin;

use ARC\Gateway\Collection;

if (!defined('ABSPATH')) {
    exit;
}

class ApiReference {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'registerPage'], 20);
    }
    
    public function registerPage() {
        add_submenu_page(
            'arc-docs',
            'API Reference',
            'API Reference',
            'manage_options',
            'arc-docs-api-reference',
            [$this, 'renderPage']
        );
    }
    
    public function renderPage() {
        $collections = $this->getCollections();
        
        ?>
        <div class="wrap">
            <h1>API Reference</h1>
            <p>REST API documentation for all registered collections</p>
            
            <?php if (empty($collections)): ?>
                <div class="notice notice-warning">
                    <p>No collections registered. Make sure collections are registered before viewing API reference.</p>
                </div>
            <?php else: ?>
                <?php foreach ($collections as $collection_name => $collection_data): ?>
                    <?php $this->renderCollectionSection($collection_name, $collection_data); ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <style>
        .api-collection {
            background: #fff;
            border: 1px solid #c3c4c7;
            border-radius: 4px;
            margin-bottom: 30px;
            padding: 20px;
        }
        
        .api-collection h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .api-endpoint {
            margin: 20px 0;
            padding: 15px;
            background: #f6f7f7;
            border-left: 3px solid #2271b1;
        }
        
        .api-method {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: 600;
            font-size: 12px;
            margin-right: 10px;
        }
        
        .api-method.get { background: #61affe; color: #fff; }
        .api-method.post { background: #49cc90; color: #fff; }
        .api-method.put { background: #fca130; color: #fff; }
        .api-method.delete { background: #f93e3e; color: #fff; }
        
        .api-url {
            font-family: monospace;
            font-size: 14px;
            color: #1d2327;
        }
        
        .api-description {
            margin: 10px 0;
            color: #50575e;
        }
        
        .api-fields {
            margin-top: 15px;
        }
        
        .api-fields h4 {
            margin-bottom: 10px;
            font-size: 13px;
            color: #1d2327;
        }
        
        .api-field {
            padding: 8px;
            background: #fff;
            margin-bottom: 5px;
            border-radius: 3px;
        }
        
        .api-field-name {
            font-weight: 600;
            font-family: monospace;
            color: #2271b1;
        }
        
        .api-field-type {
            color: #646970;
            font-size: 12px;
            font-style: italic;
        }
        
        .api-field-required {
            color: #d63638;
            font-size: 11px;
            font-weight: 600;
        }
        </style>
        <?php
    }
    
    /**
     * Get all registered collections
     */
    private function getCollections() {
        // Get registry instance from Gateway plugin
        $registry = \ARC\Gateway\Plugin::getInstance()->getRegistry();
        $all_collections = $registry->getAll();
        $aliases = $registry->getAliases();
        
        $collections = [];
        
        foreach ($all_collections as $modelClass => $collection) {
            // Get the alias for this collection
            $alias = array_search($modelClass, $aliases);
            $name = $alias ?: $modelClass;
            
            $collections[$name] = [
                'model' => $modelClass,
                'fields' => $collection->getConfig('fields') ?? [],
                'config' => $collection->getConfig()
            ];
        }
        
        return $collections;
    }
    
    /**
     * Render a collection section
     */
    private function renderCollectionSection($collection_name, $collection_data) {
        $base_url = rest_url("arc-gateway/v1/{$collection_name}");
        $fields = $collection_data['fields'] ?? [];
        
        include ARC_DOCS_PATH . 'templates/api-reference/collection-section.php';
    }
    
    /**
     * Build field parameters from collection fields
     */
    private function buildFieldParams($fields, $context = 'create') {
        $params = [];
        
        foreach ($fields as $field) {
            $params[] = [
                'name' => $field->getKey(),
                'type' => $this->mapFieldType($field->getType()),
                'description' => $field->getLabel(),
                'required' => $context === 'create' ? $field->isRequired() : false
            ];
        }
        
        return $params;
    }
    
    /**
     * Map field type to API type
     */
    private function mapFieldType($type) {
        $map = [
            'text' => 'string',
            'textarea' => 'string',
            'number' => 'integer',
            'email' => 'string',
            'url' => 'string',
            'date' => 'string',
            'datetime' => 'string',
            'boolean' => 'boolean'
        ];
        
        return $map[$type] ?? 'string';
    }
}