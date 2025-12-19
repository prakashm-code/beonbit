$(document).on('change', '.withdrawal-status', function () {
    let status = $(this).val();
    let id = $(this).data('id');

    $.ajax({
        url: base_url + "/admin/update_withdraw_status",
        method: 'POST',
        data: {
            id: id,
            status: status,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (res) {
            if (res.status == 0) {
                toastr.success('Withdraw Request Updated');
            } else {
                toastr.error('Withdraw Request Not Updated');

            }
        }
    });
});
