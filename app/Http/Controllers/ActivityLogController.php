<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer', 'subject')->orderBy('created_at', 'desc');

        if ($request->has('user_id') && $request->user_id) {
            $query->where('causer_id', $request->user_id)->where('causer_type', User::class);
        }

        if ($request->has('subject_type') && $request->subject_type) {
            $query->where('subject_type', 'like', '%' . $request->subject_type . '%');
        }

        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $activities = $query->paginate(20);
        $users = User::all();

        return view('admin.activity_logs.index', compact('activities', 'users'));
    }
}
