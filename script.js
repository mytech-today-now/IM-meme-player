document.addEventListener('DOMContentLoaded', function() {
    // Constants
    const API_ENDPOINT = 'https://insidiousmeme.com/filelist.php';
    const IMAGE_DISPLAY_TIME = 5000; // 5 seconds
    const CUSTOM_TOKEN_VALUE = 'MY_CUSTOM_VALUE';
    const MEDIA_DIRECTORY = 'https://insidiousmeme.com/presenta/memes/';

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

    let currentTimeout;

    // Function to handle video playback
    function handleVideoPlayback(videoElement, files, index) {
        let videoTimeout = setTimeout(() => {
            displayFilesSequentially(files, (index + 1) % files.length);
        }, IMAGE_DISPLAY_TIME);

        videoElement.addEventListener('play', () => {
            clearTimeout(videoTimeout);
        });
    }

    // Display media files sequentially
    function displayFilesSequentially(files, index) {
        const mediaBoxElement = document.getElementById('mediaBox');
        if (!mediaBoxElement) {
            console.error("Required DOM element not found.");
            return;
        }

        while (mediaBoxElement.firstChild) {
            mediaBoxElement.firstChild.remove();
        }

        const file = MEDIA_DIRECTORY + files[index];
        console.log(file);
        const fileExtension = file.split('.').pop().toLowerCase();

        if (['jpg', 'jpeg', 'png', 'gif', 'jfif', 'svg'].includes(fileExtension)) {
            const img = document.createElement('img');
            img.src = file;
            img.style.maxWidth = '100%';
            img.style.maxHeight = '100%';
            img.onerror = () => handleMediaError(img, 'image');
            mediaBoxElement.appendChild(img);

            currentTimeout = setTimeout(() => {
                displayFilesSequentially(files, (index + 1) % files.length);
            }, IMAGE_DISPLAY_TIME);
        } else if (['mp4', 'webm', 'ogg'].includes(fileExtension)) {
            const videoElement = document.createElement('video');
            videoElement.autoplay = true;
            videoElement.muted = true;
            videoElement.controls = true;
            videoElement.src = file;
            videoElement.onerror = () => handleMediaError(videoElement, 'video');
            mediaBoxElement.appendChild(videoElement);
            videoElement.play();

            handleVideoPlayback(videoElement, files, index);
        }
    }

    // Fetch the list of files
    fetch(API_ENDPOINT, { method: 'GET' })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(text => {
            try {
                return JSON.parse(text);
            } catch (err) {
                throw new Error(`Invalid JSON response: ${text}`);
            }
        })
        .then(jsonData => {
            if (jsonData.error) {
                console.error(jsonData.error);
                return;
            }

            const files = shuffleArray(Object.values(jsonData));
            displayFilesSequentially(files, 0);
        })
        .catch(error => {
            console.error("Network error:", error);
        });
});