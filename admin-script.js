jQuery(document).ready(function($) {
    // Add event listener to the 'Add Media' button
    $('#mytech-open-media-library').click(function(e) {
        e.preventDefault();

        var file_frame; // Keep the file frame to reuse if already open

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
            multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on('select', function() {
            // We set multiple to false, so only get one image from the uploader
            var attachment = file_frame.state().get('selection').first().toJSON();

            // Assuming you're passing the URL of the attachment to an input field
            $('#mytech_media_url').val(attachment.url);  // Update the input field with the media URL

            // Optionally, if you want to display the selected image as a preview
            // $('#image-preview').attr('src', attachment.url); 
        });

        // Finally, open the modal
        file_frame.open();
    });
});
