$(document).ready(function () {
    "use strict";

    // Init DataTable
    var table = $('.data-thumb-view').DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        ajax: {
            url: window.location.href, // /Rollpress/trash
        },
        columns: [
            { data: 'action', orderable: false, searchable: false, defaultContent: '' }, // 0
            { data: 'image', orderable: false, searchable: false, defaultContent: '' }, // 1
            { data: 'customer.name', defaultContent: '-' }, // 2
            { data: 'fabrictype', defaultContent: '-' }, // 3
            { data: 'meters', defaultContent: '-' }, // 4
            { data: 'deleted_at', defaultContent: '-' } // 5
        ],
        columnDefs: [
            {
                targets: 0, // Actions
                render: function (data, type, full, meta) {
                    return `<div class="btn-group" role="group">
                        <button type="button" class="btn btn-icon btn-flat-success restore-btn" title="استعاده" data-id="${full.id}">
                            <i class="feather icon-rotate-ccw"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-flat-danger force-delete-btn" title="حذف نهائي" data-id="${full.id}">
                            <i class="feather icon-trash-2"></i>
                        </button>
                    </div>`;
                }
            },
            {
                targets: 1, // Image
                render: function (data, type, full, meta) {
                    let imgPath = '/core/images/elements/apple-watch.png'; // Fallback
                    if (full.order && full.order.orders_imgs && full.order.orders_imgs.length > 0) {
                        imgPath = '/storage/' + full.order.orders_imgs[0].path;
                    }
                    return `<img style="height: 50px;" src="${imgPath}" alt="Img">`;
                }
            },
            {
                targets: 5, // Deleted At
                render: function (data, type, full, meta) {
                    return data ? new Date(data).toLocaleString('ar-EG') : '-';
                }
            }
        ],
        dom: '<"top"<"actions action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
        oLanguage: { sLengthMenu: "_MENU_", sSearch: "" },
        aLengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
        order: [[5, "desc"]],
        bInfo: false,
        pageLength: 10,
        buttons: []
    });


    // Restore Action
    $(document).on("click", ".restore-btn", function (e) {
        var id = $(this).data('id');
        Swal.fire({
            title: 'استعادة الطلب؟',
            text: "سيعود الطلب إلى قائمة الارشيف.",
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، استعاده!',
            cancelButtonText: 'الغاء',
            confirmButtonClass: 'btn btn-primary',
            cancelButtonClass: 'btn btn-danger ml-1',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "/Rollpress/restore/" + id,
                    type: "POST",
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        table.draw();
                        toastr.success("تم استعادة الطلب بنجاح", "نجاح");
                    },
                    error: function (xhr) {
                        toastr.error("حدث خطأ أثناء الاستعادة", "خطأ");
                    }
                });
            }
        });
    });

    // Force Delete Action
    $(document).on("click", ".force-delete-btn", function (e) {
        var id = $(this).data('id');
        Swal.fire({
            title: 'حذف نهائي؟',
            text: "لا يمكن التراجع عن هذا الاجراء!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، حذف نهائي!',
            cancelButtonText: 'الغاء',
            confirmButtonClass: 'btn btn-danger',
            cancelButtonClass: 'btn btn-primary ml-1',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "/Rollpress/force-delete/" + id,
                    type: "DELETE",
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        table.draw();
                        toastr.success("تم الحذف النهائي بنجاح", "نجاح");
                    },
                    error: function (xhr) {
                        toastr.error("حدث خطأ أثناء الحذف", "خطأ");
                    }
                });
            }
        });
    });

});
