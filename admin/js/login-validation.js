document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("loginForm");
    const emailInput = document.getElementById("inputEmailAddress");
    const emailFeedback = document.getElementById("emailFeedback");
    const passwordInput = document.getElementById("inputPassword");
    const passwordFeedback = document.getElementById("passwordFeedback");
    
    // Elements for the password strength feature
    const strengthBar = document.getElementById("strengthBar");
    const strengthText = document.getElementById("strengthText");

    // Real-time Email Validation
    function validateEmail() {
        if (emailInput.checkValidity()) {
            emailInput.classList.remove("is-invalid");
            emailInput.classList.add("is-valid");
        } else {
            emailInput.classList.remove("is-valid");
            emailInput.classList.add("is-invalid");

            if (emailInput.value.trim() === "") {
                emailFeedback.textContent = "Email field cannot be empty.";
            } else {
                emailFeedback.textContent = `"${emailInput.value}" is missing an '@' or has an invalid format.`;
            }
        }
    }

    // Password Validation & Strength Assessment
    function checkPasswordStrength() {
        const val = passwordInput.value;
        let score = 0;

        // Reset if empty
        if (val.trim() === "") {
            passwordInput.classList.remove("is-valid", "is-invalid");
            strengthBar.style.width = "0%";
            strengthText.textContent = "";
            return;
        }

        // 1. Enforce strict minimum length of 7 characters
        if (val.length < 7) {
            passwordInput.classList.remove("is-valid");
            passwordInput.classList.add("is-invalid");
            passwordFeedback.textContent = "Password must be at least 7 characters long.";
            
            // Keep the bar at 25% (Weak) while they are under 7 characters
            strengthBar.classList.remove("bg-warning", "bg-info", "bg-success");
            strengthBar.classList.add("bg-danger");
            strengthBar.style.width = "25%";
            strengthText.textContent = "Too short ❌";
            strengthText.className = "text-danger d-block mt-1";
            return;
        }

        // 2. Evaluate complexity rules (only if length >= 7)
        score++; // Base point for meeting the 7-character minimum length
        if (/[0-9]/.test(val)) score++;        // Contains numbers
        if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++; // Contains both upper/lowercase
        if (/[^A-Za-z0-9]/.test(val)) score++; // Contains special characters

        // Clear out warning indicators since it passes the length check
        passwordInput.classList.remove("is-invalid");
        passwordInput.classList.add("is-valid");
        strengthBar.classList.remove("bg-danger", "bg-warning", "bg-info", "bg-success");

        // 3. Map score metrics to styling states
        switch (score) {
            case 1:
                strengthBar.style.width = "25%";
                strengthBar.classList.add("bg-danger");
                strengthText.textContent = "Strength: Weak ⚠️";
                strengthText.className = "text-danger d-block mt-1";
                break;
            case 2:
                strengthBar.style.width = "50%";
                strengthBar.classList.add("bg-warning");
                strengthText.textContent = "Strength: Fair 😐";
                strengthText.className = "text-warning d-block mt-1";
                break;
            case 3:
                strengthBar.style.width = "75%";
                strengthBar.classList.add("bg-info");
                strengthText.textContent = "Strength: Good 🙂";
                strengthText.className = "text-info d-block mt-1";
                break;
            case 4:
                strengthBar.style.width = "100%";
                strengthBar.classList.add("bg-success");
                strengthText.textContent = "Strength: Strong! 💪";
                strengthText.className = "text-success d-block mt-1";
                break;
        }
    }

    // Event listeners for typed data feedback
    emailInput.addEventListener("input", validateEmail);
    passwordInput.addEventListener("input", checkPasswordStrength);

    // Form submission processing gate
    form.addEventListener("submit", function (event) {
        validateEmail();
        checkPasswordStrength();
        
        // Block submit if form is natively invalid OR password length is under 7 characters
        if (!form.checkValidity() || passwordInput.value.length < 7) {
            event.preventDefault();
            event.stopPropagation();
            
            // Re-trigger visual error if they attempt to submit an empty or short field
            if (passwordInput.value.length < 7) {
                passwordInput.classList.add("is-invalid");
                if(passwordInput.value.trim() === "") {
                    passwordFeedback.textContent = "Password field cannot be empty.";
                }
            }
        }
    });
});
