jQuery.noConflict();
jQuery(document).ready(function($) {

    // Shortcode (Copy to clipboard)
    $(document).on("click", "button#copy_shortcode", function() {
        $("input#shortcode").select();
        document.execCommand("copy");
    });
});