/** 
 * Initialise the datalayer object if it doesnt' exist. This dataLayer is used 
 * to send data to Google Tag Manager (e.g. the cookie consent)
 */
if(typeof dataLayer === 'undefined'){
	var dataLayer = [];
}

var CookieBanner = function() { 

	var self = this;

	// Elements
	var banner = document.getElementsByClassName( 'hc-banner' );
	var buttonAccept = document.getElementsByClassName( 'accept-cookies' );
	var buttonReject = document.getElementsByClassName( 'reject-cookies' );
	var buttonClose = document.getElementsByClassName( 'hc-banner__close' );
	var fauxPadding = document.createElement( 'div' );
	var settingsForm = document.getElementsByClassName( 'hc-settings' );

	this.settings = {
		timestamp: 0,
		isSettingsPage: false,
		implicitConsentEnabled: false,
		cookieExpiration: 365,
		bannerType: 'default',
		callback: function() {}
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
	 * Extend
	 *
	 * @description: Extend an object
	 * @source: http://stackoverflow.com/questions/11197247/javascript-equivalent-of-jquerys-extend-method
	 */
	this.extend = function(){
	    for(var i=1; i<arguments.length; i++)
	        for(var key in arguments[i])
	            if(arguments[i].hasOwnProperty(key))
	                arguments[0][key] = arguments[i][key];
	    return arguments[0];
	};


	/**
	 * Remove class
	 *
	 * @source: http://blog.adtile.me/2014/01/16/a-dive-into-plain-javascript/
	 */
	this.removeClass = function(el, cls) {
		var reg = new RegExp("(\\s|^)" + cls + "(\\s|$)");
		el.className = el.className.replace(reg, " ").replace(/(^\s*)|(\s*$)/g,"");
	};


	/**
	 * Add class
	 *
	 * @source: http://blog.adtile.me/2014/01/16/a-dive-into-plain-javascript/
	 */
	this.addClass = function(el, cls) {
		var hasClass = el.className && new RegExp("(\\s|^)" + cls + "(\\s|$)").test(el.className);
		if( ! hasClass ) 
			el.className += ' ' + cls;
	};


	/**
	 * Feature test
	 *
	 * @description: Return true if browser can handle the awsomeness
	 * @source: Idea taken from http://responsivenews.co.uk/post/18948466399/cutting-the-mustard
	 */
	this.featureTest = function() {
		
		if( 'querySelector' in document
				&& 'localStorage' in window
				&& 'addEventListener' in window ) {
			return true;
		}
	};


	/**
	 * Is refresh
	 *
	 * @description: Is this pageload a refresh
	 */
	this.isRefresh = function() {
		if( sessionStorage.getItem( 'hcLandingUrl' ) === window.location.href ) 
			return true;
		else 
			return false;
	};


	/**
	 * Consent is valid
	 *
	 * @description: Is consent still valid?
	 */
	this.consentIsValid = function() {
		var cookie = Cookies.getJSON('hc_consent');
		
		if( cookie != null ) {

			if( cookie.timestamp > self.settings.timestamp ) 
				return true;
			else 
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

		Cookies.set( 'hc_consent', cookie, { expires: self.settings.cookieExpiration } );
	};


	/**
	 * Has consent
	 *
	 * @description: Checks if consent has been given on earlier visits
	 * @return: true|false|'implicit'|null
	 */
	this.hasConsent = function () {
		var cookie = Cookies.getJSON( 'hc_consent' );
		
		if( cookie != null && 
				cookie.consent === true &&
				self.consentIsValid() ) {
			return true;
		} 

		else if( cookie != null &&
				cookie.consent === false &&
				self.consentIsValid() ) {
			return false;
		} 

		else if( sessionStorage.getItem( 'hcImplicitConsent' ) && 
					self.settings.implicitConsentEnabled &&
					! self.settings.isSettingsPage &&
					! self.isRefresh() ) {
			return 'implicit';
		} 

		else {
			return null;
		}
	};


	/** 
	 * Close banner
	 *
	 * @description: Hide the banner
	 */
	this.closeBanner = function() {
		banner[0].style.display = 'none';
		fauxPadding.style.height = '0px';
	};


	/**
	 * Show banner
	 *
	 * @description: Display and setup cookie banner
	 */
	this.showBanner = function() {

		var setHeightAndOffset = function() {
			var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
			fauxPadding.style.height = banner[0].clientHeight + 'px';
			banner[0].style.top = fauxPadding.getBoundingClientRect().top + scrollTop + 'px';
		};

		var resizeDebounce = self.debounce(function() {
			setHeightAndOffset();
		}, 250);

		if( banner[0] !== undefined ) {

			// Show banner
			banner[0].style.display = 'block';

			// Push page down or add mask
			fauxPadding.className = 'hc-banner__faux-padding';
			document.body.insertBefore( fauxPadding, document.body.firstChild );
			
			if( self.settings.bannerType === 'default' ) {
				document.addEventListener("DOMContentLoaded", setHeightAndOffset, false);
				window.addEventListener('resize', resizeDebounce);
			}

			else if( self.settings.bannerType === 'cookiewall' ) {
				buttonClose[0].onclick = function() { self.closeBanner(); }
				fauxPadding.onclick = function() { self.closeBanner(); }
			}
		}
	};


	/** 
	 * Set selected state
	 *
	 * @description: Displays selected options on privacy settings form
	 */
	this.setSelectedState = function() {

		if( self.settings.isSettingsPage &&
				self.hasConsent() === true ) {
			self.addClass( buttonReject[0], 'hc-button--grey' );
			self.removeClass( buttonAccept[0], 'hc-button--grey' );
			buttonReject[0].children[0].innerHTML = '';
			buttonAccept[0].children[0].innerHTML = '✓ ';
		} 
		
		else if( self.settings.isSettingsPage &&
				self.hasConsent() === false ) {
			self.removeClass( buttonReject[0], 'hc-button--grey' );
			self.addClass( buttonAccept[0], 'hc-button--grey' );
			buttonReject[0].children[0].innerHTML = '✓ ';
			buttonAccept[0].children[0].innerHTML = '';
		}
	};


	/**
	 * Button setup
	 *
	 * @description: Manage actions on plugin buttons
	 */
	this.buttonSetup = function() {

		if( buttonAccept[0] !== undefined ) {

			[].forEach.call( buttonAccept, function( button ) {

				button.onclick = function(e) {
					e.preventDefault();
					self.closeBanner();
					self.saveConsent( true );
					self.fireCookieScripts();
					self.setSelectedState();
				};
			} );
		}

		if( buttonReject[0] !== undefined ) {
			
			[].forEach.call( buttonReject, function( button ) {

				button.onclick = function(e) {
					e.preventDefault();
					self.closeBanner();
					self.saveConsent( false );
					self.setSelectedState();
				};
			} );
		}
	};


	/**
	 * Fire cookie scripts
	 *
	 * @description: This method is fired when consent is available or when it is given.
	 */
	this.fireCookieScripts = function() {
		dataLayer.push({'event': 'consent'});
		self.settings.callback();
	};


	/**
	 * Initialise cookie banner
	 * 
	 * @return undefined
	 */
	this.init = function( settings ) {

		if( ! self.featureTest() )
			return;

		self.extend( self.settings, self.settings, settings);
		self.buttonSetup();

		switch( self.hasConsent() ) {
			case true :
				self.fireCookieScripts();
				break;
			case false :
				// Do nothing
				break;
			case 'implicit' :
				self.saveConsent( true );
				self.fireCookieScripts();
				break;
			case null :
				self.showBanner();
				sessionStorage.setItem( 'hcImplicitConsent', true );
				break;
		}

		if( self.settings.isSettingsPage ) {
			settingsForm[0].style.display = 'block';
			self.setSelectedState();
		}

		if( self.settings.bannerType === 'cookiewall' ) {
			self.addClass( document.body, 'hc-cookiewall' );
		}

		sessionStorage.setItem( 'hcLandingUrl', window.location.href );
	};
};

var hayonaCookies = new CookieBanner();


/**
 * Has Hayona Cookie Consent
 *
 * @description: Small utility functions for users that don't use the Google Tag Mager.
 * @return: true|false 
 */
var hasHayonaCookieConsent = function() {
	if( hayonaCookies.hasConsent() ) 
		return true;
	else 
		return false;
};
