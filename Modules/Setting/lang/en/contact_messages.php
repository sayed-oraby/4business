<?php

return [
    'title' => 'Contact Messages',
    'description' => 'Manage incoming contact messages from customers',
    
    'fields' => [
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'country_code' => 'Country Code',
        'subject' => 'Subject',
        'message' => 'Message',
        'status' => 'Status',
        'created_at' => 'Date Sent',
    ],

    'statuses' => [
        'pending' => 'Pending',
        'read' => 'Read',
        'replied' => 'Replied',
        'closed' => 'Closed',
    ],

    'messages' => [
        'created' => 'Message sent successfully.',
        'status_updated' => 'Message status updated.',
        'deleted' => 'Message deleted.',
        'bulk_deleted' => ':count messages deleted.',
    ],

    'stats' => [
        'total' => 'Total Messages',
        'pending' => 'Pending',
        'read' => 'Read',
        'replied' => 'Replied',
        'closed' => 'Closed',
    ],

    'actions' => [
        'view' => 'View',
        'delete' => 'Delete',
        'update_status' => 'Update Status',
        'bulk_delete' => 'Delete Selected',
    ],

    'confirm' => [
        'delete_title' => 'Delete Message?',
        'delete_message' => 'Are you sure you want to delete this message? This action cannot be undone.',
        'bulk_title' => 'Delete Selected Messages?',
        'bulk_message' => 'You are about to delete :count messages. This action cannot be undone.',
    ],
];
