<?php

if (!function_exists('getPermissionField')) {

    function getPermissionField(string $action): string
    {
        return match ($action) {
            'view'   => 'can_view',
            'create' => 'can_create',
            'update' => 'can_update',
            'delete' => 'can_delete',
            default  => 'can_view',
        };
    }
}
