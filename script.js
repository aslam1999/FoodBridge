// =========================
// Registration Page
// =========================

document.addEventListener("DOMContentLoaded", function () {
  const donorBtn = document.getElementById("toggle-donor");
  const volunteerBtn = document.getElementById("toggle-volunteer");
  const volunteerFields = document.getElementById("volunteer-fields");
  const roleField = document.getElementById("role-field");
  const registerForm = document.getElementById("register-form");

  if (!donorBtn || !volunteerBtn) return;

  function switchRole(role) {
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

  donorBtn.addEventListener("click", function () {
    switchRole("donor");
  });
  volunteerBtn.addEventListener("click", function () {
    switchRole("volunteer");
  });

  const togglePw = document.getElementById("toggle-pw");
  if (togglePw) {
    togglePw.addEventListener("click", function () {
      const pwInput = document.getElementById("password");
      if (pwInput.type === "password") {
        pwInput.type = "text";
        togglePw.textContent = "🙈";
      } else {
        pwInput.type = "password";
        togglePw.textContent = "👁️";
      }
    });
  }

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
});

// =========================
// Volunteer Dashboard - Status Updates
// =========================

document.addEventListener("DOMContentLoaded", function () {
  const assignmentCards = document.querySelectorAll(".assignment-card");
  if (!assignmentCards.length) return;

  assignmentCards.forEach(function (card) {
    const acceptBtn = card.querySelector(".accept-btn");
    const declineBtn = card.querySelector(".decline-btn");
    const completeBtn = card.querySelector(".complete-btn");
    const statusBadge = card.querySelector(".status");

    if (acceptBtn) {
      acceptBtn.addEventListener("click", function () {
        const assignmentId = acceptBtn.getAttribute("data-id");
        fetch("update_status.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "assignment_id=" + assignmentId + "&status=accepted",
        })
          .then(function (r) {
            return r.json();
          })
          .then(function (data) {
            if (data.success) {
              statusBadge.textContent = "Accepted";
              statusBadge.className = "status accepted";
              acceptBtn.remove();
              if (declineBtn) declineBtn.remove();
              const completeButton = document.createElement("button");
              completeButton.className = "btn-small complete-btn";
              completeButton.textContent = "Mark Pickup Complete";
              completeButton.setAttribute("data-id", assignmentId);
              card
                .querySelector(".assignment-actions")
                .appendChild(completeButton);
              completeButton.addEventListener("click", function () {
                markComplete(card, statusBadge, completeButton, assignmentId);
              });
            }
          });
      });
    }

    if (completeBtn) {
      const assignmentId = completeBtn.getAttribute("data-id");
      completeBtn.addEventListener("click", function () {
        markComplete(card, statusBadge, completeBtn, assignmentId);
      });
    }
  });

  function markComplete(card, statusBadge, btn, assignmentId) {
    fetch("update_status.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "assignment_id=" + assignmentId + "&status=completed",
    })
      .then(function (r) {
        return r.json();
      })
      .then(function (data) {
        if (data.success) {
          statusBadge.textContent = "Completed";
          statusBadge.className = "status completed";
          card.classList.add("completed-card");
          btn.textContent = "Completed";
          btn.className = "btn-completed";
          btn.disabled = true;
        }
      });
  }
});

// =========================
// Login Error Messages
// =========================

document.addEventListener("DOMContentLoaded", function () {
  const params = new URLSearchParams(window.location.search);

  if (params.get("error") === "invalid_credentials") {
    const alert = document.createElement("div");
    alert.className = "alert alert-error";
    alert.textContent = "Invalid email or password. Please try again.";

    const form = document.querySelector(".login-form");
    if (form) form.insertBefore(alert, form.firstChild);
  }

  if (params.get("success") === "registered") {
    const alert = document.createElement("div");
    alert.className = "alert alert-success";
    alert.textContent = "Account created successfully! Please log in.";

    const form = document.querySelector(".login-form");
    if (form) form.insertBefore(alert, form.firstChild);
  }
});
