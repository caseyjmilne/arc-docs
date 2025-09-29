<?php
/**
 * templates/doc-item.php
 * Individual doc card
 * Variables: $item
 */
?>
<div class="arc-doc-card" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; background: #fff;">
    <h3 style="margin: 0 0 10px 0;">
        <a href="<?php echo admin_url('admin.php?page=arc-docs-edit&id=' . $item['id']); ?>">
            <?php echo esc_html($item['title']); ?>
        </a>
    </h3>
    
    <?php if (!empty($item['excerpt'])): ?>
        <p style="margin: 0 0 10px 0; color: #666;">
            <?php echo esc_html($item['excerpt']); ?>
        </p>
    <?php endif; ?>
    
    <div class="arc-doc-meta" style="font-size: 12px; color: #999;">
        <span class="status" style="padding: 2px 8px; background: <?php echo $item['status'] === 'published' ? '#46b450' : '#999'; ?>; color: #fff; border-radius: 3px;">
            <?php echo esc_html(ucfirst($item['status'] ?? 'draft')); ?>
        </span>
        
        <span style="margin-left: 10px;">
            <?php echo esc_html(date('M j, Y', strtotime($item['created_at']))); ?>
        </span>
        
        <span style="float: right;">
            <a href="<?php echo admin_url('admin.php?page=arc-docs-edit&id=' . $item['id']); ?>">Edit</a>
        </span>
    </div>
</div>