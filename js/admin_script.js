jQuery(document).ready(function($) {
			var meta_image_frame;
			  $('.meta-image-button').live('click', function(e){
					e.preventDefault();
					var this_class = $(this);
            meta_image_frame = wp.media.frames.file_frame = wp.media({
                title: 'Portfolio Image Gallery Selection Window',
                button: {text: 'Add to Gallery'},
                library: { type: 'image'},
                  multiple: false
            });
            meta_image_frame.on('select', function(){
                var media_attachment = meta_image_frame.state().get('selection').first().toJSON();
                   var url = '';
				   var id = '';
				   var img_show = '<img src="'+ media_attachment.url +'" style="width:100px;height:100px;">';
				//$(this_class).parent().prepend(img_show);
				
				
				$(this_class).parent().find('.image_label').html(img_show);
				
                //$(this_class).parent().find('.meta-image').val(media_attachment.url);
				$(this_class).parent().find('.meta-image-id').val(media_attachment.id);
            });
            meta_image_frame.open();
         });
         $('#add-input').click(function(event){
            add_input()
        }); 
		$(".remove-row").live('click', function() {
		 $(this).parent().remove();
		});
        function add_input(){
            var input = "<div><span class='image_label'><i class='fa fa-picture-o' aria-hidden='true'></i></span>"
							  +"<input type='hidden' name='meta-image-id[]' class='meta-image-id' value='' />"
                              +"<input type='button' class='meta-image-button button' value='Upload Image' />"
                              +"<span class='button remove-row'>Remove</span></div>";

            $('#images-container').append(input);
        }
});