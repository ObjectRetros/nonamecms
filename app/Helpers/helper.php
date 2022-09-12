<?php

use App\Models\WebsitePermission;
use App\Models\WebsiteSetting;

if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}

function setting(string $setting): string
{
    return WebsiteSetting::query()->where('key', '=', $setting)->first()->value ?? '';
}

function permission(string $permission): string|bool
{
    $permission = WebsitePermission::query()->where('key', '=', $permission)->first();

    if (is_null($permission)) {
        return 999;
    }

    return $permission->value;
}
