jQuery(document).ready(function ($) {
    $('#background_color').on('change', function () {
        $('#slider-preview').css('background-color', $(this).val());
    });

    $('#font_size').on('change', function () {
        $('#slider-preview').css('font-size', $(this).val() + 'px');
    });
});