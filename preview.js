
jQuery(document).ready(function ($) {
    // Company info section
    $("#section-company-color").on("change", function () {
        $(".section-company").css("color", $(this).val());
    });
    $("#section-company-font-size").on("change", function () {
        $(".section-company").css("font-size", $(this).val() + 'px');
    });
    $("#section-company-font-family").on("change", function () {
        $(".section-company").css("font-family", $(this).val());
    });
    $("#section-company-line-height").on("change", function () {
        $(".section-company").css("line-height", $(this).val() + 'px');
    });

    // Author username and date section
    $("#section-author-date-color").on("change", function () {
        $(".section-author-date").css("color", $(this).val());
    });
    $("#section-author-date-font-size").on("change", function () {
        $(".section-author-date").css("font-size", $(this).val() + 'px');
    });
    $("#section-author-date-font-family").on("change", function () {
        $(".section-author-date").css("font-family", $(this).val());
    });
    $("#section-author-date-font-weight").on("change", function () {
        $(".section-author-date").css("font-weight", $(this).val());
    });
    $("#section-author-date-line-height").on("change", function () {
        $(".section-author-date").css("line-height", $(this).val() + 'px');
    });

    // Post text section
    $("#section-body-color").on("change", function () {
        $(".section-body").css("color", $(this).val());
    });
    $("#section-body-font-size").on("change", function () {
        $(".section-body").css("font-size", $(this).val() + 'px');
    });
    $("#section-body-font-family").on("change", function () {
        $(".section-body").css("font-family", $(this).val());
    });
    $("#section-body-webkit-line-clamp").on("change", function () {
        $(".section-body").css("-webkit-line-clamp", $(this).val());
    });

    // Post interactions section
    $("#section-interactions-color").on("change", function () {
        $(".section-interactions").css("color", $(this).val());
    });
    $("#section-interactions-font-size").on("change", function () {
        $(".section-interactions").css("font-size", $(this).val() + 'px');
    });
    $("#section-interactions-font-family").on("change", function () {
        $(".section-interactions").css("font-family", $(this).val());
    });
    $("#section-interactions-font-weight").on("change", function () {
        $(".section-interactions").css("font-weight", $(this).val());
    });
    $("#section-interactions-line-height").on("change", function () {
        $(".section-interactions").css("line-height", $(this).val() + 'px');
    });
});