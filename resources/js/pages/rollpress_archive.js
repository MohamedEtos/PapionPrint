$(document).ready(function () {
    "use strict";

    // Init Wizard
    var form = $(".steps-validation").show();
    $(".steps-validation").steps({
        headerTag: "h6",
        bodyTag: "fieldset",
        transitionEffect: "fade",
        titleTemplate: '<span class="step">#index#</span> #title#',
        labels: { finish: 'Submit' },
        onStepChanging: function (event, currentIndex, newIndex) {
            if (currentIndex > newIndex) return true;
            if (currentIndex < newIndex) {
                form.find(".body:eq(" + newIndex + ") label.error").remove();
                form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
            }
            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        },
        onFinishing: function (event, currentIndex) {
            form.validate().settings.ignore = ":disabled";
            return form.valid();
        },
        onFinished: function (event, currentIndex) {
            submitWizardData();
        }
    });

    // Init Validation
    $(".steps-validation").validate({
        ignore: 'input[type=hidden]',
        errorClass: 'danger',
        successClass: 'success',
        highlight: function (element, errorClass) { $(element).removeClass(errorClass); },
        unhighlight: function (element, errorClass) { $(element).removeClass(errorClass); },
        errorPlacement: function (error, element) { error.insertAfter(element); }
    });

    // Init DataTable
    var table = $('.data-thumb-view').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        ajax: {
            url: window.location.href, // /Rollpress/archive
        },
        columns: [
            { data: 'id' }, // 0
            { data: 'action', orderable: false, searchable: false, defaultContent: '' }, // 1
            { data: 'image', orderable: false, searchable: false, defaultContent: '' }, // 2
            { data: 'customer.name', defaultContent: '-' }, // 3
            { data: 'fabrictype',defaultContent: '-' }, // 4
            { data: 'fabricsrc',defaultContent: '-' }, // 5
            { data: 'fabriccode',defaultContent: '-' }, // 6
            { data: 'fabricwidth',defaultContent: '-' }, // 7
            { data: 'meters',defaultContent: '-' }, // 8
            { data: 'status',defaultContent: '-' }, // 9
            { data: 'papyershild',defaultContent: '-' }, // 10
            { data: 'paymentstatus',defaultContent: '-' }, // 11
            { data: 'price',defaultContent: '-' }, // 12
            { data: 'notes',defaultContent: '-' }, // 13
            { data: 'created_at',defaultContent: '-' }, // 14
            { data: 'updated_at',defaultContent: '-' } // 15
        ],
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                className: 'dt-checkboxes-cell',
                render: function (data, type, full, meta) {
                    return '<div class="dt-checkboxes"><input type="checkbox" class="dt-checkboxes key_checkbox" value="' + full.id + '"><label></label></div>';
                },
                checkboxes: { selectRow: true }
            },
            {
                targets: 1, // Actions
                render: function (data, type, full, meta) {
                    return `<div class="btn-group" role="group">
                        <button type="button" class="btn btn-icon btn-flat-primary edit-btn" title="تعديل" data-id="${full.id}">
                            <i class="feather icon-edit"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-flat-danger delete-btn" title="حذف" data-id="${full.id}">
                            <i class="feather icon-trash-2"></i>
                        </button>
                    </div>`;
                }
            },
            {
                targets: 2, // Image
                render: function (data, type, full, meta) {
                    // Check order images. full.order.orders_imgs
                    let imgPath = '/core/images/elements/apple-watch.png'; // Fallback
                    // Assuming structure: Rollpress -> belongsTo Order -> hasMany OrdersImg
                    // In controller we did: Rollpress::with(..., 'order.ordersImgs')
                    if (full.order && full.order.orders_imgs && full.order.orders_imgs.length > 0) {
                        imgPath = '/storage/' + full.order.orders_imgs[0].path;
                    }
                    return `<img style="height: 50px;" src="${imgPath}" alt="Img">`;
                }
            },
            {
                targets: 9, // Status
                render: function (data, type, full, meta) {
                    var statusText = (data == 1) ? 'تم الانتهاء' : 'جاري العمل';
                    var chipClass = (data == 1) ? 'chip-success' : 'chip-warning';
                    return `<div class="chip ${chipClass}">
                      <div class="chip-body">
                        <div class="chip-text">${statusText}</div>
                      </div>
                    </div>`;
                }
            },
            {
                targets: 11, // Payment Status
                render: function (data, type, full, meta) {
                    return (data == 1) ? '<span class="text-success">مدفوع</span>' : '<span class="text-danger">غير مدفوع</span>';
                }
            },
            {
                targets: [14, 15], // Dates
                render: function (data, type, full, meta) {
                    return data ? new Date(data).toLocaleString('ar-EG') : '-';
                }
            }
        ],
        dom: '<"top"<"actions action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
        oLanguage: { sLengthMenu: "_MENU_", sSearch: "" },
        aLengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
        select: { style: "multi" },
        order: [[14, "desc"]],
        bInfo: false,
        pageLength: 10,
        buttons: [], // Actions handled by custom dropdown
        initComplete: function (settings, json) {
            $(".dt-buttons .btn").removeClass("btn-secondary");
            // Move actions dropdown
            var actionDropdown = $(".actions-dropodown");
            actionDropdown.insertBefore($(".top .actions .dt-buttons"));

            if (table.rows({ selected: true }).count() > 0) {
                actionDropdown.slideDown();
            } else {
                actionDropdown.hide();
            }
        }
    });

    // Action Dropdown Visibility
    table.on('select deselect', function () {
        var selectedCount = table.rows({ selected: true }).count();
        if (selectedCount > 0) {
            $('.actions-dropodown').slideDown();
        } else {
            $('.actions-dropodown').slideUp();
        }
    });

    // Bulk Delete
    $(document).on("click", ".bulk-delete-btn", function (e) {
        e.preventDefault();
        var selectedRows = table.rows({ selected: true });
        var selectedIds = [];
        var data = selectedRows.data();
        for (var i = 0; i < data.length; i++) {
            selectedIds.push(data[i].id);
        }
        // Fallback
        if (selectedIds.length === 0) {
            $('.dt-checkboxes:checked').each(function () {
                selectedIds.push($(this).val());
            });
            selectedIds = [...new Set(selectedIds)];
        }

        if (selectedIds.length === 0) {
            Swal.fire({ title: "تنبيه", text: "الرجاء تحديد طلب واحد على الأقل للحذف.", type: "warning", confirmButtonClass: 'btn btn-primary', buttonsStyling: false });
            return;
        }

        Swal.fire({
            title: 'هل انت متاكد من حذف ' + selectedIds.length + ' طلب؟',
            text: "لن تتمكن من التراجع عن هذا!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احذفهم!',
            confirmButtonClass: 'btn btn-primary',
            cancelButtonClass: 'btn btn-danger ml-1',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "/Rollpress/bulk-delete",
                    type: "POST",
                    data: { ids: selectedIds, _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        table.draw();
                        $('.actions-dropodown').hide();
                        Swal.fire({ type: 'success', title: 'تم الحذف!', text: 'تم حذف الطلبات المحددة بنجاح.', showConfirmButton: false, timer: 1500, buttonsStyling: false });
                    },
                    error: function (xhr) {
                        Swal.fire({ title: "خطأ!", text: "حدث خطأ أثناء الحذف.", type: "error", confirmButtonClass: 'btn btn-primary', buttonsStyling: false });
                    }
                });
            }
        });
    });

    // Single Delete
    $(document).on("click", ".delete-btn", function (e) {
        // Re-use bulk delete logic with single ID? Or specific route?
        // Let's use bulk logic for simplicity or just create a specific one.
        // Bulk logic works fine for single ID too.
        // Actually, let's stick to bulk delete route with array of 1.
        var id = $(this).data('id');
        // Trigger bulk logic manually or copy-paste? Copy paste safer for now.
        Swal.fire({
            title: 'هل انت متاكد من حذف هذا الطلب؟',
            text: "لن تتمكن من التراجع عن هذا!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احذفه!',
            confirmButtonClass: 'btn btn-primary',
            cancelButtonClass: 'btn btn-danger ml-1',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "/Rollpress/bulk-delete",
                    type: "POST",
                    data: { ids: [id], _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        table.draw();
                        Swal.fire({ type: 'success', title: 'تم الحذف!', text: 'تم حذف الطلب بنجاح.', showConfirmButton: false, timer: 1500, buttonsStyling: false });
                    },
                    error: function (xhr) {
                        Swal.fire({ title: "خطأ!", text: "حدث خطأ أثناء الحذف.", type: "error", confirmButtonClass: 'btn btn-primary', buttonsStyling: false });
                    }
                });
            }
        });
    });

    // Edit Logic
    var editingId = null;

    $(document).on("click", ".edit-btn", function () {
        var id = $(this).data('id');
        var rowData = table.row($(this).closest('tr')).data();

        editingId = id;

        // Populate Form
        $('#data-customer-view').val(rowData.customer ? rowData.customer.name : '');
        $('#data-status').val(rowData.status == 1 ? 'تم الانتهاء' : 'جاري العمل');
        $('#data-payment-status').val(rowData.paymentstatus); // 0 or 1
        $('#data-fabric-type').val(rowData.fabrictype);
        $('#data-source').val(rowData.fabricsrc);
        $('#data-code').val(rowData.fabriccode);
        $('#data-width').val(rowData.fabricwidth);
        $('#data-meters').val(rowData.meters);
        $('#data-paper-shield').val(rowData.papyershild);
        $('#data-price').val(rowData.price);
        $('#data-notes').val(rowData.notes);

        // Update Title
        $('.new-data-title').text('تعديل طلب رقم ' + id);

        // Show Wizard
        $('#validation').slideDown();
        $('html, body').animate({ scrollTop: $("#validation").offset().top - 100 }, 500);

    });

    function submitWizardData() {
        if (!editingId) return; // Should allow add? Archive is usually read-only/edit.

        var formData = new FormData();
        formData.append('_method', 'PUT'); // For update
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Fields
        formData.append('customerName', $('#data-customer-view').val());
        // Handle customerId logic if needed (datalist)
        var val = $('#data-customer-view').val();
        var opt = $('#customers-list option[value="' + val + '"]');
        if (opt.length > 0) formData.append('customerId', opt.attr('data-id'));

        formData.append('status', $('#data-status').val());
        formData.append('paymentstatus', $('#data-payment-status').val());
        formData.append('fabrictype', $('#data-fabric-type').val());
        formData.append('fabricsrc', $('#data-source').val());
        formData.append('fabriccode', $('#data-code').val());
        formData.append('fabricwidth', $('#data-width').val());
        formData.append('meters', $('#data-meters').val());
        formData.append('papyershild', $('#data-paper-shield').val());
        formData.append('price', $('#data-price').val());
        formData.append('notes', $('#data-notes').val());

        $.ajax({
            url: "/Rollpress/update/" + editingId,
            type: "POST", // POST with _method=PUT
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                toastr.success("تم تحديث الطلب بنجاح", "نجاح");
                $('#validation').slideUp();
                editingId = null;
                resetForm();
                table.draw();
            },
            error: function (xhr) {
                console.error(xhr);
                toastr.error("حدث خطأ أثناء التحديث", "خطأ");
            }
        });
    }

    function resetForm() {
        $('form.steps-validation')[0].reset();
        $('.new-data-title').text('تعديل طلب');
    }

});
