document.addEventListener('DOMContentLoaded', function() {
    const API_ENDPOINT = './presenta/memes/filelist.php';
    const CONFIG_ENDPOINT = './presenta/memes/config.php';
    const IMAGE_DISPLAY_TIME = 5000;

    let MEDIA_DIRECTORY;
    let shuffledFiles = [];
    let currentIndex = 0;

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

    function createImageElement(src) {
        const img = document.createElement('img');
        img.src = src;
        img.style.maxWidth = '100%';
        img.style.maxHeight = '100%';
        img.onerror = () => {
            const mediaBoxElement = document.getElementById('mediaBox');
            displayError(mediaBoxElement, `Failed to load image: ${src}`);
        };
        return img;
    }

    function createVideoElement(src) {
        const video = document.createElement('video');
        video.autoplay = true;
        video.muted = true;
        video.controls = true;
        video.src = src;
        video.onerror = () => {
            const mediaBoxElement = document.getElementById('mediaBox');
            displayError(mediaBoxElement, `Failed to load video: ${src}`);
        };
        video.addEventListener('ended', displayNextMedia);
        return video;
    }

    function displayMediaFile(mediaBoxElement, file) {
        while (mediaBoxElement.firstChild) {
            mediaBoxElement.removeChild(mediaBoxElement.firstChild);
        }

        const fileType = file.split('.').pop().toLowerCase();
        let mediaElement;

        switch (fileType) {
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'jfif':
            case 'svg':
                mediaElement = createImageElement(file);
                break;
            case 'mp4':
            case 'webm':
            case 'ogg':
                mediaElement = createVideoElement(file);
                break;
            default:
                displayError(mediaBoxElement, `Unsupported file type: ${fileType}`);
                return;
        }

        mediaBoxElement.appendChild(mediaElement);
    }

    function displayNextMedia() {
        currentIndex++;
        if (currentIndex >= shuffledFiles.length) {
            currentIndex = 0;
        }
        const mediaBoxElement = document.getElementById('mediaBox');
        displayMediaFile(mediaBoxElement, MEDIA_DIRECTORY + shuffledFiles[currentIndex]);
    }

    function displayPreviousMedia() {
        currentIndex--;
        if (currentIndex < 0) {
            currentIndex = shuffledFiles.length - 1;
        }
        const mediaBoxElement = document.getElementById('mediaBox');
        displayMediaFile(mediaBoxElement, MEDIA_DIRECTORY + shuffledFiles[currentIndex]);
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
                const mediaBoxElement = document.getElementById('mediaBox');
                displayMediaFile(mediaBoxElement, MEDIA_DIRECTORY + shuffledFiles[currentIndex]);
            })
            .catch(error => {
                const mediaBoxElement = document.getElementById('mediaBox');
                displayError(mediaBoxElement, `Error fetching files: ${error.message}`);
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
                const mediaBoxElement = document.getElementById('mediaBox');
                displayError(mediaBoxElement, `Configuration Error: ${error.message}`);
            });
    }

    document.addEventListener('keydown', handleArrowKeyPress);
    fetchConfig();
});
