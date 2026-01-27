@extends('layouts.app')

@section('css')
    @vite([
        'resources/core/css-rtl/core/menu/menu-types/vertical-menu.css',
        'resources/core/css-rtl/core/colors/palette-gradient.css',
        'resources/core/css-rtl/custom-rtl.css',
    ]) 
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
             <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">إعدادات أسعار الترتر</h2>
                             <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('tarter.index') }}"> الترتر</a>
                                    </li>
                                    <li class="breadcrumb-item active"> التسعير
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
             </div>
        </div>

        <div class="content-body">
            <div class="row">
                <!-- Needle Prices -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">اسعار مقاسات الإبر</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>المقاس</th>
                                            <th>السعر</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($needlePrices as $price)
                                        <tr>
                                            <td>{{ $price->size }}</td>
                                            <td>
                                                <input type="number" step="0.001" class="form-control price-input" data-id="{{ $price->id }}" value="{{ $price->price }}">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paper Prices -->
                <div class="col-md-4">
                     <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">اسعار الورق</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>النوع/المقاس</th>
                                            <th>السعر</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($paperPrices as $price)
                                        <tr>
                                            <td>{{ $price->size }}</td>
                                            <td>
                                                <input type="number" step="0.001" class="form-control price-input" data-id="{{ $price->id }}" value="{{ $price->price }}">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Operating Costs & Machine Time -->
                <div class="col-md-4">
                     <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">تكاليف التشغيل والماكينة</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>البند</th>
                                            <th>التكلفة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($otherPrices as $price)
                                        <tr>
                                            <td>
                                                @if($price->type == 'global' && $price->size == 'operating_cost')
                                                    تشغيل (ثابت)
                                                @elseif($price->type == 'machine_time_cost')
                                                    وقت الماكينة (للدقيقة)
                                                @else
                                                    {{ $price->size }}
                                                @endif
                                            </td>
                                            <td>
                                                <input type="number" step="0.001" class="form-control price-input" data-id="{{ $price->id }}" value="{{ $price->price }}">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="row">
                <div class="col-12 text-right">
                    <button id="save-all-btn" class="btn btn-primary">حفظ الكل</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Auto save on change or save button
             $('.price-input').on('change', function() {
                 updatePrice($(this));
             });

             function updatePrice(input) {
                 var id = input.data('id');
                 var val = input.val();
                 
                 $.ajax({
                     url: '{{ route("tarter.update_price") }}',
                     type: 'POST',
                     data: {
                         id: id,
                         price: val,
                         _token: '{{ csrf_token() }}'
                     },
                     success: function(response) {
                         toastr.success('تم تحديث السعر بنجاح', 'تم الحفظ');
                         
                         // Success Animation
                         input.addClass('is-valid');
                         input.css('background-color', '#d4edda'); // Light green bg
                         setTimeout(function() {
                             // input.removeClass('is-valid'); // Keep checkmark
                             input.css('background-color', '');
                         }, 1500);
                     },
                     error: function() {
                         toastr.error('حدث خطأ', 'خطأ');
                     }
                 });
             }
        });
    </script>
@endsection
