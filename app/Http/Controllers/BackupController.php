<?php

namespace App\Http\Controllers;

use App\Mail\BackupMail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin']);
    }

    public function index()
    {
        $backups = [];
        $path = storage_path('app/private/Laravel');

        if (File::exists($path)) {
            $files = File::allFiles($path);
            foreach ($files as $file) {
                if ($file->getExtension() === 'zip') {
                    $backups[] = [
                        'file_name' => $file->getFilename(),
                        'file_size' => $this->formatBytes($file->getSize()),
                        'last_modified' => date('Y-m-d H:i:s', $file->getMTime()),
                    ];
                }
            }
        }

        // Sort backups by last modified desc
        usort($backups, function ($a, $b) {
            return strcmp($b['last_modified'], $a['last_modified']);
        });

        return view('backups.index', compact('backups'));
    }

    public function create()
    {
        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            return redirect()->back()->with('success', 'تم إنشاء النسخة الاحتياطية بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'فشل إنشاء النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    public function download($file_name)
    {
        $path = storage_path('app/private/Laravel/' . $file_name);

        if (File::exists($path)) {
            return response()->download($path);
        }

        return redirect()->back()->with('error', 'الملف غير موجود');
    }

    public function destroy($file_name)
    {
        $path = storage_path('app/private/Laravel/' . $file_name);

        if (File::exists($path)) {
            File::delete($path);
            return redirect()->back()->with('success', 'تم حذف النسخة الاحتياطية بنجاح');
        }

        return redirect()->back()->with('error', 'الملف غير موجود');
    }

    public function sendEmail($file_name)
    {
        $path = storage_path('app/private/Laravel/' . $file_name);

        if (!File::exists($path)) {
            return redirect()->back()->with('error', 'الملف غير موجود');
        }

        $setting = Setting::first();
        $emailsStr = $setting->inventory_alert_emails ?? '';
        $emails = array_filter(array_map('trim', explode(',', $emailsStr)));

        if (empty($emails)) {
            return redirect()->back()->with('error', 'لم يتم ضبط إيميلات في الإعدادات (إيميلات تنبيه المخزن)');
        }

        try {
            $subject = 'النسخة الاحتياطية لقاعدة البيانات - ' . date('Y-m-d');
            $body = "مرفق النسخة الاحتياطية لقاعدة البيانات بتاريخ " . date('Y-m-d H:i:s');
            
            Mail::to($emails)->send(new BackupMail($subject, $body, $path, $file_name));

            return redirect()->back()->with('success', 'تم إرسال النسخة الاحتياطية بنجاح إلى: ' . implode(', ', $emails));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'فشل إرسال الإيميل: ' . $e->getMessage());
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
