// Function to convert text to title case (capitalize each word)
function toTitleCase(text) {
    if (!text) return text;
    return text.toLowerCase().split(' ').map(word => {
        // Capitalize first letter of each word
        return word.charAt(0).toUpperCase() + word.slice(1);
    }).join(' ');
}

// Add event listeners to text inputs
document.addEventListener('DOMContentLoaded', function () {
    // Get all text input fields
    const textInputs = document.querySelectorAll('input[type="text"]');

    // Add input event listener to each text field
    textInputs.forEach(input => {
        input.addEventListener('input', function (e) {
            // Get cursor position before changing the value
            const cursorPosition = e.target.selectionStart;

            // Convert to title case
            const newValue = toTitleCase(e.target.value);

            // Only update if the value would actually change
            if (newValue !== e.target.value) {
                e.target.value = newValue;

                // Restore cursor position
                e.target.setSelectionRange(cursorPosition, cursorPosition);
            }
        });
    });
}); 