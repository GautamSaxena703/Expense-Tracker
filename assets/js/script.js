// Handle flash messages (auto hide after 3 seconds)
document.addEventListener("DOMContentLoaded", function () {
    const alerts = document.querySelectorAll(".alert");
    if (alerts) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500);
            });
        }, 3000);
    }
});

// Confirm delete action
function confirmDelete(event) {
    if (!confirm("Are you sure you want to delete this expense?")) {
        event.preventDefault();
    }
}

// Toggle password visibility
function togglePassword(id) {
    const input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}

// Simple client-side form validation (example for login/register)
function validateForm(event, formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll("input[required]");
    let valid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.border = "2px solid red";
            valid = false;
        } else {
            input.style.border = "1px solid #ccc";
        }
    });

    if (!valid) {
        event.preventDefault();
        alert("Please fill all required fields.");
    }
}
