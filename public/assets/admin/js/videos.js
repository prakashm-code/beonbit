function previewVideo(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (!file.type.startsWith("video/")) {
        event.target.value = "";
        return;
    }

    const videoPreview = document.getElementById("preview_video");
    videoPreview.style.display = "block";
    videoPreview.src = URL.createObjectURL(file);
    videoPreview.load();
}

$(document).ready(function () {
    $("#tags").tagsinput();

    $.validator.addMethod(
        "requireTags",
        function (value, element) {
            return value.trim() !== "";
        },
        "Please enter at least one tag."
    );

    $("#tags").on("itemAdded itemRemoved", function () {
        $(this).valid();
    });

    $("#add_video_form").validate({
        ignore: [],
        rules: {
            category: {
                required: true,
            },
            video_name: {
                required: true,
            },
            video_price: {
                required: true,
            },
            video_description: {
                required: true,
            },
            video: {
                required: true,
            },
            // tags: { requireTags: true },
        },
        messages: {
            category: {
                required: "Please select a category",
            },
            video_name: {
                required: "Please enter a video name",
            },
            video_price: {
                required: "Please enter video price",
            },
            video_description: {
                required: "Please enter a video description",
            },
            video: {
                required: "Please select a video",
            },
            // tags: {
            //     required: "Please enter tags for this image",
            // },
        },

        normalizer: function (value) {
            return $.trim(value);
        },

        errorClass: "text-danger",
        errorElement: "span",
        validClass: "is-valid",

        highlight: function (element) {
            if ($(element).attr("data-role") === "tagsinput") {
                $(element)
                    .closest(".mb-3")
                    .find(".bootstrap-tagsinput")
                    .addClass("form-control is-invalid");
            } else {
                $(element).addClass("is-invalid");
            }
        },
        unhighlight: function (element) {
            if ($(element).attr("data-role") === "tagsinput") {
                $(element)
                    .closest(".mb-3")
                    .find(".bootstrap-tagsinput")
                    .removeClass("form-control is-invalid");
            } else {
                $(element).removeClass("is-invalid");
            }
        },

        submitHandler: function (form) {
            $(form)
                .find('button[type="submit"]')
                .prop("disabled", true)
                .text("Please wait...");
            $("#loader").css("display", "flex");

            form.submit();
        },
    });

    $("#edit_video_form").validate({
        ignore: [],
        rules: {
            category: {
                required: true,
            },
            video_name: {
                required: true,
            },
            video_price: {
                required: true,
            },
            video_description: {
                required: true,
            },
            // video: {
            //     required: true,
            // },
            // tags: { requireTags: true },
        },
        messages: {
            category: {
                required: "Please select a category",
            },
            video_name: {
                required: "Please enter a video name",
            },
            video_price: {
                required: "Please enter video price",
            },
            video_description: {
                required: "Please enter a video description",
            },
            // video: {
            //     required: "Please select a video",
            // },
            // tags: {
            //     required: "Please enter tags for this image",
            // },
        },

        normalizer: function (value) {
            return $.trim(value);
        },

        errorClass: "text-danger",
        errorElement: "span",
        validClass: "is-valid",

        highlight: function (element) {
            if ($(element).attr("data-role") === "tagsinput") {
                $(element)
                    .closest(".mb-3")
                    .find(".bootstrap-tagsinput")
                    .addClass("form-control is-invalid");
            } else {
                $(element).addClass("is-invalid");
            }
        },
        unhighlight: function (element) {
            if ($(element).attr("data-role") === "tagsinput") {
                $(element)
                    .closest(".mb-3")
                    .find(".bootstrap-tagsinput")
                    .removeClass("form-control is-invalid");
            } else {
                $(element).removeClass("is-invalid");
            }
        },

        submitHandler: function (form) {
            $(form)
                .find('button[type="submit"]')
                .prop("disabled", true)
                .text("Please wait...");

            form.submit();
        },
    });

    $("#tags").on("itemAdded itemRemoved", function () {
        $(this).valid();
    });
});

$(document).on("click", ".video-thumbnail", function () {
    const videoUrl = $(this).data("video");
    const modal = new bootstrap.Modal(document.getElementById("videoModal"));

    const videoElement = $("#modalVideo")[0];
    const sourceElement = $("#modalVideo source");

    sourceElement.attr("src", videoUrl);

    videoElement.load();
    videoElement.play();

    modal.show();

    $("#videoModal").on("hidden.bs.modal", function () {
        videoElement.pause();
        sourceElement.attr("src", "");
        videoElement.load();
    });
});

$(document).on("click", ".delete_video", function () {
    var id = $(this).data("id");
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            // alert();
            $("#delete_video_form" + id).submit();
        }
    });
});

$(document).on("change", ".toggle-display-videos", function () {
    let id = $(this).data("id");
    let isChecked = $(this).is(":checked");
    $.ajax({
        url: base_url + "/admin/toggle_video_display",
        type: "POST",
        data: {
            id: id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response.success) {
                toastr.success("Display status updated");
            } else {
                toastr.error("Something went wrong!");
            }
        },
        error: function () {
            toastr.error("Server error occurred!");
        },
    });
});

function toggleDeleteButton() {
    let anyChecked = $(".row-checkbox:checked").length > 0;

    if (anyChecked) {
        $("#delete-selected").show();
    } else {
        $("#delete-selected").hide();
    }
}

$(document).on("change", ".row-checkbox", function () {
    toggleDeleteButton();
});

$(document).on("change", "#select-all", function () {
    $(".row-checkbox").prop("checked", this.checked);
    toggleDeleteButton();
});

$("#select-all").on("click", function () {
    $(".row-checkbox").prop("checked", this.checked);
});

$("#delete-selected").on("click", function () {
    let ids = [];

    $(".row-checkbox:checked").each(function () {
        ids.push($(this).val());
    });

    if (ids.length === 0) {
        toastr.success("Please select at least one video");
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url + "/admin/delete_multiple_video",
                type: "POST",
                data: {
                    ids: ids,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    $("#select-all").prop("checked", false);
                    $("#delete-selected").css("display", "none");
                    $("#videos-table").DataTable().ajax.reload();
                },
            });
        }
    });
});
