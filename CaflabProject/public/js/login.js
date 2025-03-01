$(document).ready(function () {
    $('#login-form').on('submit', function (e) {
        e.preventDefault(); 

        const email = $('#email').val().trim();
        const password = $('#password').val().trim();

        // validate the input from user
        if (!email || !password) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Input',
                text: 'Please enter both email and password.',
            });
            return;
        }

        // an ajax call to the process_login.php file
        $.ajax({
            type: 'POST',
            url: 'process_login.php',
            data: { email: email, password: password },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    if (response.isFirstLogin) {
                        // For first-time login, show a welcome message
                        Swal.fire({
                            icon: 'info',
                            title: 'Welcome!',
                            text: 'For security reasons, please change your password.',
                            confirmButtonText: 'Change Password'
                        }).then(() => {
                            window.location.href = response.redirect;
                        });
                    } else {
                        // Regular login success
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful',
                            text: 'You have logged in successfully.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            if (response.isAdmin) {
                                // If admin user, check if there's a stored admin URL
                                window.location.href = response.redirect || '/Team-Project-255/admin/dashboard.php';
                            } else {
                                window.location.href = response.redirect || 'home.php';
                            }
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: response.message,
                    });
                }
            },
            error: function (xhr, status, error) {
                // Check if we're actually being redirected (which is not an error)
                if (xhr.status === 0 && status === 'error') {
                    // This is likely a redirect, so we don't need to show an error
                    return;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong. Please try again later.',
                });
            }
        });
    });

    // Password visibility toggle
    $('.toggle-password').click(function() {
        const input = $($(this).data('target'));
        const icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
