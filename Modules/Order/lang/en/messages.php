<?php

return [
    'order_created' => 'Order created successfully.',
    'order_updated' => 'Order updated successfully.',
    'payment_link_generated' => 'Payment link generated.',
    'order_loaded' => 'Order loaded.',
    'order_not_found' => 'Order not found.',
    'status_final' => 'Order is in a final state.',
    'status_canceled' => 'Order is canceled or refunded; cannot reopen.',
    'status_changed' => 'Order status updated.',
    'payment_status_changed' => 'Payment status updated successfully.',
    'status_created' => 'Order status created successfully.',
    'status_updated' => 'Order status updated successfully.',
    'status_deleted' => 'Order status deleted successfully.',
    'payment_created' => 'Payment created successfully.',
    'payment_processed' => 'Payment processed successfully.',
    'payment_failed' => 'Payment processing failed.',
    'callback_ignored' => 'Callback ignored.',
    'callback_processed' => 'Callback processed.',
    'callback_failed' => 'Callback processing failed.',
    'invoice_url_missing' => 'Invoice URL missing from payment provider response.',
    'payment_creation_failed' => 'Payment creation failed.',
    'product_fallback' => 'Product',
    'validation' => [
        'guest_uuid' => [
            'required' => 'Guest UUID is required.',
            'uuid' => 'Guest UUID must be a valid UUID.',
        ],
        'payment_method' => [
            'required' => 'Payment method is required.',
            'string' => 'Payment method must be a string.',
            'max' => 'Payment method may not be greater than :max characters.',
        ],
        'shipping' => [
            'required' => 'Shipping address is required.',
            'array' => 'Shipping address must be an array.',
        ],
        'shipping.full_name' => [
            'required' => 'Full name is required.',
            'string' => 'Full name must be a string.',
            'max' => 'Full name may not be greater than :max characters.',
        ],
        'shipping.phone' => [
            'required' => 'Phone number is required.',
            'string' => 'Phone number must be a string.',
            'max' => 'Phone number may not be greater than :max characters.',
        ],
        'shipping.address' => [
            'string' => 'Address must be a string.',
            'max' => 'Address may not be greater than :max characters.',
        ],
        'shipping.city' => [
            'string' => 'City must be a string.',
            'max' => 'City may not be greater than :max characters.',
        ],
        'shipping.state' => [
            'string' => 'State must be a string.',
            'max' => 'State may not be greater than :max characters.',
        ],
        'shipping.country' => [
            'string' => 'Country must be a string.',
            'max' => 'Country may not be greater than :max characters.',
        ],
        'shipping.postal_code' => [
            'string' => 'Postal code must be a string.',
            'max' => 'Postal code may not be greater than :max characters.',
        ],
        'shipping.user_address_id' => [
            'integer' => 'User address ID must be an integer.',
            'exists' => 'Selected user address does not exist.',
            'belongs_to_user' => 'The selected address does not belong to you.',
        ],
        'status_id' => [
            'required' => 'Status ID is required.',
            'exists' => 'Selected status does not exist.',
        ],
        'comment' => [
            'string' => 'Comment must be a string.',
            'max' => 'Comment may not be greater than :max characters.',
        ],
        'code' => [
            'required' => 'Status code is required.',
            'string' => 'Status code must be a string.',
            'max' => 'Status code may not be greater than :max characters.',
            'unique' => 'This status code already exists.',
        ],
        'title' => [
            'required' => 'Status title is required.',
            'array' => 'Status title must be an array with en and ar keys.',
            'string' => 'Status title must be a string.',
            'max' => 'Status title may not be greater than :max characters.',
            'en' => [
                'required' => 'English title is required.',
            ],
            'ar' => [
                'required' => 'Arabic title is required.',
            ],
        ],
        'color' => [
            'string' => 'Color must be a string.',
            'max' => 'Color may not be greater than :max characters.',
        ],
        'sort_order' => [
            'integer' => 'Sort order must be an integer.',
            'min' => 'Sort order must be at least :min.',
        ],
        'notes' => [
            'array' => 'Notes must be an array.',
        ],
        'meta' => [
            'array' => 'Metadata must be an array.',
        ],
        'payment_status' => [
            'required' => 'Payment status is required.',
            'in' => 'Payment status must be one of: pending, paid, failed, cancelled, refunded.',
        ],
    ],
    'status' => [
        'pending' => 'Pending',
        'completed' => 'Completed',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
    ],
    'order' => [
        'payment_status' => [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
            'success' => 'Payment processed successfully.',
        ],
    ],
    'payment_provider' => [
        'sadad' => 'Sadad',
    ],
    'dashboard' => [
        'orders' => [
            'title' => 'Orders',
            'description' => 'Manage customer orders, payments, and statuses.',
            'filters' => [
                'search' => 'Search orders…',
                'all_statuses' => 'All statuses',
                'all_payments' => 'All payments',
                'paid' => 'Paid',
                'pending' => 'Pending',
                'failed' => 'Failed',
            ],
            'table' => [
                'id' => 'ID',
                'customer' => 'Customer',
                'amount' => 'Amount',
                'status' => 'Status',
                'payment_status' => 'Payment',
                'created_at' => 'Created at',
                'actions' => 'Actions',
            ],
            'actions' => [
                'change_status' => 'Change status',
                'view' => 'View',
                'edit' => 'Edit',
            ],
        ],
        'statuses' => [
            'title' => 'Order Statuses',
            'description' => 'Configure status codes and transitions.',
            'table' => [
                'code' => 'Code',
                'title' => 'Title',
                'flags' => 'Flags',
                'sort' => 'Sort',
                'actions' => 'Actions',
            ],
            'actions' => [
                'create' => 'Add status',
                'edit' => 'Edit status',
                'delete' => 'Delete status',
                'cancel' => 'Cancel',
                'save' => 'Save',
            ],
            'form' => [
                'code' => 'Code',
                'title' => 'Title',
                'color' => 'Color',
                'sort_order' => 'Sort order',
                'is_default' => 'Default',
                'is_final' => 'Final (no further changes)',
                'is_cancel' => 'Cancel status',
                'is_refund' => 'Refund status',
            ],
            'flags' => [
                'default' => 'Default',
                'final' => 'Final',
                'cancel' => 'Cancel',
                'refund' => 'Refund',
            ],
        ],
    ],
];
