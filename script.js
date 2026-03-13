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
    
    $('[data-action="logout"]').on('click', logout);

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
        
        user.fullName = $('#profFullName').val().trim();
        user.email = $('#profEmail').val().trim();
        user.password = $('#profPassword').val();
        if (user.role === 'Student') {
            user.year = $('#profYear').val();
        }
        
        sessionStorage.setItem('loggedInUser', JSON.stringify(user));
        
        const users = JSON.parse(localStorage.getItem('users')) || [];
        const index = users.findIndex(u => u.id === user.id);
        if (index !== -1) {
            users[index] = user;
            localStorage.setItem('users', JSON.stringify(users));
        }

        $('#profileModal').modal('hide');
        showAlert('Profile updated successfully! Refreshing...', 'success');
        
        setTimeout(() => {
            window.location.reload();
        }, 1200);
    });
});

