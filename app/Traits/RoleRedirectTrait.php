<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait RoleRedirectTrait
{
    /**
     * Get the redirect path based on user roles/permissions.
     *
     * @param  \App\Models\User|null  $user
     * @return string
     */
    public function getRedirectPath($user = null)
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return route('login');
        }

        // Super Admin - Dashboard
        if ($user->hasRole('super-admin')) {
            return route('home');
        }
        
        // Press Role - Press Orders (Highest Priority as requested)
        if ($user->can('الاحصائيات')) {
            return route('home');
        }
        // Press Role - Press Orders (Highest Priority as requested)
        if ($user->can('المكبس')) {
            return route('presslist');
        }

        // Printer Role - Printing Orders
        if ($user->can('الطباعه')) {
            return route('AddPrintOrders');
        }
        
        // Stras Role - Stras Orders
        if ($user->can('الاستراس')) {
            return route('stras.index');
        }
        
        // Tarter Role - Tarter Orders
        if ($user->can('الترتر')) {
            return route('tarter.index');
        }
        
        // Laser Role - Laser Orders
        if ($user->can('الليزر')) {
            return route('laser.index');
        }
        
        // Salaries Role - Payroll
        if ($user->can('الرواتب')) {
            return route('payroll.index');
        }
        
        // Invoice Role - Invoice Page
        if ($user->can('الفواتير')) {
            return route('invoice.create');
        }
        
        // Inventory Role - Inventory Page
        if ($user->can('المخزن')) {
            return route('inventory.index');
        }
        
        // Default redirect to home
        return route('home');
    }
}
