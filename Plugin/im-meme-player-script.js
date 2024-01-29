/* 
Version: 0.0.7.1
*/

document.addEventListener('DOMContentLoaded', function() {
    // Constants
    const ajaxurl = my_meme_player_ajax.ajax_url; // WordPress AJAX URL for handling AJAX requests
    const action = 'fetch_meme_files'; // Action hook for AJAX to fetch meme files
    const IMAGE_DISPLAY_TIME = 5000; // Time in milliseconds to display each image (5 seconds)
    let currentIndex = 0; // Current index of the displayed file in the files array
    let files = []; // Array to store file names
    let currentTimeout; // Current timeout for image display, used to manage timing

    // Fisher-Yates shuffle algorithm to randomize the order of files
    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]]; // Swap elements
        }
        return array;
    }

    // Handle media loading errors
    function handleMediaError(mediaElement, type) {
        console.error(`Failed to load ${type}:`, mediaElement.src);
        mediaElement.remove(); // Remove the media element if it fails to load
    }

    // Function to handle video playback
    function handleVideoPlayback(videoElement, files, index) {
        videoElement.addEventListener('ended', () => {
            // When video ends, display the next file
            displayFilesSequentially(files, (index + 1) % files.length);
        });
    }

    // Navigate through files using the provided step (next or previous)
    function navigateFiles(step) {
        currentIndex = (currentIndex + step + files.length) % files.length;
        displayFilesSequentially(files, currentIndex);
    }

    // Add event listeners for navigation buttons
    document.getElementById('prevButton').addEventListener('click', () => navigateFiles(-1));
    document.getElementById('nextButton').addEventListener('click', () => navigateFiles(1));

    // Function to fetch media with optional filters (tags, categories, search query)
    async function fetchMedia(filters = {}) {
        const params = new URLSearchParams({ action, ...filters });
        const response = await fetch(ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params.toString()
        });

        if (!response.ok) {
            console.error('Network response was not ok');
            return [];
        }

        const jsonData = await response.json();
        if (jsonData.error) {
            console.error(jsonData.error);
            return [];
        }

        return shuffleArray(Object.values(jsonData));
    }

    // Function to update media display based on filters
    async function updateMediaDisplay(filters) {
        files = await fetchMedia(filters);
        displayFilesSequentially(files, 0);
    }

    // Event listeners for filter options (tags, categories, search)
    document.getElementById('tagFilter').addEventListener('change', (event) => {
        updateMediaDisplay({ tag: event.target.value });
    });

    document.getElementById('categoryFilter').addEventListener('change', (event) => {
        updateMediaDisplay({ category: event.target.value });
    });

    document.getElementById('searchForm').addEventListener('submit', (event) => {
        event.preventDefault();
        const query = document.getElementById('searchInput').value;
        updateMediaDisplay({ search: query });
    });

    // Initial fetch and display
    fetchMedia().then(fetchedFiles => {
        files = fetchedFiles;
        displayFilesSequentially(files, 0);
    }).catch(error => {
        console.error("Network error:", error);
    });

    // Display media files sequentially
    async function displayFilesSequentially(files, index) {
        currentIndex = index;

        const mediaBoxElement = document.getElementById('mediaBox');
        if (!mediaBoxElement) {
            console.error("Required DOM element not found.");
            return;
        }

        // Clear existing media before displaying new media
        while (mediaBoxElement.firstChild) {
            mediaBoxElement.firstChild.remove();
        }

        const file = files[index];
        const fileExtension = file.split('.').pop().toLowerCase();

        // Display image files
        if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico', 'tif', 'tiff', 'jfif'].includes(fileExtension)) {
            const img = document.createElement('img');
            img.src = file;
            img.style.maxWidth = '100%';
            img.style.maxHeight = '85vh'; // Set max height to 85% of viewport height
            img.onerror = () => handleMediaError(img, 'image');

            // On image load, set a timeout to display the next file after a fixed duration
            img.onload = () => {
                clearTimeout(currentTimeout);
                currentTimeout = setTimeout(() => {
                    displayFilesSequentially(files, (index + 1) % files.length);
                }, IMAGE_DISPLAY_TIME);
            };

            mediaBoxElement.appendChild(img);
        } 
        // Display video files
        else if (['mp4', 'webm', 'ogg'].includes(fileExtension)) {
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
    };
});

/**
 * Updates the order of playlist items on the server.
 * @param {Array} orderedIds - Array of item IDs in their new order.
 */
function updatePlaylistOrder(orderedIds) {
    // Prepare data for AJAX request
    const data = {
        action: 'update_playlist_order', // WordPress action hook
        ordered_ids: orderedIds,         // Ordered IDs of playlist items
        nonce: my_meme_player_ajax.nonce // Nonce for security verification
    };

    // AJAX request to update the playlist order
    $.ajax({
        url: my_meme_player_ajax.ajax_url, // AJAX URL provided by WordPress
        type: 'POST',
        data: data,
        success: function(response) {
            // Handle success
            if (response.success) {
                console.log('Playlist order updated successfully.');
            } else {
                // Handle failure (e.g., invalid nonce, database error)
                console.error('Failed to update playlist order:', response.error);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Log detailed error information for debugging
            console.error('AJAX error in updating playlist order:', textStatus, errorThrown);
        }
    });
}

// Sortable playlist items for Meme Player Playlist Edit page
$( ".playlist-items" ).sortable({
    update: function(event, ui) {
        var orderedIds = $(this).sortable('toArray').map(function(item) {
            return $(item).data('id');
        });
        updatePlaylistOrder(orderedIds);
    }
});

// Folder Selector for Meme Player
jQuery(document).ready(function($) {
    $('#folder_path_button').click(function(e) {
        e.preventDefault();
        var file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select Folder',
            button: {
                text: 'Use this folder'
            },
            multiple: false
        });
        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            $('#folder_path').val(attachment.url);
        });
        file_frame.open();
    });
});