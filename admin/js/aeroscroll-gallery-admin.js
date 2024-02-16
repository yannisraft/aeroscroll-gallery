(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */
    $(function() {
        console.log("RUN");

        /* $.ajax({
            method: 'GET',
            url: APEX.getgridlist.url,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', APEX.getgridlist.nonce);
            }
        }).then(function (r) {
            console.log("RECEIVED: ", r);
            if (r.hasOwnProperty('orientation')) {
                $("#gridorientation").val(r.orientation);
            }
        }); */
    });
    


    /* $('#apex-form').on('submit', function (e) {
        e.preventDefault();
        var data = {
            amount: $('#amount').val(),
            industry: $('#industry').val()
        };

        $.ajax({
            method: 'POST',
            url: APEX.api.url,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', APEX.api.nonce);
            },
            data: data
        }).then(function (r) {
            $('#feedback').html('<p>' + APEX.strings.saved + '</p>');
        }).error(function (r) {
            var message = APEX.strings.error;
            if (r.hasOwnProperty('message')) {
                message = r.message;
            }
            $('#feedback').html('<p>' + message + '</p>');

        })
    }); */

})(jQuery);
