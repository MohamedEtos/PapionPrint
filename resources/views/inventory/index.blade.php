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
                        <h2 class="content-header-title float-left mb-0">المخازن - الورق والأحبار</h2>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addStockModal">
                    <i class="feather icon-plus"></i> إضافة رصيد / تعديل
                </button>
            </div>
        </div>
        <div class="content-body">
            
            <div class="row">
                <!-- Paper Stocks -->
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">مخزون الورق</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover-animation mb-0">
                                        <thead>
                                            <tr>
                                                <th>نوع الماكينة</th>
                                                <th>الكمية (متر)</th>
                                                <th>آخر تحديث</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($paperStocks as $stock)
                                            <tr>
                                                <td class="font-weight-bold">{{ strtoupper($stock->machine_type) }}</td>
                                                <td>
                                                    <span class="badge badge-pill badge-light-{{ $stock->quantity < 50 ? 'danger' : 'success' }} mr-1">
                                                        {{ $stock->quantity }}
                                                    </span>
                                                </td>
                                                <td>{{ $stock->updated_at->diffForHumans() }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="3" class="text-center">لا يوجد بيانات</td></tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="openStockModal('paper')">تعديل / إضافة</button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ink Stocks -->
                <div class="col-md-6 col-12">
                     <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">مخزون الحبر</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover-animation mb-0">
                                        <thead>
                                            <tr>
                                                <th>النوع</th>
                                                <th>اللون</th>
                                                <th>الكمية (لتر)</th>
                                                <th>آخر تحديث</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($inkStocks as $stock)
                                            <tr>
                                                <td>{{ strtoupper($stock->machine_type) }}</td>
                                                <td>
                                                    <div class="chip chip-secondary mr-1">
                                                        <div class="chip-body">
                                                            <div class="chip-text">{{ $stock->color }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                     <span class="badge badge-pill badge-light-{{ $stock->quantity < 2 ? 'danger' : 'success' }} mr-1">
                                                        {{ $stock->quantity }}
                                                    </span>
                                                </td>
                                                <td>{{ $stock->updated_at->diffForHumans() }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="text-center">لا يوجد بيانات</td></tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4">
                                                    <button class="btn btn-sm btn-outline-primary" onclick="openStockModal('ink')">تعديل / إضافة</button>
                                                </td>
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
    </div>
</div>

<!-- Add/Edit Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إدارة المخزون</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="stockForm">
                    @csrf
                    <div class="form-group">
                        <label>النوع</label>
                        <select class="form-control" name="type" id="stockType" required>
                            <option value="paper">ورق</option>
                            <option value="ink">حبر</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>نوع الماكينة</label>
                         <select class="form-control" name="machine_type" id="machineType" required>
                            <option value="dtf">DTF</option>
                            <option value="sublimation">Sublimation</option>
                        </select>
                    </div>

                    <div class="form-group" id="colorGroup" style="display:none;">
                        <label>اللون</label>
                        <select class="form-control" name="color" id="stockColor">
                            <option value="Cyan">Cyan</option>
                            <option value="Magenta">Magenta</option>
                            <option value="Yellow">Yellow</option>
                            <option value="Black">Black</option>
                            <option value="White">White</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>العملية</label>
                        <div class="d-flex">
                            <div class="custom-control custom-radio mr-2">
                                <input type="radio" id="opAdd" name="operation" value="add" class="custom-control-input" checked>
                                <label class="custom-control-label" for="opAdd">إضافة (زيادة الرصيد)</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="opSet" name="operation" value="set" class="custom-control-input">
                                <label class="custom-control-label" for="opSet">تعديل (تعيين القيمة الحالية)</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>الكمية</label>
                        <input type="number" step="0.01" class="form-control" name="quantity" required placeholder="مثال: 50">
                    </div>

                    <input type="hidden" name="unit" id="stockUnit" value="meter">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="submitStockForm()">حفظ</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    function openStockModal(type) {
        $('#stockType').val(type).trigger('change');
        $('#addStockModal').modal('show');
    }

    // Toggle Color field based on type
    $('#stockType').change(function() {
        if($(this).val() == 'ink') {
            $('#colorGroup').show();
            $('#stockUnit').val('liter');
        } else {
            $('#colorGroup').hide();
            $('#stockUnit').val('meter');
        }
    });

    function submitStockForm() {
        $.ajax({
            url: "{{ route('inventory.store') }}",
            method: "POST",
            data: $('#stockForm').serialize(),
            success: function(response) {
                Swal.fire('تم!', response.success, 'success').then(() => location.reload());
            },
            error: function(xhr) {
                Swal.fire('خطأ', 'تأكد من إدخال البيانات بشكل صحيح', 'error');
            }
        });
    }

</script>
@endsection
