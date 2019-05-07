var cookieConsentSettings = {

	/* Edit general settings */
	privacyStatementUrl: 'http://localhost/gtm-cookie-consent/demo/privacy-en-cookies.html',
	cookies: [
		['Cookie instellingen', 'Google Analytics (anoniem)'],
		['Facebook remarketing', 'Google AdWords Remarketing', 'Bing Remarketing']
	],
	resetAllBeforeTimestamp: 1530021013000,
	consentExpiration: 365,
	gtmEventName: "consent",
	gtmTrackEventName: "track_consent",
	formPlacementBefore: "h1",
	useBodyOffset: true,
	offsetHeaderSelector: undefined,
	implicitConsentEnabled: true,

	/* Edit banner text */
	explanation: "Deze website gebruikt advertentiecookies van Google, Facebook, LinkedIn en Bing. Daarmee kunnen wij u gerichte advertenties tonen. Lees meer over cookies in ons <a href='http://localhost/gtm-cookie-consent/demo/privacy-en-cookies.html'>privacybeleid</a>.",
	buttonYes: "Oké, akkoord",
	buttonNo: "Nee, bedankt",

	/* Edit form text */
	formHead: 'Cookie voorkeuren',
	formSubtitle: 'Selecteer welke cookies u wilt accepteren van deze website.',
	optionOneButton: 'Alle cookies accepteren',
	optionTwoButton: 'Sommige cookies accepteren',
	allowed:  'Ja',
	disallowed: 'Nee',
};