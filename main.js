
document.addEventListener("DOMContentLoaded", () =>
{
    // This will get the html elements by their ID to manipulate them with js
    const hamburger = document.getElementById("hamburger-button");
    const navLinks = document.getElementById("nav-links");

    // check if the hamburger exists on the page 
    if(hamburger) 
    {
        // "listen for a click on the hamburger button"
        hamburger.addEventListener("click" , () => 
        {
            // the toggle will add the class active if its not there and remove it if it is 
            navLinks.classList.toggle("active");
        });
    }
});

function setupImagePreview(inputId, previewId, placeholderId) 
{
    const inputElement = document.getElementById(inputId);
    const previewElement = document.getElementById(previewId);
    const placeholderElement = document.getElementById(placeholderId);

    // Make sure the elements actually exist on the page before running
    if (inputElement && previewElement && placeholderElement) 
        {
        inputElement.addEventListener('change', function(event) 
        {
            const file = event.target.files[0];
            
            if (file) 
            {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewElement.src = e.target.result;
                    previewElement.style.display = 'block';
                    placeholderElement.style.display = 'none';
                }
                reader.readAsDataURL(file);
            } 
            else 
            {
                // Reset if they cancel the selection
                previewElement.src = '';
                previewElement.style.display = 'none';
                placeholderElement.style.display = 'block';
            }
        });
    }
}

// Attach the preview logic to your two separate file inputs
setupImagePreview('stl_input', 'stl_preview', 'stl_placeholder');
setupImagePreview('cover_input', 'cover_preview', 'cover_placeholder');
