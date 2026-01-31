<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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
}
