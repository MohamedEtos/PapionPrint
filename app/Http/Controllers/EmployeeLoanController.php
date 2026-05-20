<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLoan;
use Illuminate\Http\Request;

class EmployeeLoanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:الرواتب');
    }

    public function store(Request $request)
    {
        $request->validate([
            'biometric_user_id' => 'required|exists:biometric_users,id',
            'amount'            => 'required|numeric|min:1',
            'month'             => 'required|integer|min:1|max:12',
            'year'              => 'required|integer|min:2020|max:2099',
            'notes'             => 'nullable|string|max:500',
        ]);

        EmployeeLoan::create([
            'biometric_user_id' => $request->biometric_user_id,
            'amount'            => $request->amount,
            'month'             => $request->month,
            'year'              => $request->year,
            'notes'             => $request->notes,
        ]);

        return back()->with('success', 'تم إضافة السلفة بنجاح وسيتم خصمها من الراتب.');
    }

    public function destroy($id)
    {
        $loan = EmployeeLoan::findOrFail($id);
        $loan->delete();

        return back()->with('success', 'تم حذف السلفة بنجاح.');
    }
}
