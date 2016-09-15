/** 
 * Initialise the datalayer object if it doesnt' exist. This dataLayer is used 
 * to send data to Google Tag Manager (e.g. the cookie consent)
 */
if(typeof dataLayer === 'undefined'){
	var dataLayer = [];
}

var CookieBanner = function() { 

	// Elements
	this.banner = document.getElementsByClassName( 'hc-banner' );
	this.buttonAccept = document.getElementsByClassName( 'accept-cookies' );
	this.buttonReject = document.getElementsByClassName( 'reject-cookies' );
	this.buttonClose = document.getElementsByClassName( 'hc-banner__close' );
	this.fauxPadding = document.createElement( 'div' );
	this.settingsForm = document.getElementsByClassName( 'hc-settings' );

	this.settings = {
		timestamp: 0,
		isSettingsPage: false,
		implicitConsentEnabled: false,
		cookieExpiration: 365,
		bannerType: 'default'
	};
};


/**
 * Debounce
 * 
 * @source: http://davidwalsh.name/javascript-debounce-function
 */
CookieBanner.prototype.debounce = function(func, wait, immediate) {
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
CookieBanner.prototype.extend = function(){
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
CookieBanner.prototype.removeClass = function(el, cls) {
	var reg = new RegExp("(\\s|^)" + cls + "(\\s|$)");
	el.className = el.className.replace(reg, " ").replace(/(^\s*)|(\s*$)/g,"");
};


/**
 * Add class
 *
 * @source: http://blog.adtile.me/2014/01/16/a-dive-into-plain-javascript/
 */
CookieBanner.prototype.addClass = function(el, cls) {
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
CookieBanner.prototype.featureTest = function() {
	
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
CookieBanner.prototype.isRefresh = function() {
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
CookieBanner.prototype.consentIsValid = function() {
	var cookie = JSON.parse( localStorage.getItem( 'hayonaCookieConsent' ) );
	var now = +new Date();
	
	if( cookie != null ) {

		if( cookie.timestamp > this.settings.timestamp &&
				now < cookie.expires ) 
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
CookieBanner.prototype.saveConsent = function(consent) {
	var now = +new Date();

	// Erase old cookies if present
	this.reset();

	var cookie = {
		timestamp: now,
		consent: consent,
		expires: this.settings.cookieExpiration * 24 * 60 * 60 * 1000 + now
	}; 

	localStorage.setItem( 'hayonaCookieConsent', JSON.stringify( cookie ) );
};


/**
 * Reset
 *
 * @description: Remove cookie preferences
 */
CookieBanner.prototype.reset = function() {
	localStorage.removeItem( 'hayonaCookieConsent' );
};


/**
 * Has consent
 *
 * @description: Checks if consent has been given on earlier visits
 * @return: true|false|'implicit'|null
 * @todo: extend this function with expiration
 */
CookieBanner.prototype.hasConsent = function () {
	var cookie = JSON.parse( localStorage.getItem( 'hayonaCookieConsent' ) );
	
	if( cookie != null && 
			cookie.consent === true &&
			this.consentIsValid() ) {
		return true;
	} 

	else if( cookie != null &&
			cookie.consent === false &&
			this.consentIsValid() ) {
		return false;
	} 

	else if( sessionStorage.getItem( 'hcImplicitConsent' ) && 
				this.settings.implicitConsentEnabled &&
				! this.settings.isSettingsPage &&
				! this.isRefresh() ) {
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
CookieBanner.prototype.closeBanner = function() {
	this.removeClass( this.banner[0], 'js-visible' );
	this.fauxPadding.style.height = '0px';
};


/**
 * Show banner
 *
 * @description: Display and setup cookie banner
 */
CookieBanner.prototype.showBanner = function() {
	var self = this;

	var setHeightAndOffset = function() {
		var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
		self.fauxPadding.style.height = self.banner[0].clientHeight + 'px';
		self.banner[0].style.top = self.fauxPadding.getBoundingClientRect().top + scrollTop + 'px';
	};

	var resizeDebounce = self.debounce(function() {
		setHeightAndOffset();
	}, 250);

	if( typeof self.banner[0] !== 'undefined' ) {

		// Show banner
		self.addClass( self.banner[0], 'js-visible' );

		// Push page down or add mask
		self.fauxPadding.className = 'hc-banner__faux-padding';
		document.body.insertBefore( self.fauxPadding, document.body.firstChild );

		if( self.settings.bannerType === 'cookiewall' ) {
			self.buttonClose[0].onclick = function() { self.closeBanner(); }
			self.fauxPadding.onclick = function() { self.closeBanner(); }
		}

		else {
			document.addEventListener("DOMContentLoaded", setHeightAndOffset, false);
			window.addEventListener('resize', resizeDebounce);
		}
	}
};


/** 
 * Set selected state
 *
 * @description: Displays selected options on privacy settings form
 */
CookieBanner.prototype.setSelectedState = function() {
	const statusAccept = document.getElementsByClassName( 'hc-status--accept' )[0];
	const statusReject = document.getElementsByClassName( 'hc-status--reject' )[0];

	if( this.settings.isSettingsPage &&
			this.hasConsent() === true ) {
		this.buttonReject[0].style.display = 'block';
		this.buttonAccept[0].style.display = 'none';
		statusAccept.style.display = 'block';
		statusReject.style.display = 'none';
	} 
	
	else if( this.settings.isSettingsPage &&
			this.hasConsent() === false ) {
		this.buttonReject[0].style.display = 'none';
		this.buttonAccept[0].style.display = 'block';
		statusAccept.style.display = 'none';
		statusReject.style.display = 'block';
	}
};


/**
 * Button setup
 *
 * @description: Manage actions on plugin buttons
 */
CookieBanner.prototype.buttonSetup = function() {
	var self = this;

	if( typeof self.buttonAccept[0] !== 'undefined' ) {

		[].forEach.call( self.buttonAccept, function( button ) {

			button.onclick = function(e) {
				e.preventDefault();
				self.closeBanner();
				self.saveConsent( true );
				self.fireCookieScripts();
				self.setSelectedState();
			};
		} );
	}

	if( typeof self.buttonReject[0] !== 'undefined' ) {
		
		[].forEach.call( self.buttonReject, function( button ) {

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
CookieBanner.prototype.fireCookieScripts = function() {
	dataLayer.push({'event': 'consent'});
};


/**
 * Initialise cookie banner
 * 
 * @return undefined
 */
CookieBanner.prototype.init = function( settings ) {

	if( ! this.featureTest() )
		return;

	this.extend( this.settings, this.settings, settings);
	this.buttonSetup();

	switch( this.hasConsent() ) {
		case true :
			this.fireCookieScripts();
			break;
		case false :
			// Do nothing
			break;
		case 'implicit' :
			this.saveConsent( true );
			this.fireCookieScripts();
			break;
		case null :
			if( ! this.settings.isSettingsPage )
				this.showBanner();
			sessionStorage.setItem( 'hcImplicitConsent', true );
			break;
	}

	if( this.settings.isSettingsPage && typeof this.settingsForm[0] !== 'undefined' ) {
		this.settingsForm[0].style.display = 'block';
		this.setSelectedState();
	}

	if( this.settings.bannerType === 'cookiewall' ) {
		this.addClass( document.body, 'hc-cookiewall' );
	}

	sessionStorage.setItem( 'hcLandingUrl', window.location.href );
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