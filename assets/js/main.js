/** 
 * Initialise the datalayer object if it doesnt' exist. This dataLayer is used 
 * to send data to Google Tag Manager (e.g. the cookie consent)
 */
if(typeof dataLayer === 'undefined'){
	var dataLayer = [];
}


var cookieBanner = function() {

	this.settings = {
		timestamp: 0,
		isSettingsPage: false,
		implicitConsentEnabled: false,
		cookieExpiration: 365
	};

	/**
	 * Debounce
	 * 
	 * @source: http://davidwalsh.name/javascript-debounce-function
	 */
	this.debounce = function(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};
			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	};


	/**
	 * Is refresh
	 *
	 * @description: Is this pageload a refresh
	 */
	this.isRefresh = function() {
		var landingUrl = Cookies.get( 'hc_landing_url' );
		var currentUrl = window.location.href;

		if( landingUrl === currentUrl ) {
			return true;
		} else {
			return false;
		}
	};


	/**
	 * Consent is valid
	 *
	 * @description: Is consent still valid?
	 */
	this.consentIsValid = function() {
		var self = this;
		var cookie = Cookies.getJSON('hc_consent');
		
		if( cookie != null ) {
			var timestamp = cookie.timestamp;

			if( timestamp > self.settings.timestamp ) {
				return true;
			} else {
				return false;
			}
		}
	};


	/**
	 * Has consent
	 *
	 * @description: Checks if consent has been given on earlier visits
	 */
	this.hasConsent = function () {
		var self = this;
		var cookie = Cookies.getJSON('hc_consent');
		
		if( cookie != null ) {
			var consent = cookie.consent;

			if( consent === true &&
					self.consentIsValid() ) {
				return true;
			} else {
				return false;
			}
		}
	};


	/**
	 * Has rejection
	 *
	 * @description: Checks if cookies have been rejected on earlier visits
	 */
	this.hasRejection = function () {
		var self = this;
		var cookie = Cookies.getJSON('hc_consent');
		
		if( cookie != null ) {
			var consent = cookie.consent;

			if( consent === false &&
					self.consentIsValid() ) {
				return true;
			} else {
				return false;
			}
		}
	};


	/**
	 * Has implicit consent
	 *
	 * @description: Checks if implicit consent has been given
	 */
	this.hasImplicitConsent = function () {
		var cookie = Cookies.get('hc_implicit');

		if( cookie != null ) {
			return true;
		} else {
			return false;
		}
	};


	/**
	 * Save consent
	 *
	 * @description: Store consent settings in a cookie
	 */
	this.saveConsent = function(consent) {
		var self = this;

		// Erase old cookies if present
		Cookies.remove( 'hc_consent' );

		var cookie = {
			timestamp: +new Date(),
			consent: consent
		};

		console.log( self.settings.cookieExpiration );

		Cookies.set( 'hc_consent', cookie, { expires: self.settings.cookieExpiration } );
	};


	/**
	 * Save landing url
	 */
	this.saveLandingUrl = function( cookie ) {
		var currentUrl = window.location.href;
		if( cookie == null ) {
			Cookies.set( 'hc_landing_url', currentUrl);
		}
	};
	

	/**
	 * Place the banner at the top of the page
	 * and put the website below the banner.
	 */
	this.resetBodyPadding = function() {

		/* 
		 * We use a faux-padding <div> element to push the webpage down in
		 * order to create space for the cookie-banner. We give the 
		 * faux-padding element exactly the same dimensions as the
		 * cookie-banner. 
		 * 
		 * The cookie-banner is positioned on top of the faux-padding element.
		 * 
		 * We could use a padding-top on the body element to create this space,
		 * but this will almost certainly lead to conflicting styles from other
		 * themes and/or plugins. 
		 */

		// Calculate the height of the banner
		var bannerHeight = jQuery( '.hc-banner' ).outerHeight();

		// Apply this value to the faux-padding element
		jQuery( '.hc-banner__faux-padding' ).css( 'height', bannerHeight );

		// Calculate the offset of the faux-padding element
		var offset = jQuery('.hc-banner__faux-padding').offset();

		// Apply this offset to the cookie-banner
		jQuery( '.hc-banner' ).css( 'top', offset.top );
	};


	// Show the banner
	this.showBanner = function() {
		var self = this;

		// Display banner
		jQuery('.hc-banner').show();

		// Insert faux-padding <div> element just after body
		jQuery('body').prepend( '<div class="hc-banner__faux-padding"></div>' );

		jQuery(window).on( 'load', self.resetBodyPadding );

		var debounceThis = self.debounce(function() {
			self.resetBodyPadding();
		}, 250);

		window.addEventListener('resize', debounceThis );

	};

	// Close the banner
	this.closeBanner = function() {
		jQuery('.hc-banner').hide();
		jQuery('.hc-banner__faux-padding').hide();

		/*
		 * @todo solve bug with eventlistener 
		 * (i.e. add class detection to debounce function)
		 */
		// jQuery('.hc-banner__faux-padding').css( 'height', 0 );			
	};


	/**
	 * Initialise cookie banner
	 * 
	 * @return undefined
	 */
	this.init = function( settings ) {
		var self = this;

		// Store plugin settings
		jQuery.extend(self.settings, self.settings, settings); 


		// Did a user give consent to place cookies?
		if( self.hasConsent() ) {

			// Yes: fire Google Tag Manager 'consent' event
			dataLayer.push({'event': 'consent'});

		} 

		else if( self.hasRejection() ) {

			// No: do nothing

		} 

		else if( self.hasImplicitConsent() && 
				! self.settings.isSettingsPage && 
				! self.isRefresh() &&
				self.settings.implicitConsentEnabled ) {

			// No, but implicit consent is enabled
			// and the user has clicked to the next page

			// Store consent settings
			self.saveConsent( true );

			// Fire Google Tag Manager 'consent' event
			dataLayer.push({'event': 'consent'});

		} 

		else { 

			// Show banner
			self.showBanner();

			// Store landing url in session cookie
			self.saveLandingUrl( Cookies.get('hc_landing_url') );

			if( self.settings.implicitConsentEnabled ) {

				/**
				 * Store implicit consent. If the user does nothing the consent
				 * will be set to 'true' on the next page. Except for the privacy 
				 * statement page and in case of a refresh. In those cases the
				 * consent will remain set to 'implicit'.
				 */
				Cookies.set( 'hc_implicit', true );
			}
		}


		// Is the banner currently on the page?
		if( jQuery('.accept-cookies').length ) {

			// Yes, so if a user clicks on accept, we: 
			// - Close the banner
			// - Store consent settings in a cookie
			// - Fire google tag manager 'consent' event
			jQuery('.accept-cookies').on( 'click', function(e) {
				e.preventDefault();
				self.closeBanner();
				self.saveConsent( true );
				dataLayer.push({'event': 'consent'});
			} );

			// If a user rejects cookies: 
			// - Close the banner
			// - Store consent settings in a cookie
			jQuery('.reject-cookies').on( 'click', function(e) {
				e.preventDefault();
				self.closeBanner();
				self.saveConsent( false );
			} );
		}


		// Are we on the settings page?
		if( self.settings.isSettingsPage ) {

			// Yes: Show the settings form
			// (it is hidden by default for users that
			// don't have javascript enabled)
			jQuery( '.hc-settings' ).show();

			var selectAcceptButton = function() {
				jQuery( '.reject-cookies' ).addClass( 'hc-button--grey' );
				jQuery( '.accept-cookies' ).removeClass( 'hc-button--grey' );
				jQuery( '.reject-cookies span' ).html( '' );
				jQuery( '.accept-cookies span' ).html( '✓ ' );
			};

			var selectRejectButton = function() {
				jQuery( '.reject-cookies' ).removeClass( 'hc-button--grey' );
				jQuery( '.accept-cookies' ).addClass( 'hc-button--grey' );
				jQuery( '.reject-cookies span' ).html( '✓ ' );
				jQuery( '.accept-cookies span' ).html( '' );
			};

			if( self.hasConsent() ) {
				selectAcceptButton();
			} else if( self.hasRejection() ) {
				selectRejectButton();
			}

			jQuery( '.accept-cookies' ).on( 'click', selectAcceptButton );
			jQuery( '.reject-cookies' ).on( 'click', selectRejectButton );

		}
	};
};

var hayonaCookies = new cookieBanner();


/**
 * Small utility functions for users that don't use the Google Tag Mager. 
 */
var hasHayonaCookieConsent = function() {
	if( hayonaCookies.hasConsent() ) {
		return true;
	} else {
		return false;
	}
};
