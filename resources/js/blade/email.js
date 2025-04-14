document.addEventListener('DOMContentLoaded', function () {
    const firstNameInput = document.querySelector('input[name="firstname"]');
    const lastNameInput = document.querySelector('input[name="lastname"]');
    const emailPreview = document.getElementById('emailPrefixPreview');
    const fullEmailInput = document.getElementById('emailFull');

    // Check if manual adjustment input gets used
    let isManuallyEdited = false;

    emailPreview.addEventListener('input', () => {
        isManuallyEdited = true;
        if (fullEmailInput) {
            fullEmailInput.value = `${emailPreview.value}@docent.syntrapxl.be`;
        }
    });

    // Update the email depending on the first and last name inputs
    function updateEmailPreview() {
        if (isManuallyEdited) return;

        const first = (firstNameInput?.value || '')
            .trim()
            .toLowerCase();

        const last = (lastNameInput?.value || '')
            .trim()
            .toLowerCase()
            .replace(/\s+/g, '.');

        emailPreview.value = `${first}-${last}`;
        fullEmailInput.value = `${emailPreview.value}@docent.syntrapxl.be`;
    }

    // Attach event listener
    firstNameInput.addEventListener('input', updateEmailPreview);
    lastNameInput.addEventListener('input', updateEmailPreview);
});