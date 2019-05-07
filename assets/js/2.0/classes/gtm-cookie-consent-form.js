/** 
 * GTM Cookie Consent Form
 */

var GtmCookieConsentForm = function( app ) {
	this.app = app;
	this.settings = app.settings;
	this.utils = app.utils;

	// Elements
	this.buttonAccept;
	this.buttonReject;

	this.init();
};


GtmCookieConsentForm.prototype.init = function() {
	var self = this;

	// Wait till page is ready
	var interval = setInterval(function() {
		if(document.readyState === 'complete') {
			clearInterval(interval);

			// Insert form
			var el = document.querySelector( self.settings.formPlacementBefore );
			el.parentNode.insertBefore( self.getForm(), el );

			// Mark selected option
			self.markSelected();
		}    
	}, 100);
};


GtmCookieConsentForm.prototype.getForm = function() {
	var self = this;

	// Create wrapper
	var form = this.utils.createTag( 'div', 'gtmcc-form' );

	// Add header
	var header = this.utils.createTag( 'div', 'gtmcc-form__header' );
	header.appendChild( this.utils.createTag( 'div', 'gtmcc-title', self.settings.formHead ) );
	header.appendChild( this.utils.createTag( 'div', 'gtmcc-subtitle', self.settings.formSubtitle ) );
	form.appendChild( header );

	// Add two options
	var ul = this.utils.createTag( 'ul', 'gtmcc-form__options' );
	self.buttonReject = this.getOption( this.settings.optionTwoButton, self.getTable( false, self.settings.cookies ) );
	self.buttonAccept = this.getOption( this.settings.optionOneButton, self.getTable( true, self.settings.cookies ) );
	ul.appendChild( this.utils.createTag( 'li', '',  self.buttonReject) );
	ul.appendChild( this.utils.createTag( 'li', '',  self.buttonAccept) );
	form.appendChild( ul );	

	// Make buttons clickable 
	self.buttonReject.addEventListener( 'click', self.decline.bind(this) );
	self.buttonAccept.addEventListener( 'click', self.accept.bind(this) );

	return form;
};


GtmCookieConsentForm.prototype.accept = function(e) {
	e.preventDefault();
	this.app.saveUserPreferences();
	this.app.dataLayerPush( true );
	this.markSelected();
};


GtmCookieConsentForm.prototype.decline = function(e) {
	e.preventDefault();
	this.app.saveUserPreferences(false);
	this.app.dataLayerPush( true );
	this.markSelected();
};


GtmCookieConsentForm.prototype.markSelected = function() {

	if( typeof this.app.userPreferences !== 'undefined' && this.app.userPreferences.consent ) {
		this.buttonAccept.classList.add( 'gtmcc-selected' );
		this.buttonReject.classList.remove( 'gtmcc-selected' );
	} else {
		this.buttonAccept.classList.remove( 'gtmcc-selected' );
		this.buttonReject.classList.add( 'gtmcc-selected' );
	}
};


GtmCookieConsentForm.prototype.getOption = function( header, table ) {
	var self = this;
	var a = this.utils.createTag( 'a', 'gtmcc-form__option' );
	a.appendChild( this.utils.createTag( 
		'div', 'gtmcc-form__option__header',  
		[
			this.utils.createTag( 'span', 'gtmcc-form__option__header__checkbox', 
				this.utils.createTag( 'span', 'gtmcc-icon gtmcc-icon--check', this.app.icons.getIcon( 'check' ) )
			),
			this.utils.createTag( 'span', 'gtmcc-form__option__header__title', header )
		]
	) );
	a.appendChild( table );
	return a;
};


GtmCookieConsentForm.prototype.getTable = function( piiCookiesAlowed ) {
	var self = this;
	var table = self.utils.createTag( 'table', 'gtmcc-table' );

	// Parse non pii cookies
	for(var i = 0; i < self.settings.cookies[0].length; i++) {
		table.appendChild( self.getRow( self.settings.cookies[0][i], true ) );
	}

	// Parse pii cookies
	for(var i = 0; i < self.settings.cookies[1].length; i++) {

		if( piiCookiesAlowed ) {
			table.appendChild( self.getRow( self.settings.cookies[1][i], true ) );
		}
		else {
			table.appendChild( self.getRow( self.settings.cookies[1][i], false ) );
		}
	}

	return table;
};

GtmCookieConsentForm.prototype.getRow = function( cookieName, isAllowed ) {
	var row = document.createElement( 'tr' );
	if( isAllowed ) {
		row.appendChild( this.utils.createTag( 'td', 'gtmcc-table__th', cookieName ) );
		row.appendChild( this.utils.createTag( 
			'td', 'gtmcc-table__td', 
			[
				this.utils.createTag( 'span', 'gtmcc-icon gtmcc-icon--check' ),
				this.settings.allowed 
			]
		) );
	} else {
		row.appendChild( this.utils.createTag( 'td', 'gtmcc-table__th gtmcc-table__th--disallowed', cookieName ) );
		row.appendChild( this.utils.createTag( 
			'td', 'gtmcc-table__td', 
			[
				this.utils.createTag( 'span', 'gtmcc-icon gtmcc-icon--close' ),
				this.settings.disallowed 
			]
		) );
	}
	return row;
};