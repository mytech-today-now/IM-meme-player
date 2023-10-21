document.addEventListener('DOMContentLoaded', function() {
    // Constants
    const API_ENDPOINT = 'https://insidiousmeme.com/filelist.php';
    const CONFIG_ENDPOINT = 'https://insidiousmeme.com/config.php';
    const IMAGE_DISPLAY_TIME = 5000; // 5 seconds

    let MEDIA_DIRECTORY;

    // Fisher-Yates shuffle algorithm
    // This algorithm is used to shuffle the array in a random order
    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }

    function createImageElement(src) {
        const img = document.createElement('img');
        img.src = src;
        img.style.maxWidth = '100%';
        img.style.maxHeight = '100%';
        img.onerror = () => console.error(`Failed to load image: ${src}`);
        return img;
    }

    function createVideoElement(src) {
        const video = document.createElement('video');
        video.autoplay = true;
        video.muted = true;
        video.controls = true;
        video.src = src;
        video.onerror = () => console.error(`Failed to load video: ${src}`);
        video.addEventListener('ended', fetchAndDisplayFiles); // Move to next media file when video ends
        return video;
    }

    function displayMediaFile(mediaBoxElement, file) {
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
                console.error(`Unsupported file type: ${fileType}`);
                return;
        }

        mediaBoxElement.appendChild(mediaElement);
    }

    function fetchAndDisplayFiles() {
        fetch(API_ENDPOINT, { method: 'GET' })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch from API');
                }
                return response.json();
            })
            .then(data => {
                if (!Array.isArray(data)) {
                    throw new Error('Unexpected API response format');
                }
                const shuffledFiles = shuffleArray(data);
                const mediaBoxElement = document.getElementById('mediaBox');
                displayMediaFile(mediaBoxElement, MEDIA_DIRECTORY + shuffledFiles[0]);
            })
            .catch(error => {
                console.error(`Error: ${error.message}`);
            });
    }

    // Fetch configuration (e.g., MEDIA_DIRECTORY)
    function fetchConfig() {
        fetch(CONFIG_ENDPOINT, { method: 'GET' })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch configuration');
                }
                return response.json();
            })
            .then(config => {
                MEDIA_DIRECTORY = config.MEDIA_DIRECTORY;
                fetchAndDisplayFiles();
            })
            .catch(error => {
                console.error(`Configuration Error: ${error.message}`);
            });
    }

    fetchConfig();
});
