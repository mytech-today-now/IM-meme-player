document.addEventListener('DOMContentLoaded', function() {
    // Constants
    const API_ENDPOINT = 'https://insidiousmeme.com/filelist.php';
    const IMAGE_DISPLAY_TIME = 5000; // 5 seconds
    const MEDIA_DIRECTORY = 'https://insidiousmeme.com/presenta/memes/';
    let currentIndex = 0; // Current index of the displayed file
    let files = []; // Array to store file names
    let currentTimeout; // Current timeout for image display
    const file = MEDIA_DIRECTORY + files[index]; // Current file 
    const fileExtension = file.split('.').pop().toLowerCase();

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

    // Handle media loading errors
    function handleMediaError(mediaElement, type, files, index) {
        console.error(`Failed to load ${type}:`, mediaElement.src);
        mediaElement.remove();
        // Skip to the next file
        navigateFiles(1, files);
    }

    // Add event listeners for navigation buttons
    document.getElementById('prevButton').addEventListener('click', () => navigateFiles(-1));
    document.getElementById('nextButton').addEventListener('click', () => navigateFiles(1));

    // Display media files sequentially
    function displayFilesSequentially(files, index) {
        currentIndex = index;

        // Check if the file exists (pseudo-code, implement according to your environment)
        if (!fileExists(file)) {
            navigateFiles(1, files);
            return;
        }

        const mediaBoxElement = document.getElementById('mediaBox');
        if (!mediaBoxElement) {
            console.error("Required DOM element not found.");
            return;
        }

        while (mediaBoxElement.firstChild) {
            mediaBoxElement.firstChild.remove();
        }

        const file = MEDIA_DIRECTORY + files[index];
        const fileExtension = file.split('.').pop().toLowerCase();

        if (['jpg', 'jpeg', 'png', 'gif', 'jfif', 'svg'].includes(fileExtension)) {
            const img = document.createElement('img');
            img.src = file;
            img.style.maxWidth = '100%';
            img.style.maxHeight = '100%';
            img.onerror = () => handleMediaError(img, 'image', files, index);
            mediaBoxElement.appendChild(img);

            clearTimeout(currentTimeout);
            currentTimeout = setTimeout(() => {
                displayFilesSequentially(files, (index + 1) % files.length);
            }, IMAGE_DISPLAY_TIME);
        } else if (['mp4', 'webm', 'ogg'].includes(fileExtension)) {
            const videoElement = document.createElement('video');
            videoElement.autoplay = true;
            videoElement.muted = true;
            videoElement.controls = true;
            videoElement.src = file;
            videoElement.onerror = () => handleMediaError(videoElement, 'video', files, index);
            mediaBoxElement.appendChild(videoElement);

            handleVideoPlayback(videoElement, files, index);
        }
    }

    // Fetch the list of files
    fetch(API_ENDPOINT, { method: 'GET' })
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
