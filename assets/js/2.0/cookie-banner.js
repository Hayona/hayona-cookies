//@prepros-prepend classes/gtm-cookie-consent-icons.js
//@prepros-prepend classes/gtm-cookie-consent-banner.js
//@prepros-prepend classes/gtm-cookie-consent-form.js
//@prepros-prepend classes/gtm-cookie-consent-dom-edit.js

/**
 * GTM Cookie Consent 
 */

var GtmCookieConsent = function( settings ) {
	
	this.settings = settings;
	this.utils = new GtmCookieConsentDomEdit();
	this.icons = new GtmCookieConsentIcons();

	// Save state
	this.userPreferences;
	this.onLoad = true;

	this.init();
};

GtmCookieConsent.prototype.init = function() {

	// Run a small feature test
	if( ! this.featureTest() )
		return;
	
	// Set datalayer if not yet defined
	window.dataLayer = window.dataLayer || [];

	// Get user preferences
	this.loadUserPreferences();

	// Declare preferences in dataLayer
	this.dataLayerPush( this.userPreferences );
	
	// Init banner
	if( this.userPreferences == null &&
			window.location.href.indexOf(this.settings.privacyStatementUrl) === -1 )
		var banner = new GtmCookieConsentBanner( this );

	// Init form
	if( window.location.href.indexOf(this.settings.privacyStatementUrl) !== -1 ) 
		var form = new GtmCookieConsentForm( this );
};

GtmCookieConsent.prototype.featureTest = function() {
	
	if( 'querySelector' in document
			&& 'localStorage' in window
			&& 'addEventListener' in window ) {
		return true;
	}
};

GtmCookieConsent.prototype.loadUserPreferences = function() {
	var now = +new Date();
	var preferences = JSON.parse( localStorage.getItem( 'hayonaCookieConsent' ) );
	var implicit = sessionStorage.getItem( 'hcImplicitConsent' );

	// Check if preferences exist and if they are not yet expired
	if( preferences != null 
		&& preferences.timestamp > this.settings.resetAllBeforeTimestamp 
		&& now < preferences.expires ) {
		this.userPreferences = preferences;
	}

	// Set preferences on implicit consent
	if( preferences == null
		&& implicit === "true"
		&& window.location.href.indexOf(this.settings.privacyStatementUrl) === -1) {
			this.saveUserPreferences();
		}
};

GtmCookieConsent.prototype.saveUserPreferences = function( consent ) {
	var now = +new Date();
	
	if( typeof consent === 'undefined' )
		consent = true;

	var preferences = {
		timestamp: now,
		consent: consent,
		expires: this.settings.consentExpiration * 24 * 60 * 60 * 1000 + now
	}; 

	localStorage.setItem( 'hayonaCookieConsent', JSON.stringify( preferences ) );
	this.userPreferences = preferences;
};

GtmCookieConsent.prototype.clearUserPreferences = function() {
	localStorage.removeItem( 'hayonaCookieConsent' );
	this.userPreferences = undefined;
};

GtmCookieConsent.prototype.dataLayerPush = function( isOnClick ) {

	if( isOnClick === true )
		var trackEvent = this.settings.gtmTrackEventName;
	else 
		var trackEvent = '';

	if( this.onLoad && typeof this.userPreferences !== 'undefined' && this.userPreferences.consent === true ) {
		dataLayer.push({'event': this.settings.gtmEventName });
		dataLayer.push({'event': trackEvent, 'cookieConsent': 'true' });
		this.onLoad = false;
	} else {
		dataLayer.push({'event': trackEvent, 'cookieConsent': 'false' });
	}
};

var gtmCookieConsent = new GtmCookieConsent( cookieConsentSettings );