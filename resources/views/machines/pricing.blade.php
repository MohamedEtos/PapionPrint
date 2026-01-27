@extends('layouts.app')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">اسعار الطباعة</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                                <li class="breadcrumb-item active">اسعار الطباعة</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="basic-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">اسعار الماكينات</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body card-dashboard">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>اسم الماكينة</th>
                                                    <th>سعر 1 Pass</th>
                                                    <th>سعر 4 Pass</th>
                                                    <th>سعر 6 Pass</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($machines as $machine)
                                                <tr>
                                                    <td>{{ $machine->name }}</td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control price-input" 
                                                               data-id="{{ $machine->id }}" 
                                                               data-field="price_1_pass" 
                                                               value="{{ $machine->price_1_pass }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control price-input" 
                                                               data-id="{{ $machine->id }}" 
                                                               data-field="price_4_pass" 
                                                               value="{{ $machine->price_4_pass }}">
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control price-input" 
                                                               data-id="{{ $machine->id }}" 
                                                               data-field="price_6_pass" 
                                                               value="{{ $machine->price_6_pass }}">
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
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('.price-input').on('change', function() {
            var input = $(this);
            var id = input.data('id');
            var field = input.data('field');
            var value = input.val();

            $.ajax({
                url: "{{ route('machines.update_price') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                    field: field,
                    value: value
                },
                success: function(response) {
                    toastr.success('تم تحديث السعر بنجاح', 'نجاح');
                    input.addClass('is-valid').css('background-color', '#d4edda');
                    setTimeout(function() {
                        input.removeClass('is-valid').css('background-color', '');
                    }, 1500);
                },
                error: function(xhr) {
                    toastr.error('حدث خطأ اثناء الحفظ', 'خطأ');
                }
            });
        });
    });
</script>
@endsection
