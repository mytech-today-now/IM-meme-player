jQuery(document).ready(function($){
    $('#mytech-open-media-library').click(function(e) {
        e.preventDefault();
        
        var file_frame; // Keep it outside so it can be reopened
        
        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            file_frame.open();
            return;
        }
        
        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select Media',
            button: {
                text: 'Use this media'
            },
            multiple: false  // Set to true if you want to allow multiple files at once
        });
        
        // When an image is selected, run a callback.
        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            // Do something with the attachment here
        });
        
        // Finally, open the modal
        file_frame.open();
    });
});

file_frame.on('select', function() {
    var attachment = file_frame.state().get('selection').first().toJSON();
    
    // Example: Insert URL into an input field or image tag to display
    // These elements should be part of your admin-page.php form or settings area
    $('#image-preview').attr('src', attachment.url); // Display image preview
    $('#image-url-field').val(attachment.url);       // Save image URL to hidden field
});