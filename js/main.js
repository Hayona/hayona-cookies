

var cookieBanner = function() {

	this.validPermissionToken;

	/*
	 * Thanks to @ppk for his cookie scripts 
	 *
	 * Url: http://www.quirksmode.org/js/cookies.html 
	 */

	this.createCookie = function(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	};
	
	this.readCookie = function(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	};
	
	this.eraseCookie = function(name) {
		this.createCookie(name,"",-1);
	};


	/*
	 * Debounce function from David Walsh
	 * http://davidwalsh.name/javascript-debounce-function
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


	/*
	 * Checks if this pageload is the same as the previous url (refresh)
	 *
	 * @return true | false
	 */

	this.isRefresh = function() {
		var previousUrl = this.readCookie( 'previousUrl' );
		var currentUrl = window.location.href;

		if( previousUrl != null &&
				previousUrl === currentUrl ) {
			this.eraseCookie( 'previousUrl' );
			this.createCookie( 'previousUrl', currentUrl );
			return true;
		} else {
			this.createCookie( 'previousUrl', currentUrl );
			return false;
		}
	}


	/*
	 * Checks if permission is still valid
	 *
	 * @return true | false
	 */

	this.permissionIsValid = function() {
		var self = this;
		var cookiePermissionToken = Number( self.readCookie('cookiePermissionToken') );

		if( cookiePermissionToken != null &&
				cookiePermissionToken === self.validPermissionToken ) {
			return true;
		} else {
			return false;
		}
	}


	/*
	 * Checks if permission has been given on earlier visits
	 *
	 * @return true | false
	 */

	this.hasPermission = function () {
		var self = this;
		var cookiePermission = self.readCookie('cookiePermission');

		if( cookiePermission === "true" &&
				self.permissionIsValid() ) {
			return true;
		} else {
			return false;
		}
	};


	/*
	 * Checks if cookies have been rejected on earlier visits
	 *
	 * @return true | false
	 */

	this.hasRejection = function () {
		var self = this;
		var cookiePermission = self.readCookie('cookiePermission');

		if( cookiePermission === 'false' &&
				self.permissionIsValid() ) {
			return true;
		} else {
			return false;
		}
	};


	/*
	 * Checks if user has given implicit permission
	 *
	 * @return true | false
	 */

	this.hasImplicitPermission = function () {
		var cookiePermission = this.readCookie('cookiePermission');

		if( cookiePermission === 'implicit' ) {
			return true;
		} else {
			return false;
		}
	}


	// Store permission settings in a cookie
	this.storePermissionSettings = function(cookiePermission, permissionToken, isSessionCookie) {

		// Erase old cookies if present
		this.eraseCookie( 'cookiePermission' );
		this.eraseCookie( 'cookiePermissionToken' );

		if( isSessionCookie === true ) {
			// Write permission to new session cookies
			this.createCookie('cookiePermission', cookiePermission );
		} else {
			// Write permission to new cookies (stored for one year)
			this.createCookie('cookiePermission', cookiePermission, 365);
			this.createCookie('cookiePermissionToken', permissionToken, 365);
		}
	};
	

	/*
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


	/*
	 * Initialise cookie banner
	 * 
	 * @param validPermissionToken number
	 * @param isSettingsPage number
	 */
	this.init = function( validPermissionToken, isSettingsPage, implicitConsentEnabled ) {
		var self = this;

		// Check if this pageview is a refresh or not
		var isRefresh = self.isRefresh();

		// Store the current (valid) permission token
		self.validPermissionToken = validPermissionToken;

		// Check permission settings
		if( self.hasPermission() ) {

			// Fire Google Tag Manager 'permission' event
			dataLayer.push({'event': 'permission'});

		} else if( self.hasRejection() ) {

			// User has rejected cookies on earlier visit - do nothing

		} else if( self.hasImplicitPermission() && 
				! isSettingsPage && 
				! isRefresh &&
				implicitConsentEnabled ) {

			// Store permission settings
			self.storePermissionSettings( 'true', validPermissionToken );

			// Fire Google Tag Manager 'permission' event
			dataLayer.push({'event': 'permission'});

		} else { 

			// Show banner
			self.showBanner();

			if( implicitConsentEnabled ) {

				/*
				 * Store implicit permission. If the user does nothing the permission
				 * will be set to 'true' on the next page. Except for the privacy 
				 * statement page and in case of a refresh. In those cases the
				 * permission will remain set to 'implicit'.
				 */
				self.storePermissionSettings( 'implicit', validPermissionToken, true );
			}
		}


		if( jQuery('.accept-cookies').length ) {

			/*
			 * If a user clicks on accept, we: 
			 * - Close the banner
			 * - Store permission settings in a cookie
			 * - Fire google tag manager 'permission' event
			 */
			jQuery('.accept-cookies').on( 'click', function(e) {
				e.preventDefault();
				self.closeBanner();
				self.storePermissionSettings( 'true', validPermissionToken );
				dataLayer.push({'event': 'permission'});
			} );
		}

		if( jQuery('.reject-cookies').length ) {

			/*
			 * If a user rejects cookies: 
			 * - Close the banner
			 * - Store permission settings in a cookie
			 */
			jQuery('.reject-cookies').on( 'click', function(e) {
				e.preventDefault();
				self.closeBanner();
				self.storePermissionSettings( 'false', validPermissionToken );
			} );
		}


		/*
		 * Toggle selected state on the settings page
		 */
		if( isSettingsPage ) {

			jQuery( '.hc-settings' ).show();

			var selectAcceptButton = function() {
				jQuery( '.reject-cookies' ).addClass( 'hc-button--grey' );
				jQuery( '.accept-cookies' ).removeClass( 'hc-button--grey' );
				jQuery( '.reject-cookies span' ).html( '' );
				jQuery( '.accept-cookies span' ).html( '✓ ' );
			}

			var selectRejectButton = function() {
				jQuery( '.reject-cookies' ).removeClass( 'hc-button--grey' );
				jQuery( '.accept-cookies' ).addClass( 'hc-button--grey' );
				jQuery( '.reject-cookies span' ).html( '✓ ' );
				jQuery( '.accept-cookies span' ).html( '' );
			}

			if( self.hasPermission() ) {
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
