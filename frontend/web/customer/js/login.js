jQuery(document).ready(function ($) {
    //load form login
    Login.loadModel();
    Login.closePopup();

    jQuery('body').on("click", ".refresh-captcha", function (e) {
        e.preventDefault();
        jQuery("img[class$='my-captcha']").trigger('click');
    });
    $('#frm-ajax-login').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    $('#signupform-agree').click(function() {
        if ($(this).is(':checked')) {

            $(':input[type="submit"]').removeAttr('disabled');
        } else {
            $(':input[type="submit"]').attr('disabled', 'disabled');
        }
    });

    if($('#signupform-agree').is(':checked')){
        $(':input[type="submit"]').removeAttr('disabled');
    }

});

var Login = {
    loadModel: function () {
        $("#myModal").modal({backdrop: 'static', keyboard: false});
    },
    closePopup: function () {
        $('.login-pop-dialog .close').on('click', function () {
            $('.login-pop-dialog').hide();
            window.location = '/';
        });
    }
};

(function ($) {
    "use strict";
    function centerModal() {
        $(this).css('display', 'block');
        var $dialog  = $(this).find(".modal-dialog"),
            offset       = ($(window).height() - $dialog.height()) / 2,
            bottomMargin = parseInt($dialog.css('marginBottom'), 10);

        // Make sure you don't hide the top part of the modal w/ a negative margin if it's longer than the screen height, and keep the margin equal to the bottom margin of the modal
        if(offset < bottomMargin) offset = bottomMargin;
        $dialog.css("margin-top", offset);
    }

    $(document).on('show.bs.modal', '.modal', centerModal);
    $(window).on("resize", function () {
        $('.modal:visible').each(centerModal);
    });
}(jQuery));