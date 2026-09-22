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

        // If specific user_type(s) are required for this item (e.g. developer only)
        if (isset($item->user_type) || isset($item->user_types)) {
            $requiredTypes = isset($item->user_types) ? (array) $item->user_types : [(string) $item->user_type];
            $userTypeValue = is_object($user->user_type) ? $user->user_type->value : (string) $user->user_type;

            if (! in_array($userTypeValue, $requiredTypes, true)) {
                return false;
            }
        }

        // Super Admin has unrestricted access to all general menu items
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
