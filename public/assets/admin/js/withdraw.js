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
$(document).on("click", ".copy-ref", function () {
    // let ref = $(this).data("ref");

    // navigator.clipboard
    //     .writeText(ref)
    //     .then(() => {
    //         alert("Reference ID copied: " + ref);
    //     })
    //     .catch(() => {
    //         alert("Copy failed");
    //     });

    let ref = $(this).data("ref");

    let tempInput = document.createElement("input");
    tempInput.value = ref;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);

    // alert("Reference ID copied: " + ref);
    toastr.success("Wallet Address copied");
});
