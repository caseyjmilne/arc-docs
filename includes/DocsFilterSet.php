<?php
namespace ARC\Docs;

use ARC\Lens\FilterSet;

class DocsFilterSet extends FilterSet
{
    protected $collection = 'docs';

    protected $filters = [
        'status' => [
            'type' => 'select',
            'label' => 'Status',
            'options' => ['draft' => 'Draft', 'published' => 'Published'],
            'placeholder' => 'All Statuses'
        ],
        'search' => [
            'type' => 'search',
            'label' => 'Search',
            'placeholder' => 'Search docs...'
        ]
    ];

    protected $defaultQuery = [
        'orderBy' => 'created_at',
        'orderDir' => 'desc',
        'perPage' => 20
    ];

    protected function wrapperTemplate()
    {
        return ARC_DOCS_PATH . 'templates/docs-wrapper.php';
    }

    protected function itemTemplate()
    {
        return ARC_DOCS_PATH . 'templates/doc-item.php';
    }
}