/** 
 * GTM Cookie Consent Banner
 */

var GtmCookieConsentBanner = function( app ) {
	this.app = app;
	this.settings = app.settings;
	this.utils = app.utils;

	// Elements
	this.banner;
	this.bannerOffset;
	this.buttonAccept;
	this.buttonReject;

	// Save state
	this.bannerHeight;

	this.init();
};


GtmCookieConsentBanner.prototype.init = function() {

	// Put cookie consent banner on top of the page
	document.body.insertBefore( this.getBanner(), document.body.firstChild );

	// Initialise body offset
	if( this.settings.useBodyOffset ) 
		this.setBodyOffset();

	if( this.settings.implicitConsentEnabled &&
		window.location.href.indexOf(this.settings.privacyStatementUrl) === -1) {
		//window.location.href.indexOf("http://localhost/gtm-cookie-banner/demo/privacy-en-cookies.html") === -1) {
		sessionStorage.setItem( 'hcImplicitConsent', 'true');
	}
};


GtmCookieConsentBanner.prototype.getBanner = function() {
	this.banner = this.utils.createTag( 'div', 'gtmcc-banner' );

	// Add explanation
	var explanation = this.utils.createTag( 'div', 'gtmcc-banner__info', this.settings.explanation );
	this.banner.appendChild( explanation );

	// Add toolbar with two buttons
	var toolbar = this.utils.createTag( 'ul', 'gtmcc-banner__toolbar' );
	this.buttonAccept = this.utils.createTag( 
		'li', '', 
		this.utils.createTag( 
			'a', 'gtmcc-button gtmcc-button--has-icon gtmcc-button--accept gtmcc-js-accept', 
			[
				this.utils.createTag( 'span', 'gtmcc-icon gtmcc-icon--check', this.app.icons.getIcon( 'check' ) ),
				this.utils.createTag( 'span', 'gtmcc-button__label', this.settings.buttonYes )
			]	 
		) 
	);
	this.buttonDecline = this.utils.createTag( 
		'li', '', 
		this.utils.createTag( 
			'a', 'gtmcc-button gtmcc-button--has-icon gtmcc-button--decline gtmcc-js-decline', 
			[
				this.utils.createTag( 'span', 'gtmcc-icon gtmcc-icon--close', this.app.icons.getIcon( 'close' ) ),
				this.utils.createTag( 'span', 'gtmcc-button__label', this.settings.buttonNo )
			]
		) 
	);
	toolbar.appendChild( this.buttonAccept );
	toolbar.appendChild( this.buttonDecline );
	this.banner.appendChild( toolbar );

	// Add button events
	this.buttonAccept.addEventListener( 'click', this.accept.bind(this) );
	this.buttonDecline.addEventListener( 'click', this.decline.bind(this) );

	return this.banner;
};


GtmCookieConsentBanner.prototype.accept = function(e) {
	e.preventDefault();
	this.closeBanner();
	this.app.saveUserPreferences();
	this.app.dataLayerPush( true );
};


GtmCookieConsentBanner.prototype.decline = function(e) {
	e.preventDefault();
	this.closeBanner();
	this.app.saveUserPreferences( false );
	this.app.dataLayerPush( true );
};


GtmCookieConsentBanner.prototype.closeBanner = function() {

	if( typeof this.banner === 'undefined' ) 
		return;

	this.banner.style.display = 'none';

	if( typeof this.bannerOffset !== 'undefined' ) 
		this.bannerOffset.style.display = 'none';

	if( typeof this.settings.offsetHeaderSelector !== 'undefined' )
		document.querySelector( this.settings.offsetHeaderSelector ).style.top = 0 + 'px';
};


GtmCookieConsentBanner.prototype.setBodyOffset = function() {
	var self = this;
	self.bannerOffset = self.utils.createTag( 'div', 'gtmcc-banner-offset' );
	document.body.insertBefore( self.bannerOffset, document.body.firstChild );

	// Recalculate on page load
	self.calculateOffset();

	// Recalculate until page complete
	var interval = setInterval(function() {
		self.calculateOffset();
		if(document.readyState === 'complete') {
			clearInterval(interval);
		}    
	}, 100);
	
	// Recalculate on page resize
	window.addEventListener( 
		'resize', 
		self.debounce(function(){
			self.calculateOffset();
		}, 50) 
	);
};


GtmCookieConsentBanner.prototype.calculateOffset = function() {
	this.bannerHeight = this.banner.clientHeight;
	this.bannerOffset.style.height = this.bannerHeight + 'px';

	if( typeof this.settings.offsetHeaderSelector !== 'undefined' ) {
		document.querySelector( this.settings.offsetHeaderSelector ).style.top = this.bannerHeight + 'px';
	}
};


GtmCookieConsentBanner.prototype.debounce = function(func, wait, immediate) {
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