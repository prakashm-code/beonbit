$(document).ready(function () {
    // Initialize validation rules
    $("#admin_login_form").validate({
        rules: {
            email: {
                required: true,
                email: true,
            },
            password: {
                required: true,
                minlength: 6,
            },
        },
        messages: {
            email: {
                required: "Please enter your email",
                email: "Enter a valid email address",
            },
            password: {
                required: "Please enter your password",
                minlength: "Password must be at least 6 characters",
            },
        },

        normalizer: function (value) {
            return $.trim(value);
        },

        errorClass: "text-danger",
        errorElement: "span",
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },
    });

    // Handle login button click
    $(".admin_login_btn").click(function () {
        if ($("#admin_login_form").valid()) {
            $("#admin_login_form").submit();
        }
    });
});
