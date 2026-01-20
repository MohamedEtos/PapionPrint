$(document).ready(function () {

    // Clear sidebar when opening for new item
    $(".dt-buttons .btn-outline-primary").on("click", function () {
        $('#data-id').val('');
        $('#data-name').val('');
        $('#data-email').val('');
        $('#data-password').val('');
        $('.role-checkbox').prop('checked', false);
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
        var userId = $row.find('.user_id').val();
        var userName = $row.find('.user-name').text();
        var userEmail = $row.find('.user-email').text();
        var roles = [];

        $row.find('.role-item').each(function () {
            roles.push($(this).text().trim());
        });

        $('#data-id').val(userId);
        $('#data-name').val(userName);
        $('#data-email').val(userEmail);
        $('#data-password').val('');

        // Reset checkboxes
        $('.role-checkbox').prop('checked', false);

        // Check roles for this user
        roles.forEach(function (role) {
            $('.role-checkbox[value="' + role + '"]').prop('checked', true);
        });

        $(".add-new-data").addClass("show");
        $(".overlay-bg").addClass("show");
    });

    // On Delete
    $('.action-delete').on("click", function (e) {
        e.stopPropagation();
        var $row = $(this).closest('td').parent('tr');
        var userId = $row.find('.user_id').val();

        if (!userId) {
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
                    url: "/users/delete/" + userId,
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
                            text: 'تم حذف المستخدم بنجاح!',
                            confirmButtonClass: 'btn btn-success',
                        });
                    },
                    error: function (xhr) {
                        console.error("Error deleting user:", xhr);
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
        var email = $('#data-email').val();
        var password = $('#data-password').val();
        var selectedRoles = [];

        $('.role-checkbox:checked').each(function () {
            selectedRoles.push($(this).val());
        });

        if (!name || !email) {
            alert("يرجى ادخال الاسم والبريد الالكتروني");
            return;
        }

        if (!id && !password) {
            alert("يرجى ادخال كلمة المرور للمستخدم الجديد");
            return;
        }

        var url = id ? "/users/update/" + id : "/users/store";

        $.ajax({
            url: url,
            type: "POST",
            data: {
                name: name,
                email: email,
                password: password,
                roles: selectedRoles,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Swal.fire({
                    type: "success",
                    title: 'تم الحفظ!',
                    text: 'تم حفظ البيانات بنجاح!',
                    confirmButtonClass: 'btn btn-success',
                }).then(() => {
                    location.reload();
                });

                $(".add-new-data").removeClass("show");
                $(".overlay-bg").removeClass("show");
            },
            error: function (xhr) {
                console.error("Error saving user:", xhr);
                var message = "Error saving user";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                alert(message);
            }
        });
    });
});
