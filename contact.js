document.getElementById("contactForm").addEventListener("submit", function (event) {
    event.preventDefault(); 

    if (validateForm()) {
        alert("Thank you! Your message has been sent successfully.");
        this.reset(); 

        document.querySelectorAll("input, textarea, select").forEach(field => field.style.borderColor = "");
    } else {
        alert("Please correct the errors in the form.");
    }
});

function validateForm() {
    let isValid = true;

    // validate full name 
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

    // validate email address with regular expression
    const emailField = document.getElementById("email");
    const emailError = document.getElementById("emailError");
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; 
    if (!emailPattern.test(emailField.value.trim())) {
        emailError.textContent = "Please enter a valid email address.";
        emailField.style.borderColor = "red";
        isValid = false;
    } else {
        emailError.textContent = "";
        emailField.style.borderColor = "green";
    }

    // validate subject dropdown
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

    // validate message
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

    return isValid; 
}
