Registration
---------------
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

    // AJAX Simulation
    setTimeout(() => {
        const newUser = {
            id: Date.now().toString(),
            fullName,
            email,
            regNumber,
            year,
            password,
            role
        };
        users.push(newUser);
        localStorage.setItem('users', JSON.stringify(users));
        
        hideLoader();
        showAlert('Registration successful! Redirecting...', 'success');
        
        setTimeout(() => {
            window.location.href = 'login.html';
        }, 1500);
    }, 1200);
}







login
-------
// Login Logic
function handleLogin(e) {
    e.preventDefault();
    const loginRegId = $('#loginRegId').val().trim();
    const password = $('#password').val();

    showLoader();

    // AJAX Simulation
    setTimeout(() => {
        const users = JSON.parse(localStorage.getItem('users'));
        const user = users.find(u => u.regNumber === loginRegId && u.password === password);

        hideLoader();

        if (user) {
            sessionStorage.setItem('loggedInUser', JSON.stringify(user));
            if (user.role === 'Student') {
                window.location.href = 'student_dashboard.html';
            } else {
                window.location.href = 'teacher_dashboard.html';
            }
        } else {
            showAlert('Invalid Registration ID or password!', 'danger');
        }
    }, 1000);
}








feedback submission
---------------------
// Feedback Submission Logic
function handleFeedbackSubmit(e) {
    e.preventDefault();

    const teacherId = $('#teacherSelect').val();
    const subject = $('#subjectSelect').val();
    const suggestion = $('#suggestion').val().trim();

    if (!teacherId) {
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

    setTimeout(() => {
        const currentUser = JSON.parse(sessionStorage.getItem('loggedInUser'));
        const feedbacks = JSON.parse(localStorage.getItem('feedbacks'));
        const feedbackEntry = {
            studentId: currentUser ? currentUser.id : 'unknown',
            teacherId,
            subject,
            ratings,
            suggestion,
            date: new Date().toISOString()
        };

        feedbacks.push(feedbackEntry);
        localStorage.setItem('feedbacks', JSON.stringify(feedbacks));

        hideLoader();
        
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();

        setTimeout(() => {
            window.location.href = 'student_dashboard.html';
        }, 2000);

    }, 1500);
}