<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super-admin');
    }
    public function index()
    {
        $setting = Setting::first();
        if (!$setting) {
            $setting = Setting::create([
                'site_name' => 'Papion System',
                'primary_color' => '#7367F0',
                'secondary_color' => '#EA5455',
            ]);
        }
        return view('settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'success_color' => 'required|string|max:7',
            'danger_color' => 'required|string|max:7',
            'warning_color' => 'required|string|max:7',
            'info_color' => 'required|string|max:7',
            'dark_color' => 'required|string|max:7',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'inventory_alert_emails' => 'nullable|string',
        ]);

        $setting = Setting::first();
        
        $data = $request->only([
            'site_name', 
            'primary_color', 
            'secondary_color',
            'success_color',
            'danger_color',
            'warning_color',
            'info_color',
            'dark_color',
            'inventory_alert_emails'
        ]);

        if ($request->hasFile('site_logo')) {
            $imageName = time().'.'.$request->site_logo->extension();  
            $request->site_logo->move(public_path('images'), $imageName);
            $data['site_logo'] = 'images/'.$imageName;
        }

        $setting->update($data);

        return redirect()->back()->with('success', 'تم حفظ الاعدادات بنجاح');
    }
}
