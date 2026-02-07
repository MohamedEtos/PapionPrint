<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Redirect users after login based on their role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated($request, $user)
    {
        // Super Admin - Dashboard
        if ($user->hasRole('super-admin')) {
            return redirect()->route('home');
        }
        
        // Printer Role - Printing Orders
        if ($user->can('الطباعه')) {
            return redirect()->route('AddPrintOrders');
        }
        
        // Stras Role - Stras Orders
        if ($user->can('الاستراس')) {
            return redirect()->route('stras.index');
        }
        
        // Tarter Role - Tarter Orders
        if ($user->can('الترتر')) {
            return redirect()->route('tarter.index');
        }
        
        // Press Role - Press Orders
        if ($user->can('المكبس')) {
            return redirect()->route('presslist');
        }
        
        // Laser Role - Laser Orders
        if ($user->can('الليزر')) {
            return redirect()->route('laser.index');
        }
        
        // Salaries Role - Payroll
        if ($user->can('الرواتب')) {
            return redirect()->route('payroll.index');
        }
        
        // Invoice Role - Invoice Page
        if ($user->can('الفواتير')) {
            return redirect()->route('invoice.create');
        }
        
        // Inventory Role - Inventory Page
        if ($user->can('المخزن')) {
            return redirect()->route('inventory.index');
        }
        
        // Default redirect to home
        return redirect()->route('home');
    }
}
