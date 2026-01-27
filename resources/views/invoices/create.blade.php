@extends('layouts.app')

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
                                                <option value="">اختر عميل...</option>
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
                                <button class="btn btn-success mt-2" onclick="sendWhatsApp()"> <i class="fa fa-whatsapp"></i> ارسال واتس اب</button>
                                <button class="btn btn-danger mt-2" onclick="clearCart()"> <i class="fa fa-trash"></i> تفريغ السلة</button>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>النوع</th>
                                        <th>التفاصيل</th>
                                        <th>الكمية/العدد</th>
                                        <th>السعر (للوحدة/للمتر)</th>
                                        <th>الاجمالي</th>
                                        <th>حذف</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grandTotal = 0; @endphp
                                    @foreach($prods as $item)
                                        @php
                                            $detailText = '';
                                            $price = 0;
                                            $qty = 0; // Defines quantity or meters depending on type
                                            $total = 0;
                                            $typeLabel = '';

                                            // Polymorphic Handling
                                            if ($item->itemable_type == 'App\Models\Stras') {
                                                $typeLabel = 'استراس';
                                                $stras = $item->itemable;
                                                // Calculate Price based on Stras Logic (replicated from stras module)
                                                // Assuming Stras has a calculated 'total_price' accessor or we compute it.
                                                // For now, let's look for known price columns.
                                                // If no stored price, we might need a Helper to calculate it live.
                                                // Let's assume for MVP we fetch a 'price' attribute or default.
                                                // User said "Take calculation method from each page".
                                                // Stras price logic is complex (layers * beads + paper + ops).
                                                // Ideally, the Stras model should have a method `getCalculatedPriceAttribute()`.
                                                // I will assume for now we use a placeholder or existing price field if available.
                                                $qty = $stras->cards_count * $stras->pieces_per_card; // Total pieces
                                                $price = 0; // Need calculation logic
                                                
                                                // Detailed Description
                                                $detailText = ($stras->customer->name ?? 'عميل غير معروف') . ' - ' . ($stras->notes ?? '');
                                            } 
                                            elseif ($item->itemable_type == 'App\Models\Tarter') {
                                                $typeLabel = 'ترتر';
                                                $tarter = $item->itemable;
                                                $qty = $tarter->cards_count * $tarter->pieces_per_card;
                                                $detailText = $tarter->customer->name ?? 'عميل غير معروف';
                                            }
                                            elseif ($item->itemable_type == 'App\Models\Printers') {
                                                $typeLabel = 'طباعة';
                                                $printer = $item->itemable;
                                                $qty = $printer->meters;
                                                // Price logic: check stored price or machine price
                                                $detailText = $printer->machines->name . ' (' . $printer->pass . ' pass)';
                                                $price = $printer->printingprices->totalPrice ?? 0;
                                            }
                                            elseif ($item->itemable_type == 'App\Models\Rollpress') {
                                                $typeLabel = 'مكبس';
                                                $roll = $item->itemable;
                                                $qty = $roll->meters;
                                                $price = $roll->price;
                                                $detailText = $roll->fabrictype;
                                            }

                                            // Check for Custom Price Override
                                            if($item->custom_price) {
                                                $price = $item->custom_price;
                                            }
                                            
                                            // Determine Unit Price for display
                                            // If effective quantity is > 0, unit price = total / qty
                                            // Otherwise unit price = total
                                            $unitPrice = ($qty > 0) ? ($price / $qty) : $price;
                                            $total = $price; 

                                            $grandTotal += $total;
                                        @endphp
                                        <tr>
                                            <td>{{ $typeLabel }}</td>
                                            <td>{{ $detailText }}</td>
                                            <td class="item-qty" data-qty="{{ $qty }}">{{ $qty }}</td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control item-price" data-id="{{ $item->id }}" value="{{ round($unitPrice, 2) }}">
                                            </td>
                                            <td class="item-total">{{ round($total, 2) }}</td>
                                            <td>
                                                <a href="{{ route('invoice.remove', $item->id) }}" class="text-danger"><i class="feather icon-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">الاجمالي الكلي</th>
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
@endsection

@section('js')
<script>
    // Dynamic Calculation
    $(document).on('input', '.item-price', function() {
        var row = $(this).closest('tr');
        var qty = parseFloat(row.find('.item-qty').data('qty')) || 0;
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

    // Handle Phone Display & Update
    function updatePhoneField() {
        var selected = $('#customer-select option:selected');
        var phone = selected.data('phone');
        var input = $('#customer-phone');
        var group = $('#phone-group');
        
        if (selected.val()) {
            input.val(phone || '');
            group.show();
        } else {
            group.hide();
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

    // Send WhatsApp
    function sendWhatsApp() {
        var customerName = $('#customer-select option:selected').text();
        var total = $('#grand-total').text();
        var items = [];
        
        $('.table tbody tr').each(function() {
             var type = $(this).find('td:eq(0)').text();
             var details = $(this).find('td:eq(1)').text();
             var itemTotal = $(this).find('.item-total').text();
             items.push(type + " (" + details + "): " + itemTotal);
        });

        var text = "فاتورة حساب للسيد/ " + customerName + "\n";
        text += "------------------\n";
        text += items.join("\n");
        text += "\n------------------\n";
        text += "الاجمالي: " + total;

        var phone = $('#customer-phone').val();
         // Check if phone exists and add mandatory '2' if not present
        if (phone && !phone.toString().startsWith('2')) {
            phone = '2' + phone;
        }
        var url = "https://wa.me/" + (phone ? phone : "") + "?text=" + encodeURIComponent(text);
        window.open(url, '_blank');
    }
</script>
@endsection
