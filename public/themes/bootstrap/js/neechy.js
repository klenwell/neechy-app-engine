/*
 * neechy.js
 *
 */
function neechy_document_ready() {
    // Fade out flash alerts in sequence
    // Based on http://stackoverflow.com/a/8141065/1093087
    (function() {
        var wait_for = 5000;
        var interval = 1000;

        $('div#neechy-flash-alerts .alert').each(function() {
            var $alert = $(this);
            window.setTimeout(function() {
                $alert.fadeTo(500, 0).slideUp(500, function(){
                    $alert.remove();
                });
            }, wait_for);
            wait_for += interval;
        });
    })();
}

$(document).ready(neechy_document_ready);
