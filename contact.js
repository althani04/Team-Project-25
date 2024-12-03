document.getElementById("contactForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission behavior

    if (validateForm()) {
        alert("Thank you! Your message has been sent successfully.");
        this.reset(); // Reset the form after successful submission
        // Reset field borders after form submission
        document.querySelectorAll("input, textarea, select").forEach(field => field.style.borderColor = "");
    } else {
        alert("Please correct the errors in the form.");
    }
});

function validateForm() {
    let isValid = true;

    // Validate Full Name
    const nameField = document.getElementById("name");
    const nameError = document.getElementById("nameError");
    if (nameField.value.trim() === "" || nameField.value.trim().length < 3) {
        nameError.textContent = "Full Name must be at least 3 characters long.";
        nameField.style.borderColor = "red";
        isValid = false;
    } else { 
        nameError.textContent = "";
        nameField.style.borderColor = "green";
    }

    // Validate Email
    const emailField = document.getElementById("email");
    const emailError = document.getElementById("emailError");
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email pattern
    if (!emailPattern.test(emailField.value.trim())) {
        emailError.textContent = "Please enter a valid email address.";
        emailField.style.borderColor = "red";
        isValid = false;
    } else {
        emailError.textContent = "";
        emailField.style.borderColor = "green";
    }

    // Validate Subject
    const subjectField = document.getElementById("subject");
    const subjectError = document.getElementById("subjectError");
    if (subjectField.value === "") {
        subjectError.textContent = "Please select a subject.";
        subjectField.style.borderColor = "red";
        isValid = false;
    } else {
        subjectError.textContent = "";
        subjectField.style.borderColor = "green";
    }

    // Validate Message
    const messageField = document.getElementById("message");
    const messageError = document.getElementById("messageError");
    if (messageField.value.trim() === "" || messageField.value.trim().length < 5) {
        messageError.textContent = "Message must be at least 5 characters long.";
        messageField.style.borderColor = "red";
        isValid = false;
    } else {
        messageError.textContent = "";
        messageField.style.borderColor = "green";
    }

    return isValid; // Return the overall validation status
}
