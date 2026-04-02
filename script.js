// =========================
// Registration Page
// =========================

document.addEventListener("DOMContentLoaded", function () {
  const donorBtn = document.getElementById("toggle-donor");
  const volunteerBtn = document.getElementById("toggle-volunteer");
  const volunteerFields = document.getElementById("volunteer-fields");
  const roleField = document.getElementById("role-field");
  const registerForm = document.getElementById("register-form");

  // Password toggle — runs on ALL pages
  const togglePw = document.getElementById("toggle-pw");
  if (togglePw) {
    togglePw.addEventListener("click", function () {
      const pwInput = togglePw.previousElementSibling;
      if (pwInput.type === "password") {
        pwInput.type = "text";
        togglePw.textContent = "🙈";
      } else {
        pwInput.type = "password";
        togglePw.textContent = "👁️";
      }
    });
  }

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

  fetch("get_session.php")
    .then(function (response) {
      return response.json();
    })
    .then(function (data) {
      if (data.loggedIn) {
        document.getElementById("contact-name").value = data.name;
        document.getElementById("contact-email").value = data.email;
        document.getElementById("contact-phone").value = data.phone;
        document.getElementById("address").value = data.address;
        document.getElementById("city").value = data.city;
        document.getElementById("postal").value = data.postal;
        document.getElementById("contact-name").removeAttribute("required");
        document.getElementById("contact-email").removeAttribute("required");
        document.getElementById("contact-phone").removeAttribute("required");
      }
    });
});

// =========================
// Donation Form Validation
// =========================

document.addEventListener("DOMContentLoaded", function () {
  const donationForm = document.querySelector(".donation-form");
  if (!donationForm) return;

  donationForm.addEventListener("submit", function (e) {
    const foodType = document.getElementById("food-type").value.trim();
    const quantity = document.getElementById("quantity").value;
    const category = document.getElementById("category").value;
    const expiry = document.getElementById("expiry").value;
    const address = document.getElementById("address").value.trim();
    const city = document.getElementById("city").value.trim();
    const postal = document.getElementById("postal").value.trim();
    const pickupDate = document.getElementById("pickup-date").value;
    const pickupTime = document.getElementById("pickup-time").value;
    const today = new Date().toISOString().split("T")[0];

    if (!foodType) {
      e.preventDefault();
      showFormError("Please enter the food type.");
      return;
    }

    if (!quantity || quantity < 1) {
      e.preventDefault();
      showFormError("Please enter a valid quantity.");
      return;
    }

    if (!category) {
      e.preventDefault();
      showFormError("Please select a food category.");
      return;
    }

    if (!expiry) {
      e.preventDefault();
      showFormError("Please enter the best before date.");
      return;
    }

    if (expiry < today) {
      e.preventDefault();
      showFormError("Best before date cannot be in the past.");
      return;
    }

    if (!address || !city || !postal) {
      e.preventDefault();
      showFormError("Please fill in all pickup address fields.");
      return;
    }

    if (!pickupDate) {
      e.preventDefault();
      showFormError("Please select a preferred pickup date.");
      return;
    }

    if (pickupDate < today) {
      e.preventDefault();
      showFormError("Pickup date cannot be in the past.");
      return;
    }

    if (!pickupTime) {
      e.preventDefault();
      showFormError("Please select a preferred pickup time.");
      return;
    }
  });

  function showFormError(message) {
    // Remove existing alert if any
    const existing = document.querySelector(".alert");
    if (existing) existing.remove();

    const alert = document.createElement("div");
    alert.className = "alert alert-error";
    alert.textContent = message;

    const section = document.querySelector(".donation-section");
    section.insertBefore(alert, section.querySelector(".donation-form"));

    // Scroll to top of form
    alert.scrollIntoView({ behavior: "smooth" });
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

// =========================
// Dynamic Nav
// =========================

document.addEventListener("DOMContentLoaded", function () {
  const navList = document.querySelector(".nav-list");
  if (!navList) return;

  // Only run on static HTML pages (not PHP dashboards)
  if (window.location.pathname.includes(".php")) return;

  fetch("get_session.php")
    .then(function (response) {
      return response.json();
    })
    .then(function (data) {
      if (data.loggedIn) {
        const role = data.role;
        let dashboardLink = "donor-dashboard.php";
        if (role === "volunteer") dashboardLink = "volunteer-dashboard.php";
        if (role === "admin") dashboardLink = "admin-dashboard.php";

        navList.innerHTML = `
          <li><a href="${dashboardLink}">My Dashboard</a></li>
          <li><a href="logout.php">Logout</a></li>
        `;
      }
    });
});

// =========================
// Flatpickr Date/Time Pickers
// =========================

document.addEventListener("DOMContentLoaded", function () {
  if (typeof flatpickr === "undefined") return;

  flatpickr("#expiry", {
    minDate: "today",
    dateFormat: "Y-m-d",
  });

  flatpickr("#pickup-date", {
    minDate: "today",
    dateFormat: "Y-m-d",
  });

  flatpickr("#pickup-time", {
    enableTime: true,
    noCalendar: true,
    dateFormat: "h:i K",
    time_24hr: false,
  });
});

// =========================
// Inline Field Validation (blur)
// =========================

document.addEventListener("DOMContentLoaded", function () {
  const forms = document.querySelectorAll(".donation-form, .register-form");
  console.log("Forms found:", forms.length);
  if (!forms.length) return;

  function showFieldError(input, message) {
    input.classList.add("invalid");
    input.classList.remove("valid");
    let error = input.parentElement.querySelector(".field-error");
    if (!error) {
      error = document.createElement("span");
      error.className = "field-error";
      input.parentElement.appendChild(error);
    }
    error.textContent = message;
  }

  function clearFieldError(input) {
    input.classList.remove("invalid");
    input.classList.add("valid");
    const error = input.parentElement.querySelector(".field-error");
    if (error) error.remove();
  }

  forms.forEach(function (form) {
    const requiredFields = form.querySelectorAll(
      "input[required], select[required], textarea[required]",
    );
    console.log("Required fields found:", requiredFields.length);

        }
      });
    });
  });
});
