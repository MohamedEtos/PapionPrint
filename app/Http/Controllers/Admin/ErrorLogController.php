<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use Illuminate\Http\Request;

class ErrorLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:تقارير الاخطاء');
    }
    public function index()
    {
        $logs = ErrorLog::latest()->paginate(20);
        return view('admin.error_logs.index', compact('logs'));
    }

    public function show($id)
    {
        $log = ErrorLog::findOrFail($id);
        return view('admin.error_logs.show', compact('log'));
    }

    public function destroy($id)
    {
        $log = ErrorLog::findOrFail($id);
        $log->delete();

        return redirect()->route('admin.error_logs.index')->with('success', 'Error log deleted successfully.');
    }

    public function destroyAll()
    {
        ErrorLog::truncate();
        return redirect()->route('admin.error_logs.index')->with('success', 'All error logs have been deleted.');
    }
}
