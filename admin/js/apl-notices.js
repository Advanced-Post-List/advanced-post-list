/**
 * Admin Notices for APL.
 *
 * @summary  Handles the AJAX Actions with APL_Notices
 *
 * @since    0.4.2
 */

(function($) {

    /**
     * @summary Sets up the Delay Button listeners
     *
     * @since 0.4.2
     * @access public
     *
     * @global string $apl_notice_data.notice_nonce
     * @listens apl-notice-delay-{notice_slug}-{delay_index}:click
     *
     * @param string noticeSlug
     * @param string delayIndex
     */
    function apl_notice_delay_ajax_action( noticeSlug, delayIndex ) {
        var noticeNonce = apl_notice_data.notice_nonce;
        var noticeDelayID = "#apl-notice-action-" + noticeSlug + "-" + delayIndex;
        $( noticeDelayID ).on( "click", function( event ) {
            var elem_href = $( this ).attr( "href" );
            if ( "#" === elem_href || "" === elem_href ) {
                // Stops automatic actions.
                event.stopPropagation();
                event.preventDefault();
            }

            var formData = new FormData();
            formData.append( "notice_slug", noticeSlug );
            formData.append( "delay_index", delayIndex );

            formData.append( "action", "apl_notice" );
            formData.append( "_ajax_nonce", noticeNonce );
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: formData,
                cache: false,
                dataType: "json",
                processData: false,
                contentType: false,

                success: function( response, textStatus, jqXHR ){
                    var noticeContainer = ".apl-notice-" + noticeSlug;
                    $( noticeContainer ).remove();
                }
            });
        });
    }

    /**
     * @summary
     *
     * @since 0.4.2
     * @access public
     *
     * @global string $apl_notice_data.notice_nonce
     * @listens apl-notice-delay-{notice_slug}-{delay_index}:click
     *
     * @param string noticeSlug
     */
    function apl_notice_delay_wp_default_dismiss_ajax_action( noticeSlug ) {
        var noticeNonce = apl_notice_data.notice_nonce;
        var noticeContainer = ".apl-notice-" + noticeSlug;
        $( noticeContainer ).on( "click", "button.notice-dismiss ", function( event ) {
            // Prevents any unwanted actions.
            event.stopPropagation();
            event.preventDefault();

            var formData = new FormData();
            formData.append( "notice_slug", noticeSlug );
            formData.append( "delay_index", "default" );

            formData.append( "action", "apl_notice" );
            formData.append( "_ajax_nonce", noticeNonce );
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: formData,
                cache: false,
                dataType: "json",
                processData: false,
                contentType: false
            });
        });
    }

    /**
     * INITIALIZE NOTICE JS
     *
     * Constructs the actions the user may perform.
     */
    var noticeDelays = apl_notice_data.notice_delays;

    $.each( noticeDelays, function ( k1NoticeSlug, v1DelayArr ) {
        $.each( v1DelayArr, function ( k2I, v2DelayIndex ) {
            apl_notice_delay_ajax_action( k1NoticeSlug, v2DelayIndex );
        });

        // Default WP action for Dismiss Button on Upper-Right.
        apl_notice_delay_wp_default_dismiss_ajax_action( k1NoticeSlug );
    });
})(jQuery);
