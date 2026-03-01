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
// Donation Form - Guest vs Logged-in
// =========================

document.addEventListener("DOMContentLoaded", function () {
  const contactFieldset = document.getElementById("contact-fieldset");
  if (!contactFieldset) return;

  // TODO: Replace with PHP session when backend is ready
  const loggedInUser = null;

  if (loggedInUser) {
    contactFieldset.classList.add("contact-hidden");
    document.getElementById("donor-name").value = loggedInUser.name;
    document.getElementById("donor-email").value = loggedInUser.email;
    document.getElementById("donor-phone").value = loggedInUser.phone;
    document.getElementById("address").value = loggedInUser.address;
    document.getElementById("city").value = loggedInUser.city;
    document.getElementById("postal").value = loggedInUser.postal;
    document.getElementById("donor-name").removeAttribute("required");
    document.getElementById("donor-email").removeAttribute("required");
    document.getElementById("donor-phone").removeAttribute("required");
  }
});

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
