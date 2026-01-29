<?php

return [
    'title' => 'Brands',
    'description' => 'Manage store brands, activate/deactivate them, and keep logos fresh.',
    'search_placeholder' => 'Search brands...',
    'table' => [
        'title' => 'Title',
        'status' => 'Status',
        'position' => 'Sort order',
        'updated_at' => 'Last updated',
        'actions' => 'Actions',
    ],
    'form' => [
        'localization' => 'Localized name',
        'title_en' => 'Title (English)',
        'title_ar' => 'Title (Arabic)',
        'status' => 'Status',
        'position' => 'Sort order',
        'image' => 'Logo',
        'image_help' => 'Recommended 500x500px. Allowed types: *.png, *.jpg, *.jpeg, *.webp',
        'save' => 'Save brand',
    ],
    'statuses' => [
        'draft' => 'Draft',
        'active' => 'Active',
        'archived' => 'Archived',
    ],
    'states' => [
        'active' => 'Active',
        'archived' => 'Archived',
    ],
    'actions' => [
        'create' => 'Add brand',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'bulk_delete' => 'Delete selected',
        'bulk_delete_confirm' => 'You are about to delete :count brands. This action cannot be undone.',
        'confirm_delete' => 'Are you sure you want to delete this brand?',
        'confirm' => 'Confirm',
        'cancel' => 'Cancel',
    ],
    'messages' => [
        'created' => 'Brand created successfully.',
        'updated' => 'Brand updated successfully.',
        'deleted' => 'Brand deleted successfully.',
        'bulk_deleted' => 'Selected brands deleted successfully.',
        'listed' => 'Brands loaded successfully.',
    ],
    'audit' => [
        'created' => 'Brand ":title" created',
        'updated' => 'Brand ":title" updated',
        'deleted' => 'Brand ":title" deleted',
        'bulk_deleted' => 'Brand ":title" deleted (bulk)',
    ],
];
