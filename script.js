document.addEventListener('DOMContentLoaded', function() {
    // Constants
    const API_ENDPOINT = 'https://insidiousmeme.com/filelist.php';
    const CONFIG_ENDPOINT = 'https://www.insidiousmeme.com/config.php';
    const IMAGE_DISPLAY_TIME = 5000;

    // Variables
    let MEDIA_DIRECTORY;
    let shuffledFiles = [];
    let currentIndex = 0;

    // Utility Functions
    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }

    function displayError(mediaBoxElement, errorMessage) {
        mediaBoxElement.innerHTML = `<div class="error-message">${errorMessage}</div>`;
    }

    function createMediaElement(fileType, file) {
        let mediaElement;
        switch (fileType) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'jfif':
            case 'svg':
                mediaElement = document.createElement('img');
                mediaElement.src = file;
                mediaElement.style.maxWidth = '100%';
                mediaElement.style.maxHeight = '100%';
                mediaElement.onerror = () => displayError(document.getElementById('mediaBox'), `Failed to load image: ${file}`);
                break;
            case 'mp4':
            case 'webm':
            case 'ogg':
                mediaElement = document.createElement('video');
                mediaElement.autoplay = true;
                mediaElement.muted = true;
                mediaElement.controls = true;
                mediaElement.src = file;
                mediaElement.onerror = () => displayError(document.getElementById('mediaBox'), `Failed to load video: ${file}`);
                mediaElement.addEventListener('ended', displayNextMedia);
                break;
            default:
                displayError(document.getElementById('mediaBox'), `Unsupported file type: ${fileType}`);
                return null;
        }
        return mediaElement;
    }

    function displayMediaFile(mediaBoxElement, file) {
        while (mediaBoxElement.firstChild) {
            mediaBoxElement.removeChild(mediaBoxElement.firstChild);
        }

        const fileType = file.split('.').pop().toLowerCase();
        const mediaElement = createMediaElement(fileType, file);
        if (mediaElement) {
            mediaBoxElement.appendChild(mediaElement);
        }
    }

    function displayNextMedia() {
        currentIndex++;
        if (currentIndex >= shuffledFiles.length) {
            currentIndex = 0;
        }
        displayMediaFile(document.getElementById('mediaBox'), MEDIA_DIRECTORY + shuffledFiles[currentIndex]);
    }

    function displayPreviousMedia() {
        currentIndex--;
        if (currentIndex < 0) {
            currentIndex = shuffledFiles.length - 1;
        }
        displayMediaFile(document.getElementById('mediaBox'), MEDIA_DIRECTORY + shuffledFiles[currentIndex]);
    }

    function handleArrowKeyPress(event) {
        if (event.key === 'ArrowRight') {
            displayNextMedia();
        } else if (event.key === 'ArrowLeft') {
            displayPreviousMedia();
        }
    }

    function fetchAndDisplayFiles() {
        fetch(API_ENDPOINT, { method: 'GET' })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to fetch from API. Status: ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (!Array.isArray(data)) {
                    throw new Error('Unexpected API response format');
                }
                shuffledFiles = shuffleArray(data);
                displayMediaFile(document.getElementById('mediaBox'), MEDIA_DIRECTORY + shuffledFiles[currentIndex]);
            })
            .catch(error => {
                displayError(document.getElementById('mediaBox'), `Error fetching files: ${error.message}`);
            });
    }

    function fetchConfig() {
        fetch(CONFIG_ENDPOINT, { method: 'GET' })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to fetch configuration. Status: ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(config => {
                MEDIA_DIRECTORY = config.MEDIA_DIRECTORY;
                fetchAndDisplayFiles();
            })
            .catch(error => {
                displayError(document.getElementById('mediaBox'), `Configuration Error: ${error.message}`);
            });
    }

    // Event Listeners
    document.addEventListener('keydown', handleArrowKeyPress);
    fetchConfig();
});
