jQuery(function($){

    var hidden = document.getElementById('custom_document_data');

    // on upload button click
    $('body').on( 'click', '.dominant-upload', function(e){

        e.preventDefault();

        var button = $(this),
            custom_uploader = wp.media({
                title: 'Dodaj dokument',
               /* library : {
                    // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
                    type : 'document'
                },*/
                button: {
                    text: 'Unesi u objavu' // button label text
                },
                multiple: true
            }).on('select', function() { // it also has "open" and "close" events
                //var attachment = custom_uploader.state().get('selection').first().toJSON();
                var attachment = custom_uploader.state().get('selection')
                //button.html('<img src="' + attachment.url + '">').next().val(attachment.id).next().show();
                var output = [];
                $('.added_file_names').text(''); // clear selected names
                attachment.map( function( attached ) {
                    attached = attached.toJSON();
                    output.push({ id: attached.id, url: attached.url, filename: attached.filename });

                    $('.added_file_names').append('<p class="form-field"><label>' + attached.filename + '</label></p>').parent('div').show(); // add new selected name
                });

                hidden.setAttribute( 'value', JSON.stringify( output ));

            }).open();

    });

});