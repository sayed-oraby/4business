<?php

return [
    'title' => 'Roles & Permissions',
    'description' => 'Control which modules administrators can access.',
    'roles' => [
        'table_title' => 'Roles',
        'name' => 'Role name',
        'guard' => 'Guard',
        'users' => 'Users',
        'create' => 'Create role',
        'edit' => 'Edit role',
    ],
    'permissions' => [
        'title' => 'Permissions',
        'create' => 'Add permission',
        'name' => 'Permission',
        'module' => 'Module',
        'ability' => 'Ability',
    ],
    'actions' => [
        'save' => 'Save',
        'delete' => 'Delete',
    ],
    'confirm' => [
        'delete_title' => 'Delete role?',
        'delete_message' => 'This will remove the role and revoke its permissions.',
        'confirm' => 'Yes, delete',
        'cancel' => 'Cancel',
    ],
    'messages' => [
        'role_created' => 'Role created successfully.',
        'role_updated' => 'Role updated.',
        'role_deleted' => 'Role deleted.',
        'permissions_synced' => 'Permissions updated.',
        'permission_created' => 'Permission created.',
        'cannot_delete_super_admin' => 'Cannot delete the Super Admin role.',
    ],
    'permissions_list' => [
        'authorization' => [
            'authorization.update' => 'Manage roles & permissions',
            'authorization.view' => 'View roles & permissions',
        ],
        'dashboard' => [
            'dashboard.access' => 'Access the dashboard',
        ],
        'settings' => [
            'settings.update' => 'Update application settings',
            'settings.view' => 'View application settings',
        ],
        'users' => [
            'users.create' => 'Create users',
            'users.delete' => 'Delete (archive) users',
            'users.restore' => 'Restore users',
            'users.update' => 'Edit users',
            'users.view' => 'View users',
        ],
    ],
];
