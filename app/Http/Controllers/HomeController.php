<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    use \App\Traits\RoleRedirectTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:الاحصائيات']);
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $todayAttendance = Attendance::where('user_id', Auth::id())
            ->where('date', Carbon::today())
            ->first();

        return view('home', compact('todayAttendance'));
    }

    /**
     * Redirect to the appropriate dashboard based on user role.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dashboard()
    {
        return redirect($this->getRedirectPath());
    }
}
