var GtmCookieConsentDomEdit = function() {};


GtmCookieConsentDomEdit.prototype.createTag = function( type, className, inner ) {
	var el = document.createElement( type );
	
	if( typeof className !== 'undefined' && className.length > 0 ) {
		el.className = className;
	}

	if( type === 'a' ) {
		el.href = "#";
	}

	if( typeof inner === 'undefined' ) {
		// Skip this
	} else if ( inner instanceof Array ) {

		for(var i=0; i < inner.length; i++) {
			this.appendChildByType( el, inner[i] );
		}
	} else {
		this.appendChildByType( el, inner, true );
	} 

	return el;
};


GtmCookieConsentDomEdit.prototype.appendChildByType = function( el, inner, useInnerHTML ) {
	var useInnerHTML = useInnerHTML || false;

	if( typeof inner === 'string' ) {
		
		if( useInnerHTML ) {
			el.innerHTML = inner;
		} else {
			el.appendChild( document.createTextNode( inner ) );
		}
	} else if( typeof inner === 'object' ) {
		el.appendChild( inner );
	}
};