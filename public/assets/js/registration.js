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
                // Handle success response
                alert("Registration successful!");
                window.location.href = "{{ path('admin_login') }}";
            },
            error: function (xhr, status, error) {
                // Handle error response
                alert("Registration failed. Please try again.");
                console.error(error);
            },
        });
    });
});
