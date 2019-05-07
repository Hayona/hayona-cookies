var GtmCookieConsentIcons = function() {

    this.icons = {
        'check': '<svg width="15" height="14" viewBox="0 0 15 14" xmlns="http://www.w3.org/2000/svg"><path d="M15 1.935L12.697 0l-7.48 8.797-3.144-2.96L0 8.015l5.46 5.138z" fill-rule="nonzero"/></svg>',
        'close': '<svg width="11" height="11" viewBox="0 0 11 11" xmlns="http://www.w3.org/2000/svg"><path d="M8.683 0L5.5 3.183 2.317 0 0 2.317 3.183 5.5 0 8.683 2.317 11 5.5 7.817 8.683 11 11 8.683 7.817 5.5 11 2.317z" fill-rule="nonzero"/></svg>'
    };
};


GtmCookieConsentIcons.prototype.getIcon = function( iconName ) {

    return this.icons[ iconName ];
}