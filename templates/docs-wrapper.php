<?php
/**
 * templates/docs-wrapper.php
 * Wrapper for docs grid
 * Variables: $items, $itemTemplate
 */
?>
<div class="arc-docs-grid">
    <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
            <?php include $itemTemplate; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No docs found.</p>
    <?php endif; ?>
</div>