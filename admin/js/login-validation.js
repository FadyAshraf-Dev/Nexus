document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#loginForm");
    const emailInput = document.querySelector("#inputEmailAddress");
    const passwordInput = document.querySelector("#inputPassword");

    const emailFeedback = document.querySelector("#emailFeedback");
    const passwordFeedback = document.querySelector("#passwordFeedback");

    // Baseline configuration constraints
    const MIN_PASSWORD_LENGTH = 8;

    /* ------------------------- Core Validation Rules ------------------------- */

    function validateEmail() {
        const value = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (value === "") {
            emailFeedback.innerText = "Email address cannot be empty.";
            emailInput.classList.add("is-invalid");
            emailInput.classList.remove("is-valid");
            return false;
        } else if (!emailRegex.test(value)) {
            emailFeedback.innerText = "Please enter a valid email address style structure.";
            emailInput.classList.add("is-invalid");
            emailInput.classList.remove("is-valid");
            return false;
        } else {
            emailInput.classList.remove("is-invalid");
            emailInput.classList.add("is-valid");
            return true;
        }
    }

    function validatePassword() {
        const value = passwordInput.value; // Avoid trimming if spaces are valid passwords

        if (value === "") {
            passwordFeedback.innerText = "Password field cannot be empty.";
            passwordInput.classList.add("is-invalid");
            passwordInput.classList.remove("is-valid");
            return false;
        } else if (value.length < MIN_PASSWORD_LENGTH) {
            passwordFeedback.innerText = `Password must be at least ${MIN_PASSWORD_LENGTH} characters long.`;
            passwordInput.classList.add("is-invalid");
            passwordInput.classList.remove("is-valid");
            return false;
        } else {
            passwordInput.classList.remove("is-invalid");
            passwordInput.classList.add("is-valid");
            return true;
        }
    }

    /* ------------------------- Real-Time Event Handlers ------------------------- */

    // Re-evaluates instantly as the user types into the input text boxes
    emailInput.addEventListener("input", validateEmail);
    passwordInput.addEventListener("input", validatePassword);

    /* ------------------------- Form Interception Submit ------------------------- */

    form.addEventListener("submit", (event) => {
        const isEmailValid = validateEmail();
        const isPasswordValid = validatePassword();

        // If either checking mechanism evaluates false, block submission entirely
        if (!isEmailValid || !isPasswordValid) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        // Let Bootstrap handle class styling applications
        form.classList.add("was-validated");
    });
});