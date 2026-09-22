$(document).ready(function () {
    $("#registrationForm").on("submit", function (event) {
        event.preventDefault(); // Prevent the default form submission

        // Get form data
        var formData = $(this).serialize();

        // Send AJAX request to the server
        $.ajax({
            url: "/admin/registers",
            type: "POST",
            data: formData,
            success: function (response) {
                alert("User created.");
                window.location.href = response.redirect;
            },
            error: function (xhr, status, error) {
                // Handle error response
                const message =
                    xhr.responseJSON?.message ??
                    "Registration failed. Please try again.";
                alert(message);

                console.error(error);
            },
        });
    });
});
