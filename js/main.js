const passwordInput = document.getElementById("password");
const togglePassword = document.getElementById("togglePassword");

if (passwordInput && togglePassword) {
  togglePassword.addEventListener("click", function () {
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      togglePassword.textContent = "Hide";
    } else {
      passwordInput.type = "password";
      togglePassword.textContent = "Show";
    }
  });
}

const passwordToggles = document.querySelectorAll(".toggle-password");

passwordToggles.forEach(function (button) {
  button.addEventListener("click", function () {
    const targetId = button.dataset.target;
    const passwordInput = document.getElementById(targetId);

    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      button.textContent = "Hide";
    } else {
      passwordInput.type = "password";
      button.textContent = "Show";
    }
  });
});

// ========================================
// PASSWORD MATCHING VALIDATION
// ========================================

const registerForm = document.getElementById("registerForm");

if (registerForm) {
  registerForm.addEventListener("submit", function (event) {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;

    const errorMessage = document.getElementById("password-error");

    if (password !== confirmPassword) {
      event.preventDefault();

      errorMessage.textContent = "Passwords do not match!";
    } else {
      errorMessage.textContent = "";
    }
  });
}
