// Version: 0.0.7.1

jQuery(document).ready(function($) {
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
                    showFeedback('Playlist order updated successfully.');
                } else {
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
        feedbackDiv.attr('class', 'notice is-dismissible notice-' + type).text(message).show().delay(5000).fadeOut();
    }
});
