function checkPasswordStrength() {
    var password = document.getElementById("password").value;
    var helpText = document.getElementById("passwordHelp");

    // Regex pattern for strong password
    var strongPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    if (strongPassword.test(password)) {
        helpText.style.color = "green";
        helpText.innerHTML = "Strong Password!";
    } else {
        helpText.style.color = "red";
        helpText.innerHTML = "Password must be 8+ characters, with uppercase, lowercase, number, and special character.";
    }
}

// Password Strength Checker (already added earlier)

const togglePassword = document.querySelector('#togglePassword');
const passwordField = document.querySelector('#password');

togglePassword.addEventListener('click', function (e) {
    // toggle the type attribute
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
    
    // toggle the eye / eye slash icon
    this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
});
