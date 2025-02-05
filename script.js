// Form validation for registration and login
// Form validation for registration
function validateForm() {
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;
    var firstName = document.getElementById("first_name").value;
    var lastName = document.getElementById("last_name").value;
    var email = document.getElementById("email").value;

    if (username === "" || password === "" || confirmPassword === "" || firstName === "" || lastName === "" || email === "") {
        alert("All fields must be filled out");
        return false;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match. Please try again.");
        return false;
    }

    return true;
}

// JavaScript for handling modal interactions remains unchanged

let userIdToDelete = null;

// Function to show the modal
function showModal(userId) {
    userIdToDelete = userId; // Store the user ID to delete
    document.getElementById('deleteModal').style.display = 'flex';
}

// Function to hide the modal
function hideModal() {
    document.getElementById('deleteModal').style.display = 'none';
    userIdToDelete = null; // Reset the user ID
}

// Function to confirm deletion
function confirmDelete() {
    if (userIdToDelete) {
        window.location.href = `delete_user.php?id=${userIdToDelete}`;
    }
}

// Get modal and button elements
const modal = document.getElementById("addSubjectModal");
const btn = document.getElementById("addSubjectBtn");
const closeBtn = document.querySelector(".close-btn");

// Open modal on button click
btn.addEventListener("click", () => {
    modal.style.display = "block";
});

// Close modal when the close button is clicked
closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
});

// Close modal when clicking outside the modal content
window.addEventListener("click", (event) => {
    if (event.target === modal) {
        modal.style.display = "none";
    }
});


// script.js
window.onload = function() {
    // Get the modal
    var modal = document.getElementById('subjectModal');
    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}

// Redirect to login page on clicking "OK" in the popup
document.addEventListener("DOMContentLoaded", () => {
    const okButton = document.getElementById("okButton");
    if (okButton) {
        okButton.addEventListener("click", () => {
            window.location.href = "login.php"; // Redirect to login page
        });
    }
});

// Ensure window.onclick logic does not interfere with dropdown behavior
window.onload = function () {
    var modal = document.getElementById('assignModal');
    var span = document.getElementsByClassName("close")[0];

    // Close modal when clicking the close button
    if (span) {
        span.onclick = function () {
            modal.style.display = 'none';
        };
    }

    // Close modal when clicking outside the modal content
    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
};

// Prevent form redirection on modal interaction
function showAssignPopup(event) {
    event.preventDefault();
    document.getElementById('assignModal').style.display = 'block';
}

function hideAssignPopup() {
    document.getElementById('assignModal').style.display = 'none';
}

// Form validation for assigning subjects
function validateAssignForm() {
    var student = document.getElementById('student_id').value;
    var subject = document.getElementById('subject_id').value;

    if (student === "" || subject === "") {
        alert("Both student and subject must be selected.");
        return false;
    }

    return true;
}




