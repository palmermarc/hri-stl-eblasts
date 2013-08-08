jQuery(document).ready(function () {
    var formfield;
    var id;
    jQuery('.upload_image_button').click(function () {
        jQuery('html').addClass('Image');
        formfield = jQuery(this).prev().attr('name');
        id = jQuery(this).next().attr('name');

        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });
    window.original_send_to_editor = window.send_to_editor;
    window.send_to_editor = function (html) {
        if (formfield) {
            fileurl = jQuery('img', html).attr('src');
            jQuery('#' + formfield).val(fileurl);
            jQuery('#' + formfield + '_preview').html('<img src="' + fileurl + '" alt="" title="" />');

            imgclass = jQuery('img', html).attr('class');
            imgid = parseInt(imgclass.replace(/\D/g, ''), 10);
            jQuery('#' + id).val(imgid);

            tb_remove();
            jQuery('html').removeClass('Image');
        } else {
            window.original_send_to_editor(html);
        }
    };
});