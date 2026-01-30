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
                            <h2 class="content-header-title float-left mb-0">إعدادات أسعار الليزر</h2>
                             <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('laser.index') }}"> الليزر</a>
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
                <!-- Laser Materials Prices -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">اسعار خامات الليزر / متر</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>الخامة</th>
                                            <th>السعر (للمتر)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($materials as $material)
                                        <tr>
                                            <td>{{ $material->name }}</td>
                                            <td>
                                                <input type="number" step="0.001" class="form-control price-input" data-type="material" data-id="{{ $material->id }}" data-name="{{ $material->name }}" value="{{ $material->price }}">
                                            </td>
                                        </tr>
                                        @endforeach
                                        {{-- Add New Material Row - Simplified --}}
                                        <tr>
                                            <td><input type="text" class="form-control new-material-name" placeholder="خامة جديدة"></td>
                                            <td>
                                                <input type="number" step="0.001" class="form-control new-material-price" placeholder="السعر">
                                                <button class="btn btn-sm btn-success mt-1 add-material-btn"><i class="feather icon-plus"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Global Costs -->
                <div class="col-md-6">
                     <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">تكاليف التشغيل والإضافات</h4>
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
                                        @foreach($globalPrices as $price)
                                        <tr>
                                            <td>
                                                @if($price->name == 'operating_cost') تكلفة التشغيل (للقطعة)
                                                @elseif($price->name == 'ceylon_price') سعر السيلون (للمتر)
                                                @else {{ $price->name }} @endif
                                            </td>
                                            <td>
                                                <input type="number" step="0.001" class="form-control price-input" data-type="global" data-id="{{ $price->id }}" data-name="{{ $price->name }}" value="{{ $price->price }}">
                                            </td>
                                        </tr>
                                        @endforeach
                                        
                                        @if($globalPrices->doesntContain('name', 'operating_cost'))
                                            <tr>
                                                <td>تكلفة التشغيل (للقطعة)</td>
                                                <td><input type="number" class="form-control price-input" data-type="global" data-id="" data-name="operating_cost" value="0"></td>
                                            </tr>
                                        @endif
                                        @if($globalPrices->doesntContain('name', 'ceylon_price'))
                                             <tr>
                                                <td>سعر السيلون (للمتر)</td>
                                                <td><input type="number" class="form-control price-input" data-type="global" data-id="" data-name="ceylon_price" value="0"></td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="row">
                <div class="col-12 text-right">
                    {{-- <button id="save-all-btn" class="btn btn-primary">حفظ الكل</button> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Auto save on change
             $(document).on('change', '.price-input', function() {
                 updatePrice($(this));
             });

             $('.add-material-btn').on('click', function() {
                 let name = $('.new-material-name').val();
                 let price = $('.new-material-price').val();
                 if(name && price) {
                     $.ajax({
                         url: '{{ route("laser.update_price") }}',
                         type: 'POST',
                         data: {
                             type: 'material',
                             name: name,
                             price: price,
                             _token: '{{ csrf_token() }}'
                         },
                         success: function() {
                             location.reload();
                         }
                     });
                 }
             });

             function updatePrice(input) {
                 var id = input.data('id');
                 var val = input.val();
                 var type = input.data('type');
                 var name = input.data('name');
                 
                 $.ajax({
                     url: '{{ route("laser.update_price") }}',
                     type: 'POST',
                     data: {
                         id: id,
                         price: val,
                         type: type,
                         name: name,
                         _token: '{{ csrf_token() }}'
                     },
                     success: function(response) {
                         toastr.success('تم تحديث السعر بنجاح', 'تم الحفظ');
                         input.addClass('is-valid');
                         input.css('background-color', '#d4edda');
                         setTimeout(function() {
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
