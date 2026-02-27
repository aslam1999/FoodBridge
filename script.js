// =========================
// Registration Page
// =========================

function switchRole(role) {
  const donorBtn = document.getElementById("toggle-donor");
  const volunteerBtn = document.getElementById("toggle-volunteer");
  const volunteerFields = document.getElementById("volunteer-fields");
  const roleField = document.getElementById("role-field");

  if (role === "donor") {
    donorBtn.classList.add("active");
    donorBtn.setAttribute("aria-pressed", "true");
    volunteerBtn.classList.remove("active");
    volunteerBtn.setAttribute("aria-pressed", "false");
    volunteerFields.classList.remove("visible");
  } else {
    volunteerBtn.classList.add("active");
    volunteerBtn.setAttribute("aria-pressed", "true");
    donorBtn.classList.remove("active");
    donorBtn.setAttribute("aria-pressed", "false");
    volunteerFields.classList.add("visible");
  }

  roleField.value = role;
}

const registerForm = document.getElementById("register-form");
if (registerForm) {
  registerForm.addEventListener("submit", function (e) {
    const pw = document.getElementById("password").value;
    const cpw = document.getElementById("confirm-password").value;

    if (pw.length < 8) {
      e.preventDefault();
      alert("Password must be at least 8 characters.");
      document.getElementById("password").focus();
      return;
    }

    if (pw !== cpw) {
      e.preventDefault();
      alert("Passwords do not match. Please try again.");
      document.getElementById("confirm-password").focus();
    }
  });
}
