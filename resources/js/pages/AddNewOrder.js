// On Edit
$('.action-edit').on("click", function (e) {
  e.stopPropagation();
  $('#data-name').val('Altec Lansing - Bluetooth Speaker');
  $('#data-price').val('$9921');
  $(".add-new-data").addClass("show");
  $(".overlay-bg").addClass("show");
});

// On Delete
$('.action-delete').on("click", function (e) {
  e.stopPropagation();
  $(this).closest('td').parent('tr').fadeOut();
});

// on action-info
// on action-info
$('.action-info').on("click", function (e) {
  e.stopPropagation();
  $('#xlarge').modal('show');
});