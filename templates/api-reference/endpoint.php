<?php
/**
 * API Reference - Endpoint Template
 * Place in: arc-docs/templates/api-reference/endpoint.php
 * 
 * Available variables:
 * - $endpoint (array with: method, url, description, params)
 */

if (!defined('ABSPATH')) exit;

$method = $endpoint['method'] ?? 'GET';
$url = $endpoint['url'] ?? '';
$description = $endpoint['description'] ?? '';
$params = $endpoint['params'] ?? [];
?>

<div class="api-endpoint">
    <div class="api-endpoint-header">
        <span class="api-method <?php echo esc_attr(strtolower($method)); ?>">
            <?php echo esc_html($method); ?>
        </span>
        <span class="api-url"><?php echo esc_html($url); ?></span>
    </div>
    
    <?php if ($description): ?>
        <div class="api-description">
            <?php echo esc_html($description); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($params)): ?>
        <div class="api-fields">
            <h4>Parameters:</h4>
            <?php foreach ($params as $param): ?>
                <div class="api-field">
                    <span class="api-field-name"><?php echo esc_html($param['name']); ?></span>
                    <span class="api-field-type"><?php echo esc_html($param['type'] ?? 'string'); ?></span>
                    <?php if (!empty($param['required'])): ?>
                        <span class="api-field-required">required</span>
                    <?php endif; ?>
                    <?php if (!empty($param['description'])): ?>
                        <div style="margin-top: 5px; color: #646970;">
                            <?php echo esc_html($param['description']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>