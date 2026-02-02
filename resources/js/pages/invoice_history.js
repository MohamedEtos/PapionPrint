$(document).ready(function () {
    'use strict';

    console.log('Invoice History JS loaded');

    var table = $('.data-list-view').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/invoices/history-data',
            error: function (xhr, error, code) {
                console.error('DataTable AJAX Error:', error, code);
                console.error('Response:', xhr.responseText);
            }
        },
        columns: [
            {
                data: 'image',
                name: 'image',
                orderable: false,
                searchable: false
            },
            {
                data: 'type',
                name: 'type'
            },
            {
                data: 'details',
                name: 'details'
            },
            {
                data: 'customer_name',
                name: 'customer_name'
            },
            {
                data: 'quantity',
                name: 'quantity'
            },
            {
                data: 'unit_price',
                name: 'custom_price'
            },
            {
                data: 'total',
                name: 'total',
                orderable: false
            },
            {
                data: 'sent_date',
                name: 'sent_date'
            },
            {
                data: 'sent_status',
                name: 'sent_status'
            },
            {
                data: 'created_at',
                name: 'created_at'
            }
        ],
        order: [[9, 'desc']], // Order by created_at descending
        language: {
            "sProcessing": "جارٍ التحميل...",
            "sLengthMenu": "أظهر _MENU_ مدخلات",
            "sZeroRecords": "لم يعثر على أية سجلات",
            "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ مدخل",
            "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
            "sInfoFiltered": "(منتقاة من مجموع _MAX_ مُدخل)",
            "sInfoPostFix": "",
            "sSearch": "ابحث:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "الأول",
                "sPrevious": "السابق",
                "sNext": "التالي",
                "sLast": "الأخير"
            }
        },
        dom: '<"top"<"actions action-btns"B><"action-filters"f>>rt<"bottom"<"actions"i><"pagination"><"actions"l><"actions clearfix">>',
        buttons: [
            {
                extend: 'print',
                text: 'طباعة', // Arabic for Print
                titleAttr: 'Print',
                className: 'btn btn-outline-primary'
            },
            {
                extend: 'copy',
                text: 'نسخ', // Arabic for Copy
                titleAttr: 'Copy',
                className: 'btn btn-outline-primary'
            },
            {
                extend: 'csv',
                text: 'CSV',
                titleAttr: 'CSV',
                className: 'btn btn-outline-primary',
                charset: 'utf-8',
                bom: true
            },
            {
                extend: 'excel',
                text: 'Excel',
                titleAttr: 'Excel',
                className: 'btn btn-outline-primary'
            }
        ],
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100]
    });

    // Row Click Handler for Details
    $('.data-list-view tbody').on('click', 'tr', function () {
        var data = table.row(this).data();
        if (data && data.id) {
            $('#invoice-details-modal').modal('show');
            $('#invoice-details-body').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');

            $.ajax({
                url: '/invoices/archive-details/' + data.id,
                method: 'GET',
                success: function (response) {
                    $('#invoice-details-body').html(response.html);
                },
                error: function () {
                    $('#invoice-details-body').html('<div class="alert alert-danger">حدث خطأ أثناء تحميل التفاصيل</div>');
                }
            });
        }
    });

    console.log('DataTable initialized');

    // Add custom styling
    $('.dataTables_filter input').addClass('form-control');
    $('.dataTables_length select').addClass('form-control');
});
