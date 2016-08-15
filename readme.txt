=== Hayona Cookie Consent ===
Contributors: lkorteweg@hayona.nl
Donate link: http://www.hayona.com
Tags: eu cookie law, cookie consent, cookie banner, privacy settings, google tag manager
Requires at least: 4.0
Tested up to: 4.6
Stable tag: 1.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A straightforward plugin to comply with the EU cookie law: Inform your visitors about cookies, obtain their consent and give them some control.

== Description ==

This plugin adds a simple cookie banner to your website. It gives your users the option to allow or deny cookies that contain personal identifiable information (PII). They can change their cookie preferences at any time. Place all scripts on your site trough the Google Tag Manager and run them based on these preferences. 

### Features

This simple and lightweight plugin helps you to comply with the EU cookie law:

1. Insert a simple banner on your website to *inform users* about the cookies you are using, to *obtain consent* for the cookies and to offer them the possibility to *change their preferences*. 
2. Block cookies for which permission is required until you obtain their consent.
3. Enable visitors to adjust their *cookie preferences* through a small form on your site.
4. Enable *implied consent* which means that visitors give consent by clicking to the next page. The cookies will be placed and the banner will disappear.
5. Cookies that do not require permission are placed immediately. For instance *Google Analytics* under certain conditions, affiliate cookies and functional cookies.

This plugin offers some unique characteristics:

1. Scripts are placed through the Google Tag Manager
2. Caching has no influence on this plugin or vice-versa
3. PII cookies are blocked until visitors give consent
4. Functional and non-PII cookies are placed immediately
5. Implied consent is optional

### Installation 

Read the [installation page](https://wordpress.org/plugins/hayona-cookies/installation/) for a step by step guide.

### About the cookie law in the EU

**IMPORTANT NOTICE**: Implementing this plugin will NOT automatically make your website in compliance with the EU cookie law. It gives you all the functionality you need, but you will have to use it correctly:

1. Place your cookies in the right category
2. Provide complete and correct information in the banner
3. Provide complete and correct information on the page with your privacy policy
4. Do not place any scripts that place cookies outside the Google Tag manager

Compliance with the EU cookie law comes down to three basic steps:

1. Make sure you know exactly which cookies your site sets, what they are used for and if they are privacy sensitive, which means that they contain personal identifiable information (PII).
2. Inform your visitors precisely how you use cookies.
3. Obtain the visitor’s consent and give them some control over their preferences.

PII stands for Personable Identifiable Information and is a key element of the EU cookie law.

### Detect your cookies

This plugin does not block scripts that you have placed directly in your website. This includes all default youtube videos and most social media buttons. Here are some suggestions on how to work around this issue: 

- Enable privacy mode when embedding a YouTube video. This way the embedded video will not place cookies.
- Do not use social media share buttons. Many well-known solutions place heaps of cookies. Some common examples are the Facebook like button and the AddThis toolbar.

WordPress plugins are powerful tools to build amazing things in relatively short time. The downside of using someone else’s scripts is that you don't always know exactly what is happening when someone visits your website and which cookies are placed. We suggest that you use the [ghostery browser add-on](https://www.ghostery.com/en/our-solutions/ghostery-add-on/) to measure which cookies are placed on your website. 

### Are you sure you want to use cookies?

If you don't comply you risk enforcement action from regulators, including a very high fine. However, non-compliance could also have other consequences. There is evidence that consumers avoid engaging with websites where they believe their privacy is at risk. In general, consumers just don’t like cookies and they don’t like being tracked.

Hayona, the developer of this plugin, therefore believes that the ideal situation is a website that does not need this plugin at all! Most websites do not need PII cookies and without PII cookies the only thing you need is a page describing your privacy policy. No banner or visitor consent is needed.

Be therefore very critical towards your website builder and online marketing people; don’t accept any cookie-placing scripts unless you are absolutely sure it is needed for your business. Implement Google Analytics in non-PII mode by masking ip addresses and not sharing your data with Google or others. Do not use social media buttons with scripts; why would you help the social media to follow and track your website visitors? Use simple hyperlinks instead; it even improves the performance of your site. Think twice about remarketing; consumers will get the feeling they are being tracked. And if you embed YouTube videos, always enable the privacy mode!

### Credits

Thanks to David from [Admin Columns](https://www.admincolumns.com/) for his technical review and advice. 

### Translations

We currently have support for two languages: English and Dutch. Would you like to help translating this plugin? Please contact us trough [this page](http://www.hayona.com).

### Development

Development for this plugin takes place at [GitHub](https://github.com/Hayona/hayona-cookies). Please let us know if you have any feature requests / bugs or if you would like to contribute. 

== Installation ==

### Step 1: Install the plugin 
Follow these steps to install the plugin: 

1. Go to Plugins » Add new
2. Search for 'Hayona Cookie Consent'
3. Click 'Install now'
4. Click 'Activate Plugin'

### Step 2: Make a privacy settings page

1. Add a new page under 'Pages » Add New'
2. Name it 'Privacy statement' and paste in your privacy statement
3. Go to 'Settings » Cookie Consent' 
4. Select your 'Privacy Statement' and hit 'Save changes'

### Step 3: Review banner text

1. Go to 'Settings » Cookie Consent' 
2. Go to the tab 'Banner settings'
3. Review the banner text and adjust if needed

### Step 4: Name all your cookies

1. Go to 'Settings » Cookie Consent' 
2. Go to the tab 'Cookie settings'
3. List all the cookies cookies in the proper category

>Tip: Use the [ghostery browser add-on](https://www.ghostery.com/en/our-solutions/ghostery-add-on/) to find out which cookies are placed on your website. 

### Step 5: Install the Google Tag Manager. 

1. Get a [free account here](https://www.google.com/analytics/tag-manager/) 
2. Place the trackingcode in your site using [DuracellTomi's plugin](https://wordpress.org/plugins/duracelltomi-google-tag-manager/)
2. [Log in](https://tagmanager.google.com/) and select your site.
3. Click on 'Triggers' and click 'New'. 
4. Select 'Custom Event' and give it the event name 'consent'. Save the trigger and click 'Publish'. Name this trigger 'Cookie Consent'

[Watch a video](https://www.youtube.com/watch?v=buEZdno55SU) to see these steps in a little screencast.

### Congratulations!

By now you're ready to add all kinds of tracking scripts to your site. You will need to do this via Google Tag Manager. 

- [Log in](https://tagmanager.google.com/) and select your site.
- Click on 'Tags' and click 'New'. 
- Select the product you would like to add. If you just have a piece of code to add, select 'custom HTML tag' and paste your code in. 

Under 'Fire on' you can configure if the script will be loaded before or after consent has been given by the user. Use the 'All Pages' trigger on tracking scripts that you want to load before consent has been given, Use the 'Cookie Consent' trigger from step 5 on tracking scripts that need consent. 

Save you tag and click 'Publish'.

### Banner text examples

Here are some examples of content you can place in your banner. The parts with **bold emphasis** depend on your settings. Edit them to suit your situation. 

English example:

> We use cookies to ensure that we give you the best experience on our website. The European Law differentiates between functional or non-privacy sensitive cookies and cookies which could be privacy sensitive (PII). **This websites only uses functional or non-privacy sensitive cookies. / This website uses both types of cookies.** By clicking ‘OK, close’ **or by continuing to use our website,** you accept the cookies of our website. Choose ‘change settings’ if you want more information or to change your cookie preferences.

Dutch example: 

> Wij gebruiken cookies om deze website zo gebruiksvriendelijk mogelijk te maken en u te voorzien van de beste informatie. De Europese wetgeving maakt onderscheid tussen cookies die functioneel of niet privacygevoelig zijn en cookies die mogelijk wel privacygevoelig kunnen zijn (PII). **Deze website gebruikt alleen functionele of niet-privacygevoelige cookies.  / Deze website gebruikt beide soorten cookies.** Door hiernaast op ‘OK, sluiten’ te klikken **of door gebruik te blijven maken van deze website,** geeft u toestemming voor het plaatsen van cookies. Klik op 'Instellingen wijzigen' om uw cookievoorkeuren aan te passen en voor meer informatie over het gebruik van cookies op deze website.

### Cookie examples

Examples of functional and non-PII cookies for which you don’t need the visitor’s consent:

- Language choice
- Shopping basket
- Google Analytics – only if implemented correctly in non-PII mode!

Examples of PII cookies – permission required:

- Comscore
- Google Adsense
- Google Adwords remarketing
- Facebook remarketing
- All retargeting and advertising cookies
- Most social media buttons (Add-this toolbar, Facebook like box, etc.)
- Etc.

### Add scripts outside of the Google Tag Manager

Web developers can check for cookie consent through a simple utility function called hasHayonaCookieConsent(). 

	<script>

	$(document).ready( function() {
		if(hasHayonaCookieConsent()) {
			// Place your cookie script here
		}
	} );

	</script>

== Frequently Asked Questions ==

= Why do I need to install Google Tag Manager to use this plugin? =

This plugin does not handle the actual loading of tracking scripts. We use a dedicated tool for that, called [Google Tag Manager](https://www.google.nl/tagmanager/). Here's why:

- **No more conflicts with caching plugins**. Loading tracking scripts based on user consent on the server side (within WordPress) usually does not work together with caching plugins. This is the case with many other cookie banner plugins.  This is a no go for us. Google Tag Manager enables us to do all this on the client side. That means that caching plugins do not get in the way anymore. 
- **To make this plugin as fast as possible**. Small plugins are fast plugins. The less our plugin has to do, the faster it will be. 
- **To fit the workflow of online marketers**. Tracking scripts are usually placed by marketing people. Since many of them are already using Google Tag Manager (like we do) we thought it would make sense to utilize that.

== Screenshots ==

1. Add a customizable cookie banner to the top of your website. 
2. Use a 'cookiewall' if your theme doesn't work well with the banner.
3. Add a privacy settings form to a page of your choice
4. Customize the plugin through the options page under Settings » Cookie Consent

== Changelog ==

= 1.0.1 =
* Added a banner to the plugin page
* Added an icon to the plugin page
* Added different urls to the plugin documentation on the settings page
* Small changes to readme.txt to make it even clearer
* Cleaned up code and comments
* Added a default value for the cookie expiration option field

= 1.0.2 =
* Translation link now to our international site on readme page.
* Moved banner text examples to /installation

= 1.0.3 =
* Minor changes to readme.txt

= 1.0.4 =
* Improved installation instructions

= 1.1 =
* Settings page now with WordPress Settings API
* Added cookiewall option (in case when a theme has conflicting sticky header styles)
* Removed jQuery dependancy
* Added couple of filters (see [github](https://github.com/Hayona/hayona-cookies))
* Use system default font instead of Arial
* Simplified 'cookie settings' page
* Cookie consent is now stored in localStorage.hayonaCookieConsent (instead of cookie)
* Improved installation guide
