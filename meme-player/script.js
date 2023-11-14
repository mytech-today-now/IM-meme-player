document.addEventListener('DOMContentLoaded', function() {
    // Constants
    const ajaxurl = my_meme_player_ajax.ajax_url; // WordPress AJAX URL
    const action = 'fetch_meme_files'; // Action hook for AJAX
    const IMAGE_DISPLAY_TIME = 5000; // 5 seconds
    let currentIndex = 0; // Current index of the displayed file
    let files = []; // Array to store file names
    let currentTimeout; // Current timeout for image display

    // Fisher-Yates shuffle algorithm
    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }

    // Handle media loading errors
    function handleMediaError(mediaElement, type) {
        console.error(`Failed to load ${type}:`, mediaElement.src);
        mediaElement.remove();
    }

    // Function to handle video playback
    function handleVideoPlayback(videoElement, files, index) {
        videoElement.addEventListener('ended', () => {
            displayFilesSequentially(files, (index + 1) % files.length);
        });
    }

    // Navigate through files
    function navigateFiles(step) {
        currentIndex = (currentIndex + step + files.length) % files.length;
        displayFilesSequentially(files, currentIndex);
    }

    // Add event listeners for navigation buttons
    document.getElementById('prevButton').addEventListener('click', () => navigateFiles(-1));
    document.getElementById('nextButton').addEventListener('click', () => navigateFiles(1));

    // Display media files sequentially
    async function displayFilesSequentially(files, index) {
        currentIndex = index;
    
        const mediaBoxElement = document.getElementById('mediaBox');
        if (!mediaBoxElement) {
            console.error("Required DOM element not found.");
            return;
        }
    
        // Clear existing media
        while (mediaBoxElement.firstChild) {
            mediaBoxElement.firstChild.remove();
        }
    
        const file = files[index];
        const fileExtension = file.split('.').pop().toLowerCase();
    
        if (['jpg', 'jpeg', 'png', 'gif', 'jfif', 'svg'].includes(fileExtension)) {
            const img = document.createElement('img');
            img.src = file;
            img.style.maxWidth = '100%';
            img.style.maxHeight = '85vh'; // Set max height to 85% of viewport height
            img.onerror = () => handleMediaError(img, 'image');
    
            // Add onload event listener
            img.onload = () => {
                clearTimeout(currentTimeout);
                currentTimeout = setTimeout(() => {
                    displayFilesSequentially(files, (index + 1) % files.length);
                }, IMAGE_DISPLAY_TIME);
            };
    
            mediaBoxElement.appendChild(img);
        } else if (['mp4', 'webm', 'ogg'].includes(fileExtension)) {
            const videoElement = document.createElement('video');
            videoElement.autoplay = true;
            videoElement.muted = true;
            videoElement.controls = true;
            videoElement.src = file;
            videoElement.style.maxHeight = '85vh'; // Set max height for video
            videoElement.onerror = () => handleMediaError(videoElement, 'video');
            mediaBoxElement.appendChild(videoElement);
    
            handleVideoPlayback(videoElement, files, index);
        }
    }

    // Fetch the list of files using WordPress AJAX
    fetch(ajaxurl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=${action}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(jsonData => {
        if (jsonData.error) {
            console.error(jsonData.error);
            return;
        }

        files = shuffleArray(Object.values(jsonData)); // Set and shuffle the files array
        displayFilesSequentially(files, 0); // Start with the first file
    })
    .catch(error => {
        console.error("Network error:", error);
    });
});