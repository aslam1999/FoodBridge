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

// =========================
// Volunteer Dashboard - Status Updates
// =========================

document.addEventListener("DOMContentLoaded", function () {
  const assignmentCards = document.querySelectorAll(".assignment-card");
  if (!assignmentCards.length) return;

  assignmentCards.forEach(function (card) {
    const acceptBtn = card.querySelector(".btn-primary");
    const completeBtn = card.querySelector(".btn-small:not(.disabled)");
    const statusBadge = card.querySelector(".status");

    // Accept Assignment
    if (acceptBtn) {
      acceptBtn.addEventListener("click", function () {
        statusBadge.textContent = "Accepted";
        statusBadge.className = "status accepted";
        acceptBtn.remove();
        const declineBtn = card.querySelector(".btn-secondary");
        if (declineBtn) declineBtn.remove();

        const completeButton = document.createElement("button");
        completeButton.className = "btn-small";
        completeButton.textContent = "Mark Pickup Complete";
        card.querySelector(".assignment-actions").appendChild(completeButton);

        // Attach complete listener to new button
        completeButton.addEventListener("click", function () {
          markComplete(card, statusBadge, completeButton);
        });
      });
    }

    // Mark Complete
    if (completeBtn) {
      completeBtn.addEventListener("click", function () {
        markComplete(card, statusBadge, completeBtn);
      });
    }
  });

  function markComplete(card, statusBadge, btn) {
    statusBadge.textContent = "Completed";
    statusBadge.className = "status completed";
    card.classList.add("completed-card");
    btn.textContent = "Completed";
    btn.classList.add("disabled");
    btn.disabled = true;
  }
});
