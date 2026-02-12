@extends('layouts.app')
@section('css')
    @vite([
        'resources/core/vendors/css/tables/datatable/datatables.min.css',
        'resources/core/vendors/css/tables/datatable/extensions/dataTables.checkboxes.css',
        'resources/core/css-rtl/core/menu/menu-types/vertical-menu.css',
        'resources/core/css-rtl/core/colors/palette-gradient.css',
        'resources/core/css-rtl/plugins/file-uploaders/dropzone.css',
        'resources/core/css-rtl/pages/data-list-view.css',
        'resources/core/css-rtl/custom-rtl.css',
        'resources/core/vendors/css/file-uploaders/dropzone.min.css',
    ])
@endsection 
@section('content')
<style>
    @keyframes flash-green {
        0% { background-color: #28c76f; color: white; }
        50% { background-color: rgba(40, 199, 111, 0.5); color: white; }
        100% { background-color: white; color: inherit; }
    }
    .flash-input {
        animation: flash-green 1s ease-out;
    }
    .borderless-input {
        border: none !important;
        background: transparent !important;
        padding: 8px;
        width: 100%;
        text-align: center;
    }
    .borderless-input:focus {
        border: 1px solid #7367F0 !important;
        background: #f8f8f8 !important;
        outline: none;
        border-radius: 4px;
    }
</style>
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                 <h2 class="content-header-title float-left mb-0">فاتورة حساب مجمعة</h2>
            </div>
        </div>
        <div class="content-body">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">بيانات الفاتورة</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <!-- Customer Selection -->
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>العميل</label>
                                            <select class="form-control" id="customer-select">
                                                <option selected value="اختر عميل...">اختر عميل...</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}" data-phone="{{ $customer->phone }}" {{ $invoice->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" id="phone-group" style="display:none;">
                                            <label>رقم الواتساب</label>
                                            <input type="text" id="customer-phone" class="form-control" placeholder="واتساب" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-6 text-right">
                                <button class="btn btn-purple mt-2" data-toggle="modal" data-target="#compositeItemModal"> <i class="fa fa-plus-square"></i> إضافة فاتوره</button>
                                <button class="btn btn-success mt-2" id="send-whatsapp-btn" onclick="sendWhatsApp()"> <i class="fa fa-whatsapp"></i> ارسال واتس اب</button>
                                <button class="btn btn-warning mt-2" id="save-invoice-changes"><i class="feather icon-save"></i> حفظ</button>
                                <button class="btn btn-danger mt-2" onclick="clearCart()"> <i class="fa fa-trash"></i> تفريغ السلة</button>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>صورة</th>
                                        <th>النوع</th>
                                        <th>التفاصيل</th>
                                        <th>الكمية/العدد</th>
                                        <th>السعر (للوحدة/للمتر)</th>
                                        <th>الاجمالي</th>
                                        <th>حذف</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        $grandTotal = 0; 
                                        $totalUnitPrice = 0;
                                    @endphp
                                    @foreach($prods as $item)
                                        @php
                                            $detailText = '';
                                            $price = 0;
                                            $qty = 0; // Defines quantity or meters depending on type
                                            $total = 0;
                                            $typeLabel = '';
                                            $unitLabel = 'قطعة'; // Default unit
                                            $linkUrl = null;

                                            // Polymorphic Handling
                                            if ($item->itemable_type == 'App\Models\Stras') {
                                                $typeLabel = 'استراس';
                                                $stras = $item->itemable;
                                                $qty = $stras->cards_count * $stras->pieces_per_card; // Total pieces
                                                $linkUrl = route('stras.show', $stras->id);
                                                $price = ($stras->manufacturing_cost ?? 0) * $qty; 
                                                
                                                // Detailed Description
                                                $layersInfo = $stras->layers->map(function($l) {
                                                    return $l->size . ':' . $l->count;
                                                })->implode(' | ');
                                                $detailText = 'مراحل: ' . $stras->layers->count() . ' - ' . $layersInfo;
                                            } 
                                            elseif ($item->itemable_type == 'App\Models\Tarter') {
                                                $typeLabel = 'ترتر';
                                                $tarter = $item->itemable;
                                                $qty = $tarter->cards_count * $tarter->pieces_per_card;
                                                $linkUrl = route('tarter.show', $tarter->id);
                                                $price = ($tarter->manufacturing_cost ?? 0);
                                                
                                                // Detailed Description
                                                $layersInfo = $tarter->layers->map(function($l) {
                                                    return $l->size . ':' . $l->count;
                                                })->implode(' | ');
                                                $detailText = 'ابر: ' . $tarter->layers->count() . ' - ' . $layersInfo;
                                            }
                                            elseif ($item->itemable_type == 'App\Models\Printers') {
                                                $typeLabel = 'طباعة';
                                                $unitLabel = ($item->unit_type === 'piece') ? 'قطعة' : 'متر';
                                                $printer = $item->itemable;
                                                
                                                if (!$printer) {
                                                    // Handle case where printer is deleted or not found
                                                    $qty = 0;
                                                    $price = 0;
                                                    $detailText = 'Item Deleted';
                                                    // Skip or show error
                                                    continue; 
                                                }
                                                
                                                if ($item->unit_type === 'piece') {
                                                    $files = $printer->fileCopies ?? 1;
                                                    $pics = $printer->picInCopies ?? 1;
                                                    $qty = $files * $pics;
                                                } else {
                                                    $qty = $printer->meters ; // Default to meters
                                                }
                                                $linkUrl = route('printers.show', $printer->id);
                                                // Price logic: check stored price or machine price
                                                $detailText = $printer->machines->name . ' (' . $printer->pass . ' pass)';
                                                
                                                // Calculate Unit Price based on pass
                                                $machine = $printer->machines;
                                                $uPrice = 0;
                                                if($printer->pass == 1) $uPrice = $machine->price_1_pass;
                                                elseif($printer->pass == 4) $uPrice = $machine->price_4_pass;
                                                elseif($printer->pass == 6) $uPrice = $machine->price_6_pass;
                                                
                                                $price = $uPrice * $qty; // Keep $price as Total for compatibility with downstream logic (line 221)
                                                // User asked to "make the price the unit price". 
                                                // If I set $price = $uPrice, line 221 ($total = $price) becomes ($total = unit). Wrong.
                                                // Unless I change downstream.
                                                // Let's change downstream logic to differentiate or handle implicit unit vs total.
                                                // Actually, best to just satisfy "make the price the unit price" implies they want to SEE unit price or use it variable?
                                                // If I set $price = $uPrice here, I MUST update line 221.
                                                
                                                $price = $uPrice; // As requested, $price is now Unit Price for printer.
                                            }
                                            elseif ($item->itemable_type == 'App\Models\Rollpress') {
                                                $typeLabel = 'مكبس';
                                                $unitLabel = 'متر';
                                                $roll = $item->itemable;
                                                $qty = $roll->meters;
                                                $price = $roll->price;
                                                $detailText = $roll->fabrictype;
                                            }
                                            elseif ($item->itemable_type == 'App\Models\LaserOrder') {
                                                $typeLabel = 'ليزر';
                                                $laser = $item->itemable;
                                                $qty = $laser->required_pieces;
                                                $linkUrl = route('laser.show', $laser->id);
                                                $price = $laser->total_cost;
                                                $detailText = $laser->material->name ?? '-';
                                                if ($laser->add_ceylon) {
                                                    $detailText .= ' - سيليكون';
                                                }
                                            }
                                            elseif ($item->itemable_type == 'App\Models\CompositeItem') {
                                                $typeLabel = 'مجمة'; // Composite
                                                $comp = $item->itemable;
                                                $qty = $item->quantity; // Use invoice item quantity
                                                
                                                if (!$comp) {
                                                    $price = 0;
                                                    $detailText = 'عنصر محذوف';
                                                } else {
                                                    $price = $comp->total_price;
                                                    $detailText = $comp->name;
                                                    
                                                    // Build detail text from components
                                                    $parts = [];
                                                    if($comp->laser_cost > 0) $parts[] = 'ليزر: ' . $comp->laser_cost;
                                                    if($comp->tarter_cost > 0) $parts[] = 'ترتر: ' . $comp->tarter_cost;
                                                    if($comp->print_cost > 0) $parts[] = 'طباعة: ' . $comp->print_cost;
                                                    if($comp->stras_cost > 0) $parts[] = 'استراس: ' . $comp->stras_cost;
                                                    if($comp->other_cost > 0) $parts[] = 'أخرى: ' . $comp->other_cost;
                                                    
                                                    if(!empty($parts)) $detailText .= ' (' . implode(' | ', $parts) . ')';
                                                }
                                            }

                                            // Image Handling
                                            $imgPath = null;
                                            if ($item->itemable_type == 'App\Models\Stras') {
                                                $imgPath = $item->itemable->image_path ?? null;
                                            } elseif ($item->itemable_type == 'App\Models\Tarter') {
                                                $imgPath = $item->itemable->image_path ?? null;
                                            } elseif ($item->itemable_type == 'App\Models\LaserOrder') {
                                                $imgPath = $item->itemable->image_path ?? null;
                                            } elseif ($item->itemable_type == 'App\Models\Printers') {
                                                $imgObj = $item->itemable->ordersImgs->first();
                                                $imgPath = $imgObj ? $imgObj->path : null;
                                            }
                                            elseif ($item->itemable_type == 'App\Models\CompositeItem') {
                                                $imgPath = null; // No image for composite yet
                                            }
                                            
                                            $imgUrl = ($imgPath && $imgPath !== '') ? asset('storage/' . $imgPath) : '';

                                            // Check for Custom Price Override
                                            if($item->custom_price) {
                                                // custom_price is stored as Unit Price
                                                $unitPrice = $item->custom_price;
                                                $total = $unitPrice * $qty;
                                            } else {
                                                // Default logic based on model type
                                                if ($item->itemable_type == 'App\Models\CompositeItem') {
                                                    // CompositeItem store Unit Price in total_price
                                                    $unitPrice = $price;
                                                    $total = $unitPrice * $qty;
                                                } else {
                                                    // Other types (Laser) return Total Price in $price
                                                    // Printer returns Unit Price in $price (modified above)
                                                    
                                                    if ($item->itemable_type == 'App\Models\Printers') {
                                                        $unitPrice = $price;
                                                        $total = $unitPrice * $qty;
                                                    } else {
                                                        $total = $price;
                                                        $unitPrice = ($qty > 0) ? ($price / $qty) : $price;
                                                    }
                                                }
                                            } 

                                            $grandTotal += $total;
                                            $totalUnitPrice += $unitPrice;
                                        @endphp
                                        <tr data-img-url="{{ $imgUrl }}">
                                            <td style="width: 60px;" class="view-details-btn cursor-pointer" data-id="{{ $item->id }}" title="عرض التفاصيل">
                                                @if($imgUrl)
                                                    <img src="{{ $imgUrl }}" alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                @else
                                                    <i class="feather icon-image font-medium-3 text-muted"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @if($linkUrl)
                                                    <a href="{{ $linkUrl }}" target="_blank" class="text-primary font-weight-bold">
                                                        {{ $typeLabel }} <i class="feather icon-external-link small"></i>
                                                    </a>
                                                @else
                                                    {{ $typeLabel }}
                                                @endif
                                            </td>
                                            <td>
                                                <input type="text" class="borderless-input item-details" data-id="{{ $item->id }}" value="{{ $item->custom_details ?? $detailText }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <input type="number" step="0.01" class="borderless-input editable-qty item-qty" data-id="{{ $item->id }}" value="{{ $qty }}" style="width: 80px;">
                                                    <span class="small text-muted mr-1">{{ $unitLabel }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="borderless-input item-price" data-id="{{ $item->id }}" value="{{ round($unitPrice, 2) }}">
                                            </td>
                                            <td class="item-total">{{ round($total, 2) }}</td>
                                            <td>
                                                <a href="{{ route('invoice.remove', $item->id) }}" class="text-danger"><i class="feather icon-trash"></i></a>
                                                @if($item->itemable_type == 'App\Models\Printers')
                                                    <a href="#" class="text-info ml-1 toggle-unit-btn" data-id="{{ $item->id }}" data-current-unit="{{ $item->unit_type ?? 'meter' }}" title="تحويل الوحدة (متر/قطعة)">
                                                        <i class="feather icon-refresh-cw"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">الاجمالي الكلي</th>
                                        <th id="total-unit-price">{{ round($totalUnitPrice, 2) }}</th>
                                        <th id="grand-total">{{ round($grandTotal, 2) }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Composite Item Modal -->
<div class="modal fade" id="compositeItemModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة فاتوره</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="composite-item-form">
                    <div class="form-group">
                        <label>الوصف / الاسم</label>
                        <input type="text" name="name" class="form-control" required placeholder="مثال: فستان سهرة موديل 1">
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>تكلفة الليزر</label>
                                <input type="number" step="0.01" name="laser_cost" class="form-control comp-cost" value="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>تكلفة الترتر</label>
                                <input type="number" step="0.01" name="tarter_cost" class="form-control comp-cost" value="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>تكلفة الطباعة</label>
                                <input type="number" step="0.01" name="print_cost" class="form-control comp-cost" value="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>تكلفة الاستراس</label>
                                <input type="number" step="0.01" name="stras_cost" class="form-control comp-cost" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>تكلفة أخرى</label>
                                <input type="number" step="0.01" name="other_cost" class="form-control comp-cost" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>سعر الوحدة الاجمالي</label>
                                <input type="number" id="comp-unit-price" class="form-control" readonly value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>الكمية</label>
                                <input type="number" step="1" name="quantity" id="comp-qty" class="form-control" value="1" min="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>السعر الكلي</label>
                                <input type="number" id="comp-total-price" class="form-control" readonly value="0">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" id="save-composite-item">إضافة للفاتورة</button>
            </div>
        </div>
    </div>
</div>

<!-- Item Details Modal -->
<div class="modal fade" id="itemDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تفاصيل الطلب</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="item-details-content">
                <div class="text-center"><i class="feather icon-loader fa-spin fa-2x"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- WhatsApp Review Modal -->
<div class="modal fade" id="whatsappPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">مراجعة رسالة الواتساب</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="whatsapp-message-preview">يمكنك تعديل الرسالة قبل الإرسال:</label>
                    <textarea class="form-control" id="whatsapp-message-preview" rows="15" style="direction: rtl;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success" id="confirm-send-whatsapp">
                    <i class="fa fa-whatsapp"></i> إرسال وتحديث الحالة
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')


        <script src="{{ asset('core/vendors/js/extensions/dropzone.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('core/vendors/js/tables/datatable/datatables.checkboxes.min.js') }}"></script>

        <script>
            var assetPath = "{{ asset('') }}";






    // Dynamic Calculation
    $(document).on('input', '.item-price', function() {
        var row = $(this).closest('tr');
        var qty = parseFloat(row.find('.item-qty').val()) || 0;
        var unitPrice = parseFloat($(this).val()) || 0;
        
        // If qty is 0 (e.g. edge case), treat Unit Price as Total
        var total = (qty > 0) ? (unitPrice * qty) : unitPrice;
        
        row.find('.item-total').text(total.toFixed(2));
        
        calculateGrandTotal();
    });

    function calculateGrandTotal() {
        var grand = 0;
        $('.item-total').each(function() {
            grand += parseFloat($(this).text()) || 0;
        });
        $('#grand-total').text(grand.toFixed(2));
    }

    // Handle Phone Display & Update & Button Validation
    function updatePhoneField() {
        var selected = $('#customer-select option:selected');
        var val = selected.val();
        var phone = selected.data('phone');
        var input = $('#customer-phone');
        var group = $('#phone-group');
        
        // Validation: Disable WhatsApp button if no customer selected
        if (!val || val === 'اختر عميل...') {
             $('#send-whatsapp-btn').prop('disabled', true);
             group.hide();
        } else {
             $('#send-whatsapp-btn').prop('disabled', false);
             input.val(phone || '');
             group.show();
        }
    }

    // Init
    updatePhoneField();

    $('#customer-select').change(function() {
        updatePhoneField();
        
        $.post("{{ route('invoice.update_customer') }}", {
            _token: "{{ csrf_token() }}",
            customer_id: $(this).val()
        }, function(res) {
            toastr.success('تم تحديث العميل');
        });
    });




    // Update Phone via AJAX
    $('#customer-phone').on('blur keypress', function(e) {
        if (e.type === 'keypress' && e.which !== 13) return; 

        var input = $(this);
        
        if (e.type === 'keypress' && e.which === 13) {
            e.preventDefault();
            input.blur(); 
            return;
        }



        
        
        // Blur logic (main save logic)
        if (e.type === 'blur') {
            var phone = input.val();
            var customerId = $('#customer-select').val();

             if(customerId) {
                 $.post("{{ route('invoice.update_customer_phone') }}", {
                    _token: "{{ csrf_token() }}",
                    customer_id: customerId,
                    phone: phone
                }, function(res) {
                    toastr.success('تم تحديث رقم الهاتف');
                    $('#customer-select option:selected').data('phone', phone);
                    
                    // Trigger Animation
                    input.removeClass('flash-input');
                    void input.get(0).offsetWidth; // Trigger reflow
                    input.addClass('flash-input');
                    
                    setTimeout(function () {
                        input.removeClass('flash-input');
                    }, 1000);
                });
            }
        }
    });

    // Clear Cart
    function clearCart() {
        if(confirm('هل انت متأكد من تفريغ السلة؟')) {
            window.location.href = "{{ route('invoice.clear') }}";
        }
    }

    // Send WhatsApp (Step 1: Preview)
    function sendWhatsApp() {
        var customerName = $('#customer-select option:selected').text();
        var total = $('#grand-total').text();
        var groupedItems = {};
        
        $('.table tbody tr').each(function() {
             var type = $(this).find('td:eq(1)').text().trim();
             var details = $(this).find('.item-details').val(); // Get value from input
             var qty = $(this).find('.editable-qty').val(); // Get value from input
             var itemTotal = $(this).find('.item-total').text();
             var imgUrl = $(this).find('td:eq(0) img').attr('src');
             
             if (!groupedItems[type]) {
                 groupedItems[type] = [];
             }
             
             var itemText = "• " + details + "\n  الكمية: " + qty + " | الاجمالي: " + itemTotal + " ج.م";
             if(imgUrl) {
                 itemText += "\n  رابط الصورة: " + imgUrl;
             }
             
             groupedItems[type].push(itemText);
        });

        var text = "*فاتورة للسيد/ة* " + customerName + "\n";
        text += "━━━━━━━━━━━━━━━━━━\n";
        
        for (var type in groupedItems) {
            text += "\n--- *" + type + "* ---\n";
            groupedItems[type].forEach(function(item) {
                text += item + "\n";
            });
        }
        
        text += "\n━━━━━━━━━━━━━━━━━━\n";
        text += "*الإجمالي:* " + total + " ج.م";

        $('#whatsapp-message-preview').val(text);
        $('#whatsappPreviewModal').modal('show');
    }

    // Confirm Send WhatsApp
    $('#confirm-send-whatsapp').click(function() {
        var text = $('#whatsapp-message-preview').val();
        var phone = $('#customer-phone').val();
        
        if (phone && !phone.toString().startsWith('2')) {
            phone = '2' + phone;
        }
        
        var url = "https://wa.me/" + (phone ? phone : "") + "?text=" + encodeURIComponent(text);
        window.open(url, '_blank');
        
        // Mark as Sent
        $.post("{{ route('invoice.mark_sent') }}", {
            _token: "{{ csrf_token() }}"
        }, function(response) {
            toastr.success('تم تحديث حالة الفاتورة الى "تم الارسال"');
            $('#whatsappPreviewModal').modal('hide');
        });
    });

    // Enter key to blur (save)
    $(document).on('keypress', '.editable-qty, .item-price, .item-details', function(e) {
        if(e.which === 13) {
            e.preventDefault();
            $(this).blur();
        }
    });

    // Auto-update invoice items on blur
    $(document).on('blur change', '.editable-qty, .item-price, .item-details', function() {
        var $input = $(this);
        var itemId = $input.data('id');
        var $row = $input.closest('tr');
        
        var quantity = $row.find('.editable-qty').val();
        var price = $row.find('.item-price').val();
        var details = $row.find('.item-details').val();
        
        // Update total for this row
        var rowTotal = (parseFloat(quantity) || 0) * (parseFloat(price) || 0);
        $row.find('.item-total').text(rowTotal.toFixed(2));
        
        // Recalculate grand total
        calculateGrandTotal();
        
        // Save to backend
        $.post('/invoices/update-item', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            item_id: itemId,
            quantity: quantity,
            custom_price: price,
            custom_details: details
        }, function(response) {
            console.log('Item updated');
            
            // Trigger Animation
            $input.removeClass('flash-input');
            void $input.get(0).offsetWidth; // Trigger reflow
            $input.addClass('flash-input');
            
            setTimeout(function () {
                $input.removeClass('flash-input');
            }, 1000);

        }).fail(function(xhr) {
            var errorMsg = 'حدث خطأ أثناء الحفظ';
            if(xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg += ': ' + xhr.responseJSON.error;
            }
            toastr.error(errorMsg);
        });
    });

    // Save all changes button
    $('#save-invoice-changes').click(function() {
        var $button = $(this);
        $button.prop('disabled', true).html('<i class="feather icon-loader"></i> جاري الحفظ...');
        
        var totalItems = $('.item-qty').length;
        var savedItems = 0;
        var hasError = false;
        
        if (totalItems === 0) {
            toastr.warning('لا توجد عناصر للحفظ');
            $button.prop('disabled', false).html('<i class="feather icon-save"></i> حفظ');
            return;
        }
        
        // Save all items
        $('tbody tr').each(function() {
            var $row = $(this);
            var itemId = $row.find('.editable-qty').data('id');
            var quantity = $row.find('.editable-qty').val();
            var price = $row.find('.item-price').val();
            var details = $row.find('.item-details').val();
            
            if (itemId) {
                $.post('/invoices/update-item', {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    item_id: itemId,
                    quantity: quantity,
                    custom_price: price,
                    custom_details: details
                }, function(response) {
                    savedItems++;
                    if (savedItems === totalItems && !hasError) {
                        toastr.success('تم حفظ جميع التعديلات (' + totalItems + ' عنصر)');
                        $button.prop('disabled', false).html('<i class="feather icon-save"></i> حفظ');
                    }
                }).fail(function(xhr) {
                    hasError = true;
                    var errorMsg = 'حدث خطأ أثناء حفظ بعض العناصر';
                    if(xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg += ': ' + xhr.responseJSON.error;
                    }
                    toastr.error(errorMsg);
                    $button.prop('disabled', false).html('<i class="feather icon-save"></i> حفظ');
                });
            }
        });
    });
    // Item Details Modal
    $(document).on('click', '.view-details-btn', function() {
        var id = $(this).data('id');
        $('#itemDetailsModal').modal('show');
        $('#item-details-content').html('<div class="text-center p-3"><i class="feather icon-loader fa-spin fa-2x"></i> جار التحميل...</div>');
        
        $.get('/invoices/item-details/' + id, function(response) {
            $('#item-details-content').html(response.html);
        }).fail(function() {
            $('#item-details-content').html('<div class="text-danger text-center p-3">حدث خطأ أثناء تحميل التفاصيل</div>');
        });
    });

    // Toggle Unit Type (Printer)
    $(document).on('click', '.toggle-unit-btn', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var itemId = $btn.data('id');
        var currentUnit = $btn.data('current-unit');
        var newUnit = (currentUnit === 'meter') ? 'piece' : 'meter';
        
        $btn.html('<i class="feather icon-loader fa-spin"></i>');
        
        $.post('/invoices/update-item', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            item_id: itemId,
            unit_type: newUnit
        }, function(response) {
            toastr.success('تم تحويل الوحدة بنجاح');
            location.reload(); 
        }).fail(function(xhr) {
            $btn.html('<i class="feather icon-refresh-cw"></i>');
            toastr.error('حدث خطأ أثناء التحويل');
        });
    });



    $(document).ready(function() {
        // Composite Item Logic
        $('.comp-cost, #comp-qty').on('input', function() {
            var laser = parseFloat($('input[name="laser_cost"]').val()) || 0;
            var tarter = parseFloat($('input[name="tarter_cost"]').val()) || 0;
            var print = parseFloat($('input[name="print_cost"]').val()) || 0;
            var stras = parseFloat($('input[name="stras_cost"]').val()) || 0;
            var other = parseFloat($('input[name="other_cost"]').val()) || 0;
            
            var unitPrice = laser + tarter + print + stras + other;
            $('#comp-unit-price').val(unitPrice.toFixed(2));
            
            var qty = parseFloat($('#comp-qty').val()) || 0;
            var total = unitPrice * qty;
            $('#comp-total-price').val(total.toFixed(2));
        });

        $('#save-composite-item').click(function() {
            var $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> جاري الإضافة...');
            
            var data = $('#composite-item-form').serialize();
            data += '&_token={{ csrf_token() }}';
            
            $.post('/invoices/add-composite-item', data, function(response) {
                toastr.success('تمت إضافة القطعة بنجاح');
                $('#compositeItemModal').modal('hide');
                // Reset form
                $('#composite-item-form')[0].reset();
                $('#comp-unit-price').val(0);
                $('#comp-total-price').val(0);
                
                location.reload(); 
                
            }).fail(function(xhr) {
                $btn.prop('disabled', false).html('إضافة للفاتورة');
                var errorMsg = 'حدث خطأ';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg += ': ' + xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
            });
        });
    });
</script>
@endsection
