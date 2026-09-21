<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MenuHelper
{
    /**
     * Determine if a menu or submenu item should be visible to the given or authenticated user.
     *
     * @param object|array $item
     * @param User|null $user
     * @return bool
     */
    public static function isItemVisible(object|array $item, ?User $user = null): bool
    {
        $item = (object) $item;
        $user = $user ?? Auth::user();

        // If unauthenticated, hide menu items
        if (! $user) {
            return false;
        }

        // Super Admin has unrestricted access to all menu items
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // If item has a submenu, it is visible if at least one of its children is visible
        if (isset($item->submenu) && is_array($item->submenu)) {
            foreach ($item->submenu as $subItem) {
                if (self::isItemVisible($subItem, $user)) {
                    return true;
                }
            }
            return false;
        }

        // If specific permission(s) are required for this item
        if (isset($item->permission)) {
            $permissions = is_array($item->permission) ? $item->permission : [$item->permission];

            foreach ($permissions as $permission) {
                if ($user->can($permission)) {
                    return true;
                }
            }

            return false;
        }

        // If no permission is defined (e.g. default Dashboard), it is visible to all authenticated users
        return true;
    }
}
