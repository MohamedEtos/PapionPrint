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
        if ($user->hasRole('printer')) {
            return redirect()->route('AddPrintOrders');
        }
        
        // Stras Role - Stras Orders
        if ($user->hasRole('stras')) {
            return redirect()->route('stras.index');
        }
        
        // Tarter Role - Tarter Orders
        if ($user->hasRole('tarter')) {
            return redirect()->route('tarter.index');
        }
        
        // Press Role - Press Orders
        if ($user->hasRole('press')) {
            return redirect()->route('presslist');
        }
        
        // Laser Role - Laser Orders
        if ($user->hasRole('laser')) {
            return redirect()->route('laser.index');
        }
        
        // Salaries Role - Payroll
        if ($user->hasRole('salaries')) {
            return redirect()->route('payroll.index');
        }
        
        // Customers Role - Home (or create specific page)
        if ($user->hasRole('customers')) {
            return redirect()->route('home');
        }
        
        // Invoice Role - Invoice Page
        if ($user->hasRole('invoice')) {
            return redirect()->route('invoice.create');
        }
        
        // Inventory Role - Inventory Page
        if ($user->hasRole('inventory')) {
            return redirect()->route('inventory.index');
        }
        
        // Users Management Role
        if ($user->hasRole('users')) {
            return redirect()->route('users.index');
        }
        
        // Default redirect to home
        return redirect()->route('home');
    }
}
