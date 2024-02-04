/* 
Version: 0.0.7.2

im-meme-player-script.js
JavaScript file for the Meme Player plugin.
*/

document.addEventListener('DOMContentLoaded', function() {
    // Constants
    const ajaxurl = my_meme_player_ajax.ajax_url; // AJAX URL provided by WordPress for handling AJAX requests
    const action = 'fetch_meme_files'; // Action hook name for AJAX requests to fetch meme files
    const IMAGE_DISPLAY_TIME = 5000; // Display time for images in milliseconds (5 seconds)
    let currentIndex = 0; // Current index for navigating through the media files array
    let files = []; // Array to hold the media files fetched from the server
    let currentTimeout; // Timeout ID for managing image display intervals

    // Function to shuffle the media files array for a randomized presentation
    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]]; // Swap elements
        }
        return array;
    }

    // Function to log and handle media loading errors
    function handleMediaError(mediaElement, type) {
        console.error(`Failed to load ${type}:`, mediaElement.src);
        mediaElement.remove(); // Remove the element to avoid displaying broken media
    }

    // Function to handle video playback, ensuring continuous play
    function handleVideoPlayback(videoElement, files, index) {
        videoElement.addEventListener('ended', () => {
            // Loop to the next media file upon video end
            displayFilesSequentially(files, (index + 1) % files.length);
        });
    }

    // Function to navigate through media files (next or previous)
    function navigateFiles(step) {
        currentIndex = (currentIndex + step + files.length) % files.length;
        displayFilesSequentially(files, currentIndex);
    }

    // Event listeners for navigation buttons
    document.getElementById('prevButton').addEventListener('click', () => navigateFiles(-1));
    document.getElementById('nextButton').addEventListener('click', () => navigateFiles(1));

    // Async function to fetch media files from the server with optional filters
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

    // Async function to update the display of media files based on filters
    async function updateMediaDisplay(filters) {
        files = await fetchMedia(filters);
        displayFilesSequentially(files, 0);
    }

    // Event listeners for filtering options (tags, categories, search)
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

    // Initial fetch and display of media files
    fetchMedia().then(fetchedFiles => {
        files = fetchedFiles;
        displayFilesSequentially(files, 0);
    }).catch(error => {
        console.error("Network error:", error);
    });

    // Core function to display media files sequentially
    async function displayFilesSequentially(files, index) {
        currentIndex = index;

        const mediaBoxElement = document.getElementById('mediaBox');
        if (!mediaBoxElement) {
            console.error("Required DOM element 'mediaBox' not found.");
            return;
        }

        // Clear the media box before displaying new media
        while (mediaBoxElement.firstChild) {
            mediaBoxElement.firstChild.remove();
        }

        const file = files[index];
        const fileExtension = file.split('.').pop().toLowerCase();

        // Conditions to handle different media types (images, videos, and now audio)
        if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico', 'tif', 'tiff', 'jfif'].includes(fileExtension)) {
            // Display image files
            const img = document.createElement('img');
            img.src = file;
            img.style.maxWidth = '100%';
            img.style.maxHeight = '85vh'; // Set max height to 85% of viewport height
            img.onerror = () => handleMediaError(img, 'image');
            img.onload = () => {
                clearTimeout(currentTimeout);
                currentTimeout = setTimeout(() => {
                    displayFilesSequentially(files, (index + 1) % files.length);
                }, IMAGE_DISPLAY_TIME);
            };
            mediaBoxElement.appendChild(img);
        } else if (['mp4', 'webm', 'ogg'].includes(fileExtension)) {
            // Display video files
            const videoElement = document.createElement('video');
            videoElement.autoplay = true;
            videoElement.muted = true;
            videoElement.controls = true;
            videoElement.src = file;
            videoElement.style.maxHeight = '85vh';
            videoElement.onerror = () => handleMediaError(videoElement, 'video');
            mediaBoxElement.appendChild(videoElement);
            handleVideoPlayback(videoElement, files, index);
        } else if (['mp3', 'ogg', 'wav'].includes(fileExtension)) {
            // Display audio files
            const audioElement = document.createElement('audio');
            audioElement.controls = true;
            audioElement.src = file;
            audioElement.style.width = '100%';
            audioElement.onerror = () => handleMediaError(audioElement, 'audio');
            mediaBoxElement.appendChild(audioElement);
            // Log successful audio loading
            console.log(`Audio file loaded: ${file}`);
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
                ConsoleLogger::log('Playlist order updated successfully.');
                console.log('Playlist order updated successfully.');
            } else {
                // Handle failure (e.g., invalid nonce, database error)
                ConsoleLogger::error('Failed to update playlist order:', response.error);
                console.error('Failed to update playlist order:', response.error);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Log detailed error information for debugging
            ConsoleLogger::error('AJAX error in updating playlist order:', textStatus, errorThrown);
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
    
       // Initialize sortable playlists for drag-and-drop functionality
       $('.playlist').sortable({
        items: '.playlist-item', // Specify sortable items
        update: function(event, ui) {
            var orderedIds = $(this).sortable('toArray', {attribute: 'data-id'});
            
            // Send the new order to the server
            $.post(ajaxurl, {
                action: 'update_playlist_order',
                order: orderedIds,
                playlist_id: $(this).attr('id').replace('playlist-', ''),
                nonce: my_meme_player_ajax.nonce
            }, function(response) {
                if (response.success) {
                    ConsoleLogger::log('Playlist order updated successfully.');
                    showFeedback('Playlist order updated successfully.');
                } else {
                    ConsoleLogger::error('Failed to update playlist order:', response.data);
                    showFeedback('Failed to update playlist order.', 'error');
                }
            });
        }
    });

    // Event listener for 'Add Media' button
    $('#meme-open-media-library').click(function(e) {
        e.preventDefault();
        var file_frame;
        
        // If the media frame already exists, reopen it.
        if (file_frame) {
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select Media',
            button: {
                text: 'Use this media'
            },
            multiple: false // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            // Assuming you're updating an input field with the media URL
            $('#meme_media_url').val(attachment.url);
            // Show feedback message
            ConsoleLogger::log('Media selected:', attachment.url);
            showFeedback('Media added to playlist successfully.');
        });

        // Finally, open the modal
        file_frame.open();
    });

    // Function to show feedback messages
    function showFeedback(message, type = 'success') {
        var feedbackDiv = $('#meme-playlist-feedback');
        if (!feedbackDiv.length) {
            feedbackDiv = $('<div id="meme-playlist-feedback" class="notice is-dismissible"></div>');
            $('.wrap').prepend(feedbackDiv);
        }
        ConsoleLogger::log('Feedback message:', message);
        feedbackDiv.attr('class', 'notice is-dismissible notice-' + type).text(message).show().delay(5000).fadeOut();
    }

    var $dropArea = $('#mytech-playlist-drop-area');

    // Drag and drop events
    $dropArea.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('is-active');
    }).on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('is-active');
    }).on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('is-active');
        var files = e.originalEvent.dataTransfer.files;
        handleFiles(files);
    });

    // Handle file selection
    function handleFiles(files) {
        var formData = new FormData();
        formData.append('action', 'mytech_upload_playlist_item');
        formData.append('nonce', mytechAjax.nonce);
        formData.append('file', file);

        $.ajax({
            url: mytechAjax.ajax_url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                ConsoleLogger::log('File uploaded successfully.');
                alert(response.data.message);
            }
        });
    }

    // WordPress media uploader
    $('#mytech-select-file').on('click', function(e) {
        e.preventDefault();
        wp.media.frames.file_frame = wp.media({
            title: 'Select or Upload Media',
            button: { text: 'Use this media' },
            multiple: false
        });

        wp.media.frames.file_frame.on('select', function() {
            var attachment = wp.media.frames.file_frame.state().get('selection').first().toJSON();
            ConsoleLogger::log('Selected: ' + attachment.url);
            alert('Selected: ' + attachment.url);
        });

        wp.media.frames.file_frame.open();
    });


    $('#rename_playlist_button').on('click', function() {
        var playlistId = $('#playlist_id').val(); // Assuming an input field for playlist ID
        var newName = $('#new_playlist_name').val(); // Assuming an input field for new name
        var nonce = $('#im_meme_player_rename_playlist_nonce').val(); // Assuming a hidden input field for nonce
    
        $.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'im_meme_player_rename_playlist',
                playlist_id: playlistId,
                new_title: newName,
                security: nonce
            },
            success: function(response) {
                if (response.success) {
                    ConsoleLogger::log('Playlist renamed successfully.');
                    alert('Playlist renamed successfully.');
                } else {
                    ConsoleLogger::error('Error: ' + response.data);
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    $('#delete_playlist_button').on('click', function() {
        var playlistId = $('#playlist_id').val(); // Assuming an input field for playlist ID
        var nonce = $('#im_meme_player_delete_playlist_nonce').val(); // Assuming a hidden input field for nonce

        $.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'im_meme_player_delete_playlist',
                playlist_id: playlistId,
                security: nonce
            },
            success: function(response) {
                if (response.success) {
                    ConsoleLogger::log('Playlist deleted successfully.');
                    alert('Playlist deleted successfully.');
                } else {
                    ConsoleLogger::error('Error: ' + response.data);
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    $('#create_playlist_button').on('click', function() {
        var newName = $('#new_playlist_name').val(); // Assuming an input field for new name
        var nonce = $('#im_meme_player_create_playlist_nonce').val(); // Assuming a hidden input field for nonce

        $.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'im_meme_player_create_playlist',
                new_title: newName,
                security: nonce
            },
            success: function(response) {
                if (response.success) {
                    ConsoleLogger::log('Playlist created successfully.');
                    alert('Playlist created successfully.');
                } else {
                    ConsoleLogger::error('Error: ' + response.data);
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    $('#add_to_playlist_button').on('click', function() {
        var playlistId = $('#playlist_id').val(); // Assuming an input field for playlist ID
        var mediaUrl = $('#meme_media_url').val(); // Assuming an input field for media URL
        var nonce = $('#im_meme_player_add_to_playlist_nonce').val(); // Assuming a hidden input field for nonce

        $.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'im_meme_player_add_to_playlist',
                playlist_id: playlistId,
                media_url: mediaUrl,
                security: nonce
            },
            success: function(response) {
                if (response.success) {
                    ConsoleLogger::log('Media added to playlist successfully.');
                    alert('Media added to playlist successfully.');
                } else {
                    ConsoleLogger::error('Error: ' + response.data);
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    $('#remove_from_playlist_button').on('click', function() {
        var playlistId = $('#playlist_id').val(); // Assuming an input field for playlist ID
        var mediaId = $('#media_id').val(); // Assuming an input field for media ID
        var nonce = $('#im_meme_player_remove_from_playlist_nonce').val(); // Assuming a hidden input field for nonce

        $.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'im_meme_player_remove_from_playlist',
                playlist_id: playlistId,
                media_id: mediaId,
                security: nonce
            },
            success: function(response) {
                if (response.success) {
                    ConsoleLogger::log('Media removed from playlist successfully.');
                    alert('Media removed from playlist successfully.');
                } else {
                    ConsoleLogger::error('Error: ' + response.data);
                    alert('Error: ' + response.data);
                }
            }
        });
    });

    $('#update_playlist_button').on('click', function() {
        var playlistId = $('#playlist_id').val(); // Assuming an input field for playlist ID
        var newName = $('#new_playlist_name').val(); // Assuming an input field for new name
        var nonce = $('#im_meme_player_update_playlist_nonce').val(); // Assuming a hidden input field for nonce

        $.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                action: 'im_meme_player_update_playlist',
                playlist_id: playlistId,
                new_title: newName,
                security: nonce
            },
            success: function(response) {
                if (response.success) {
                    ConsoleLogger::log('Playlist updated successfully.');
                    alert('Playlist updated successfully.');
                } else {
                    ConsoleLogger::error('Error: ' + response.data);
                    alert('Error: ' + response.data);
                }
            }
        });
    });
});
    