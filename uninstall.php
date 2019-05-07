<?php
    if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
        exit;
    }

    delete_option( 'hc_implied_consent_enabled' );
    delete_option( 'hc_banner_text' );
    delete_option( 'hc_banner_color_scheme' );
    delete_option( 'hc_banner_type' );
    delete_option( 'hc_cookielist_consent_required' );
    delete_option( 'hc_cookielist_consent_not_required' );
    delete_option( 'hc_cookie_expiration' );
    delete_option( 'hc_consent_timestamp' );
    delete_option( 'hc_reset_consent_timestamp' );
    delete_option( 'hc_privacy_statement_url' );
    delete_option( 'hc_is_enabled' );
    // new banner
    delete_option( 'hc_banner_color' );
    delete_option( 'hc_banner_text_button_yes' );
    delete_option( 'hc_banner_text_button_no' );
    delete_option( 'hc_banner_use_body_offset' );
    delete_option( 'hc_banner_offset_header_selector' );
    // form
    delete_option( 'hc_form_placement_before' );
    delete_option( 'hc_form_head_text' );
    delete_option( 'hc_form_subtitle_text' );
    delete_option( 'hc_form_option_one_button_text' );
    delete_option( 'hc_form_option_two_button_text' );
    delete_option( 'hc_form_allowed_text' );
    delete_option( 'hc_form_disallowed_text' );