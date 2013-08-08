jQuery(document).ready(function($) {
	var uploadID = null;
	
    $(document).on('click', '.upload_image_button', function() {
        console.log('The upload button has been clicked.');
        $('html').addClass('Image');
        uploadID = '#' + $(this).prev().attr('id');
        
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });
    
    window.original_send_to_editor = window.send_to_editor;
    
    window.send_to_editor = function (html) {
        if (uploadID != null) {
            fileurl = $('img', html).attr('src');
            $(uploadID).val(fileurl);
            $(uploadID + '_preview').html('<img src="' + fileurl + '" alt="" title="" />');
    		
            tb_remove();
            $('html').removeClass('Image');
        } else {
            window.original_send_to_editor(html);
        }
    };
});