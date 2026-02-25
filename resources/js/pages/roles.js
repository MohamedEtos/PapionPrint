$(document).ready(function () {

    // Clear sidebar when opening for new item
    $(".dt-buttons .btn-outline-primary").on("click", function () {
        $('#data-id').val('');
        $('#data-name').val('');
        $('.permission-checkbox').prop('checked', false);
        $(".add-new-data").addClass("show");
        $(".overlay-bg").addClass("show");
    });

    // Close Sidebar
    $('.hide-data-sidebar, .cancel-data-btn, .overlay-bg').on("click", function (e) {
        e.stopPropagation();
        $(".add-new-data").removeClass("show");
        $(".overlay-bg").removeClass("show");
    });

    // On Edit
    $('.action-edit').on("click", function (e) {
        e.stopPropagation();
        var $row = $(this).closest('td').parent('tr');
        // Use data attributes directly from the button 
        var roleId = $(this).data('id');
        var roleName = $(this).data('name');

        // Fallback or additional logic if needed (e.g., getting permissions from row)
        if (!roleId) {
             roleId = $row.find('.role_id').val();
        }
        if (!roleName) {
             roleName = $row.find('.role-name').text().trim();
        }
        
        var permissions = [];

        $row.find('.perm-item').each(function () {
            permissions.push($(this).text().trim());
        });

        $('#data-id').val(roleId);
        $('#data-name').val(roleName);

        // Reset checkboxes
        $('.permission-checkbox').prop('checked', false);

        // Check permissions for this role
        permissions.forEach(function (perm) {
            $('.permission-checkbox[value="' + perm + '"]').prop('checked', true);
        });

        $(".add-new-data").addClass("show");
        $(".overlay-bg").addClass("show");

    });

    // On Delete
    $('.action-delete').on("click", function (e) {
        e.stopPropagation();
        var $row = $(this).closest('td').parent('tr');
        var roleId = $row.find('.role_id').val();

        if (!roleId) {
            $row.fadeOut();
            return;
        }

        Swal.fire({
            title: 'هل انت متاكد?',
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
                    url: "/roles/delete/" + roleId,
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        $row.fadeOut(function () {
                            $(this).remove();
                        });
                        Swal.fire({
                            type: "success",
                            title: 'تم الحذف!',
                            text: 'تم حذف الدور بنجاح!',
                            confirmButtonClass: 'btn btn-success',
                        });
                    },
                    error: function (xhr) {
                        console.error("Error deleting role:", xhr);
                        Swal.fire({
                            title: "خطأ!",
                            text: "حدث خطأ أثناء الحذف.",
                            type: "error",
                            confirmButtonClass: 'btn btn-primary',
                            buttonsStyling: false,
                        });
                    }
                });
            }
        });
    });

    // Save Data
    $('#saveDataBtn').on('click', function (e) {
        e.preventDefault();

        var id = $('#data-id').val();
        var name = $('#data-name').val();
        var selectedPermissions = [];

        $('.permission-checkbox:checked').each(function () {
            selectedPermissions.push($(this).val());
        });

        if (!name) {
            alert("يرجى ادخال اسم الدور");
            return;
        }

        var url = id ? "/roles/update/" + id : "/roles/store";

        $.ajax({
            url: url,
            type: "POST",
            data: {
                name: name,
                permissions: selectedPermissions,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Swal.fire({
                    type: "success",
                    title: 'تم الحفظ!',
                    text: 'تم حفظ البيانات بنجاح!',
                    confirmButtonClass: 'btn btn-success',
                }).then(() => {
                    location.reload(); // Simple reload to refresh table, or append row dynamically if preferred
                });

                

                $(".add-new-data").removeClass("show");
                $(".overlay-bg").removeClass("show");
            },
            error: function (xhr) {
                console.error("Error saving role:", xhr);
                alert("Error saving role: " + (xhr.responseJSON.message || "Unknown error"));
            }
        });
    });
});
