// Initial Data Setup
function initData() {
    if (!localStorage.getItem('users')) {
        localStorage.setItem('users', JSON.stringify([]));
    }
    if (!localStorage.getItem('feedbacks')) {
        localStorage.setItem('feedbacks', JSON.stringify([]));
    }
}

// Show/Hide Loader
function showLoader() {
    $('#loader').removeClass('hidden');
}

function hideLoader() {
    $('#loader').addClass('hidden');
}

// Check Session Utility
function checkSession(requiredRole = null) {
    const user = JSON.parse(sessionStorage.getItem('loggedInUser'));
    if (!user) {
        window.location.href = 'login.html';
        return null;
    }
    if (requiredRole && user.role !== requiredRole) {
        if (user.role === 'Student') window.location.href = 'student_dashboard.html';
        if (user.role === 'Teacher') window.location.href = 'teacher_dashboard.html';
    }
    return user;
}

// Logout
function logout() {
    showLoader();
    setTimeout(() => {
        sessionStorage.removeItem('loggedInUser');
        window.location.href = 'login.html';
    }, 800);
}

// Registration Logic
function handleRegister(e) {
    e.preventDefault();
    
    const fullName = $('#fullName').val().trim();
    const email = $('#email').val().trim();
    const regNumber = $('#regNumber').val().trim();
    const year = $('#year').val();
    const password = $('#password').val();
    const confirmPassword = $('#confirmPassword').val();
    const role = $('#role').val();

    if (password !== confirmPassword) {
        showAlert('Passwords do not match!', 'danger');
        return;
    }

    const users = JSON.parse(localStorage.getItem('users'));
    if (users.find(u => u.regNumber === regNumber)) {
        showAlert('Registration ID is already registered!', 'danger');
        return;
    }

    showLoader();

    $.ajax({
        url: 'api_register.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ fullName, email, regNumber, year, password, role }),
        success: function(res) {
            hideLoader();
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Registration Successful!',
                    text: 'Redirecting to login...',
                    timer: 2000,
                    showConfirmButton: false,
                    background: 'rgba(20, 20, 35, 0.9)',
                    color: '#fff'
                }).then(() => {
                    window.location.href = 'login.html';
                });
            } else {
                showAlert(res.message || 'Registration failed', 'danger');
            }
        },
        error: function() {
            hideLoader();
            showAlert('Server error', 'danger');
        }
    });
}

// Login Logic
function handleLogin(e) {
    e.preventDefault();
    const loginRegId = $('#loginRegId').val().trim();
    const password = $('#password').val();
    const rememberMe = $('#rememberMe').is(':checked');

    showLoader();

    $.ajax({
        url: 'api_login.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ regNumber: loginRegId, password }),
        success: function(res) {
            hideLoader();
            if (res.success && res.user) {
                // Handle Remember Me
                if (rememberMe) {
                    localStorage.setItem('rememberedUser', JSON.stringify({ loginRegId, password }));
                } else {
                    localStorage.removeItem('rememberedUser');
                }

                sessionStorage.setItem('loggedInUser', JSON.stringify(res.user));
                
                Swal.fire({
                    icon: 'success',
                    title: 'Login Success!',
                    text: 'Welcome back, ' + res.user.fullName,
                    timer: 1500,
                    showConfirmButton: false,
                    background: 'rgba(20, 20, 35, 0.9)',
                    color: '#fff'
                }).then(() => {
                    if (res.user.role === 'Admin') {
                        window.location.href = 'admin_dashboard.html';
                    } else if (res.user.role === 'Student') {
                        window.location.href = 'student_dashboard.html';
                    } else {
                        window.location.href = 'teacher_dashboard.html';
                    }
                });
            } else {
                showAlert(res.message || 'Invalid Registration ID or password!', 'danger');
            }
        },
        error: function() {
            hideLoader();
            showAlert('Server error', 'danger');
        }
    });
}

// Alert Helper
function showAlert(message, type) {
    const alertHtml = `
        <div class="alert ${type === 'danger' ? 'alert-glass' : 'alert-success-glass'} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    $('#alertContainer').html(alertHtml);
}

// Feedback Submission Logic
function handleFeedbackSubmit(e) {
    e.preventDefault();

    const teacherId = $('#teacherSelect').val();
    const subject = $('#subjectSelect').val();
    const suggestion = $('#suggestion').val().trim();

    if (!teacherId || teacherId === "Select Teacher") {
        alert("Please select a teacher.");
        return;
    }

    if (!subject) {
        alert("Please select a subject.");
        return;
    }

    const ratings = [];
    let allAnswered = true;
    for (let i = 1; i <= 9; i++) {
        const rating = $(`input[name="q${i}"]:checked`).val();
        if (!rating) {
            allAnswered = false;
            break;
        }
        ratings.push(parseInt(rating));
    }

    if (!allAnswered) {
        alert("Please rate all 9 questions before submitting.");
        return;
    }

    showLoader();

    const currentUser = JSON.parse(sessionStorage.getItem('loggedInUser'));
    const studentId = currentUser ? currentUser.id : 0;

    $.ajax({
        url: 'api_submit_feedback.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            studentId,
            teacherId,
            subject,
            ratings,
            suggestion
        }),
        success: function(res) {
            hideLoader();
            if (res.success) {
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                setTimeout(() => {
                    window.location.href = 'student_dashboard.html';
                }, 2000);
            } else {
                alert(res.message || 'Failed to submit feedback');
            }
        },
        error: function() {
            hideLoader();
            alert('Server error');
        }
    });
}

// Initialization calls
$(document).ready(function() {
    initData();

    $('#role').on('change', function() {
        if ($(this).val() === 'Teacher') {
            $('#regNumberLabel').text('Teacher Reg ID');
            $('#regNumber').attr('placeholder', 'TR-2026');
            $('#yearContainer').addClass('hidden');
            $('#year').removeAttr('required');
        } else {
            $('#regNumberLabel').text('Register Number');
            $('#regNumber').attr('placeholder', 'CS202601');
            $('#yearContainer').removeClass('hidden');
            $('#year').prop('required', true);
        }
    });

    $('#registerForm').on('submit', handleRegister);
    $('#loginForm').on('submit', handleLogin);
    $('#feedbackForm').on('submit', handleFeedbackSubmit);

    // Initialize Remember Me
    if ($('#loginForm').length) {
        const remembered = JSON.parse(localStorage.getItem('rememberedUser'));
        if (remembered) {
            $('#loginRegId').val(remembered.loginRegId);
            $('#password').val(remembered.password);
            $('#rememberMe').prop('checked', true);
        }
    }

    // Forgot Password Action
    $('#forgotPasswordLink').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Forgot Password?',
            text: 'Please contact the system administrator to reset your password. You can find their office in the Computer Science block or email admin@edufeedback.com',
            icon: 'info',
            background: 'rgba(20, 20, 35, 0.9)',
            color: '#fff',
            confirmButtonColor: 'var(--primary-color)'
        });
    });
    
    // Logout bindings
    $('[data-action="logout"]').on('click', logout);

    // Profile Modal Logic
    $('#profileModal').on('show.bs.modal', function () {
        const user = JSON.parse(sessionStorage.getItem('loggedInUser'));
        if (user) {
            $('#profRegId').val(user.regNumber);
            $('#profFullName').val(user.fullName);
            $('#profEmail').val(user.email);
            $('#profPassword').val(user.password);
            
            if (user.role === 'Student') {
                $('#profYearContainer').show();
                $('#profYear').val(user.year);
            } else {
                $('#profYearContainer').hide();
                $('#profYear').removeAttr('required');
            }
        }
    });

    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        const user = JSON.parse(sessionStorage.getItem('loggedInUser'));
        if (!user) return;
        
        $.ajax({
            url: 'api_update_profile.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                id: user.id,
                fullName: $('#profFullName').val().trim(),
                email: $('#profEmail').val().trim(),
                password: $('#profPassword').val(),
                year: user.role === 'Student' ? $('#profYear').val() : user.year
            }),
            success: function(res) {
                if (res.success) {
                    sessionStorage.setItem('loggedInUser', JSON.stringify(res.user));
                    $('#profileModal').modal('hide');
                    showAlert('Profile updated successfully! Refreshing...', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                } else {
                    alert(res.message || 'Failed to update profile');
                }
            }
        });
    });
});
