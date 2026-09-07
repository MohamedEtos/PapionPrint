<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id)->where('causer_type', User::class);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', 'like', '%' . $request->subject_type . '%');
        }

        if ($request->filled('event')) {
            $event = $request->event;
            if ($event === 'created') {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%create%')
                      ->orWhere('description', 'like', '%إضافة%')
                      ->orWhere('description', 'like', '%اصافة%')
                      ->orWhere('event', 'created');
                });
            } elseif ($event === 'updated') {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%update%')
                      ->orWhere('description', 'like', '%تعديل%')
                      ->orWhere('description', 'like', '%تحديث%')
                      ->orWhere('event', 'updated');
                });
            } elseif ($event === 'deleted') {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%delete%')
                      ->orWhere('description', 'like', '%حذف%')
                      ->orWhere('event', 'deleted');
                });
            } else {
                $query->where(function ($q) use ($event) {
                    $q->where('description', 'like', '%' . $event . '%')
                      ->orWhere('event', 'like', '%' . $event . '%');
                });
            }
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $activities = $query->paginate(20)->withQueryString();
        $users = User::orderBy('name')->get();

        // Distinct subject types for filter dropdown
        $rawSubjectTypes = Activity::distinct()->pluck('subject_type')->filter()->values();
        $subjectTypes = [];
        foreach ($rawSubjectTypes as $st) {
            $subjectTypes[] = [
                'class' => $st,
                'label' => self::getModelLabel($st)
            ];
        }

        // Stats summary
        $stats = [
            'total' => Activity::count(),
            'created' => Activity::where(function ($q) {
                $q->where('description', 'like', '%create%')
                  ->orWhere('description', 'like', '%إضافة%')
                  ->orWhere('event', 'created');
            })->count(),
            'updated' => Activity::where(function ($q) {
                $q->where('description', 'like', '%update%')
                  ->orWhere('description', 'like', '%تعديل%')
                  ->orWhere('event', 'updated');
            })->count(),
            'deleted' => Activity::where(function ($q) {
                $q->where('description', 'like', '%delete%')
                  ->orWhere('description', 'like', '%حذف%')
                  ->orWhere('event', 'deleted');
            })->count(),
        ];

        return view('admin.activity_logs.index', compact('activities', 'users', 'subjectTypes', 'stats'));
    }

    public static function getModelLabel($subjectType)
    {
        if (!$subjectType) return 'عام / نظام';

        $map = [
            'User' => 'مستخدم',
            'App\Models\User' => 'مستخدم',
            'customers' => 'عميل',
            'Customer' => 'عميل',
            'App\Models\customers' => 'عميل',
            'App\Models\Customer' => 'عميل',
            'LaserOrder' => 'طلب ليزر',
            'App\Models\LaserOrder' => 'طلب ليزر',
            'LaserMaterial' => 'خامات ليزر',
            'App\Models\LaserMaterial' => 'خامات ليزر',
            'LaserPrice' => 'أسعار ليزر',
            'App\Models\LaserPrice' => 'أسعار ليزر',
            'Rollpress' => 'طلب رول بريس',
            'App\Models\Rollpress' => 'طلب رول بريس',
            'Tarter' => 'طلب ترتر',
            'App\Models\Tarter' => 'طلب ترتر',
            'TarterLayer' => 'طبقة ترتر',
            'App\Models\TarterLayer' => 'طبقة ترتر',
            'TarterPrice' => 'أسعار ترتر',
            'App\Models\TarterPrice' => 'أسعار ترتر',
            'Stras' => 'طلب ستراس',
            'App\Models\Stras' => 'طلب ستراس',
            'StrasLayer' => 'طبقة ستراس',
            'App\Models\StrasLayer' => 'طبقة ستراس',
            'StrasPrice' => 'أسعار ستراس',
            'App\Models\StrasPrice' => 'أسعار ستراس',
            'Printers' => 'طابعة',
            'App\Models\Printers' => 'طابعة',
            'Printerlogs' => 'سجل طابعة',
            'App\Models\Printerlogs' => 'سجل طابعة',
            'Printingprices' => 'أسعار طباعة',
            'App\Models\Printingprices' => 'أسعار طباعة',
            'Invoice' => 'فاتورة',
            'App\Models\Invoice' => 'فاتورة',
            'InvoiceItem' => 'بند فاتورة',
            'App\Models\InvoiceItem' => 'بند فاتورة',
            'InvoiceArchive' => 'أرشيف فاتورة',
            'App\Models\InvoiceArchive' => 'أرشيف فاتورة',
            'Machines' => 'ماكينة',
            'App\Models\Machines' => 'ماكينة',
            'InventoryLog' => 'سجل المخزون',
            'App\Models\InventoryLog' => 'سجل المخزون',
            'Stock' => 'خامة / صنف مخزون',
            'App\Models\Stock' => 'خامة / صنف مخزون',
            'EmployeeLoan' => 'سلفة موظف',
            'App\Models\EmployeeLoan' => 'سلفة موظف',
            'Attendance' => 'حضور وانصراف',
            'App\Models\Attendance' => 'حضور وانصراف',
            'BiometricAttendance' => 'سجل بصمة',
            'App\Models\BiometricAttendance' => 'سجل بصمة',
            'BiometricUser' => 'مستخدم بصمة',
            'App\Models\BiometricUser' => 'مستخدم بصمة',
            'Setting' => 'إعدادات النظام',
            'App\Models\Setting' => 'إعدادات النظام',
            'ErrorLog' => 'سجل خطأ',
            'App\Models\ErrorLog' => 'سجل خطأ',
            'CompositeItem' => 'صنف مركب',
            'App\Models\CompositeItem' => 'صنف مركب',
        ];

        if (isset($map[$subjectType])) {
            return $map[$subjectType];
        }

        $basename = class_basename($subjectType);
        return $map[$basename] ?? $basename;
    }

    public static function getEventInfo($activity)
    {
        $evt = strtolower($activity->event ?? $activity->description ?? '');

        if (str_contains($evt, 'create') || str_contains($evt, 'إضافة') || str_contains($evt, 'اضافة')) {
            return [
                'type' => 'created',
                'label' => 'إضافة',
                'badge' => 'badge-success',
                'badge_light' => 'badge-light-success',
                'icon' => 'feather icon-plus-circle',
                'color' => '#28c76f'
            ];
        }

        if (str_contains($evt, 'update') || str_contains($evt, 'تعديل') || str_contains($evt, 'تحديث')) {
            return [
                'type' => 'updated',
                'label' => 'تعديل',
                'badge' => 'badge-warning',
                'badge_light' => 'badge-light-warning',
                'icon' => 'feather icon-edit-2',
                'color' => '#ff9f43'
            ];
        }

        if (str_contains($evt, 'delete') || str_contains($evt, 'حذف') || str_contains($evt, 'destroy')) {
            return [
                'type' => 'deleted',
                'label' => 'حذف',
                'badge' => 'badge-danger',
                'badge_light' => 'badge-light-danger',
                'icon' => 'feather icon-trash-2',
                'color' => '#ea5455'
            ];
        }

        if (str_contains($evt, 'restore') || str_contains($evt, 'استرجاع')) {
            return [
                'type' => 'restored',
                'label' => 'استرجاع',
                'badge' => 'badge-primary',
                'badge_light' => 'badge-light-primary',
                'icon' => 'feather icon-rotate-ccw',
                'color' => '#7367f0'
            ];
        }

        return [
            'type' => 'event',
            'label' => $activity->description ?: 'حدث عام',
            'badge' => 'badge-info',
            'badge_light' => 'badge-light-info',
            'icon' => 'feather icon-activity',
            'color' => '#00cfdd'
        ];
    }

    public static function getSubjectDetails($activity)
    {
        $modelName = self::getModelLabel($activity->subject_type);

        if (!$activity->subject_type) {
            return [
                'model_name' => 'نظام',
                'identifier' => 'عام',
                'id' => null,
                'is_deleted' => false,
                'full_text' => 'الأنشطة العامة للنظام'
            ];
        }

        $subject = $activity->subject;

        if ($subject) {
            $identifier = $subject->name 
                ?? $subject->title 
                ?? $subject->customer_name 
                ?? $subject->code 
                ?? $subject->order_number 
                ?? $subject->id;

            return [
                'model_name' => $modelName,
                'identifier' => $identifier,
                'id' => $activity->subject_id,
                'is_deleted' => false,
                'full_text' => $modelName . ' (' . (is_numeric($identifier) ? '#' . $identifier : $identifier) . ')'
            ];
        }

        return [
            'model_name' => $modelName,
            'identifier' => '#' . $activity->subject_id,
            'id' => $activity->subject_id,
            'is_deleted' => true,
            'full_text' => $modelName . ' (محذوف ID: ' . $activity->subject_id . ')'
        ];
    }

    public static function getFieldLabel($key)
    {
        $labels = [
            'id' => 'المعرف (ID)',
            'name' => 'الاسم',
            'title' => 'العنوان / الاسم',
            'customer_name' => 'اسم العميل',
            'user_id' => 'رقم المستخدم',
            'customer_id' => 'رقم العميل',
            'printer_id' => 'رقم الطابعة',
            'machine_id' => 'رقم الماكينة',
            'status' => 'الحالة',
            'price' => 'السعر',
            'total' => 'الإجمالي',
            'amount' => 'المبلغ',
            'quantity' => 'الكمية',
            'qty' => 'الكمية',
            'phone' => 'رقم الهاتف',
            'mobile' => 'رقم الجوال',
            'email' => 'البريد الإلكتروني',
            'address' => 'العنوان',
            'notes' => 'ملاحظات',
            'note' => 'ملاحظة',
            'description' => 'الوصف',
            'code' => 'الكود',
            'type' => 'النوع',
            'role' => 'الدور / الصلاحية',
            'roles' => 'الصلاحيات',
            'permissions' => 'الأذونات',
            'created_at' => 'تاريخ الإنشاء',
            'updated_at' => 'تاريخ التحديث',
            'deleted_at' => 'تاريخ الحذف',
            'is_active' => 'حالة التفعيل',
            'discount' => 'الخصم',
            'tax' => 'الضريبة',
            'subtotal' => 'المجموع الفرعي',
            'paid' => 'المبلغ المدفوع',
            'remaining' => 'المتبقي',
            'payment_method' => 'طريقة الدفع',
            'file' => 'الملف',
            'image' => 'الصورة',
            'width' => 'العرض',
            'height' => 'الارتفاع',
            'length' => 'الطول',
            'unit' => 'الوحدة',
            'color' => 'اللون',
            'size' => 'المقاس',
            'count' => 'العدد',
        ];

        return $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
    }

    public static function formatChanges($activity)
    {
        $props = $activity->properties;

        if (!$props) {
            return ['type' => 'none', 'rows' => []];
        }

        $old = $props['old'] ?? [];
        $attributes = $props['attributes'] ?? [];

        if (empty($old) && empty($attributes)) {
            if (is_array($props) || is_object($props)) {
                $flat = array_diff_key((array)$props, array_flip(['old', 'attributes']));
                if (!empty($flat)) {
                    $attributes = $flat;
                }
            }
        }

        $rows = [];

        if (!empty($old) && !empty($attributes)) {
            $allKeys = array_unique(array_merge(array_keys($old), array_keys($attributes)));
            foreach ($allKeys as $key) {
                if ($key === 'updated_at') continue;

                $oldVal = $old[$key] ?? null;
                $newVal = $attributes[$key] ?? null;

                if ($oldVal != $newVal) {
                    $rows[] = [
                        'field' => self::getFieldLabel($key),
                        'raw_key' => $key,
                        'old' => self::formatValue($oldVal),
                        'new' => self::formatValue($newVal),
                        'status' => 'changed'
                    ];
                }
            }
            return ['type' => 'update', 'rows' => $rows];
        } elseif (!empty($attributes)) {
            foreach ($attributes as $key => $val) {
                if ($key === 'created_at' || $key === 'updated_at') continue;
                $rows[] = [
                    'field' => self::getFieldLabel($key),
                    'raw_key' => $key,
                    'old' => null,
                    'new' => self::formatValue($val),
                    'status' => 'added'
                ];
            }
            return ['type' => 'create', 'rows' => $rows];
        } elseif (!empty($old)) {
            foreach ($old as $key => $val) {
                $rows[] = [
                    'field' => self::getFieldLabel($key),
                    'raw_key' => $key,
                    'old' => self::formatValue($val),
                    'new' => null,
                    'status' => 'deleted'
                ];
            }
            return ['type' => 'delete', 'rows' => $rows];
        }

        return ['type' => 'none', 'rows' => []];
    }

    private static function formatValue($val)
    {
        if (is_null($val)) {
            return '—';
        }
        if (is_bool($val)) {
            return $val ? 'نعم' : 'لا';
        }
        if (is_array($val) || is_object($val)) {
            return json_encode($val, JSON_UNESCAPED_UNICODE);
        }
        return (string) $val;
    }

    public static function getItemNavigation($activity)
    {
        $subjectType = $activity->subject_type;
        $subjectId = $activity->subject_id;
        $subject = $activity->subject;

        $url = null;
        $location = 'النظام العام';
        $canView = true;

        if (!$subjectType) {
            return [
                'url' => null,
                'location' => 'أنشطة النظام العامة',
                'can_view' => false
            ];
        }

        $basename = class_basename($subjectType);

        try {
            switch ($basename) {
                case 'LaserOrder':
                    $location = 'الطلبات > قسم الليزر (Laser)';
                    if ($subjectId && \Route::has('laser.show')) {
                        $url = route('laser.show', $subjectId);
                    } else {
                        $url = route('laser.index');
                    }
                    break;

                case 'Stras':
                case 'StrasLayer':
                    $location = 'الطلبات > قسم الاستراس (Stras)';
                    if ($subjectId && \Route::has('stras.show')) {
                        $url = route('stras.show', $subjectId);
                    } else {
                        $url = route('stras.index');
                    }
                    break;

                case 'Tarter':
                case 'TarterLayer':
                    $location = 'الطلبات > قسم الترتر (Tarter)';
                    if ($subjectId && \Route::has('tarter.show')) {
                        $url = route('tarter.show', $subjectId);
                    } else {
                        $url = route('tarter.index');
                    }
                    break;

                case 'Rollpress':
                    $location = 'الطلبات > قسم المكبس (Rollpress)';
                    $url = route('presslist');
                    break;

                case 'Printers':
                    $location = 'الطلبات > قسم الطباعة (Printers)';
                    if ($subjectId && \Route::has('printers.show')) {
                        $url = route('printers.show', $subjectId);
                    } else {
                        $url = route('AddPrintOrders');
                    }
                    break;

                case 'Printerlogs':
                    $location = 'الطباعة > سجل أوامر الطباعة';
                    $url = route('print_log');
                    break;

                case 'Invoice':
                case 'InvoiceItem':
                case 'InvoiceArchive':
                    $location = 'الفواتير > أرشيف ومراجعة الفواتير';
                    $url = route('invoice.history');
                    break;

                case 'customers':
                case 'Customer':
                    $location = 'العملاء > ملف وقائمة العملاء';
                    if ($subjectId && \Route::has('customers.show')) {
                        $url = route('customers.show', $subjectId);
                    } else {
                        $url = route('customers.index');
                    }
                    break;

                case 'User':
                    $location = 'الإدارة > إدارة المستخدمين والصلاحيات';
                    $url = route('users.index');
                    break;

                case 'InventoryLog':
                case 'Stock':
                    $location = 'المخزن > حركة ومخزون الخامات والإنك';
                    $url = route('inventory.index');
                    break;

                case 'Machines':
                    $location = 'الفواتير > أسعار وتسعير الماكينات';
                    $url = route('machines.pricing');
                    break;

                case 'Attendance':
                case 'BiometricAttendance':
                case 'BiometricUser':
                case 'EmployeeLoan':
                    $location = 'الرواتب > سجل البصمة والحضور والسلف';
                    $url = route('biometric.index');
                    break;

                case 'Setting':
                    $location = 'الإدارة > إعدادات النظام العامة';
                    $url = route('settings.index');
                    break;

                case 'ErrorLog':
                    $location = 'التقارير > سجل أخطاء النظام';
                    $url = route('admin.error_logs.index');
                    break;

                case 'LaserPrice':
                    $location = 'الليزر > أسعار الليزر';
                    $url = route('laser.pricing');
                    break;

                case 'StrasPrice':
                    $location = 'الاستراس > أسعار الاستراس';
                    $url = route('stras.pricing');
                    break;

                case 'TarterPrice':
                    $location = 'الترتر > أسعار الترتر';
                    $url = route('tarter.pricing');
                    break;

                default:
                    $location = 'قسم النظام (' . $basename . ')';
                    $url = null;
                    break;
            }
        } catch (\Exception $e) {
            $url = null;
        }

        if (!$subject && in_array($basename, ['LaserOrder', 'Stras', 'Tarter', 'Printers', 'Customer'])) {
            if ($basename === 'LaserOrder') $url = route('laser.index');
            elseif ($basename === 'Stras') $url = route('stras.index');
            elseif ($basename === 'Tarter') $url = route('tarter.index');
            elseif ($basename === 'Printers') $url = route('AddPrintOrders');
            elseif ($basename === 'Customer') $url = route('customers.index');

            $canView = false;
        }

        return [
            'url' => $url,
            'location' => $location,
            'can_view' => $canView
        ];
    }
}


