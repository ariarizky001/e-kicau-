<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('isSuperAdmin')) {
    /**
     * Check if current user is Super Admin
     */
    function isSuperAdmin()
    {
        $user = Auth::user();
        return $user && $user->role === 'super_admin';
    }
if (!function_exists('isAdmin')) {
    /**
     * Check if current user is Admin
     */
    function isAdmin()
    {
        $user = Auth::user();
        return $user && $user->role === 'admin';
    }
}
    }
if (!function_exists('getUserRole')) {
    /**
     * Get current user role
     */
    function getUserRole()
    {
        $user = Auth::user();
        return $user ? $user->role : null;
    }
}


