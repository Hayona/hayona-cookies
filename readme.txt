=== Hayona Cookie Consent ===
Contributors: lkorteweg@hayona.nl
Donate link: http://www.hayona.com
Tags: eu cookie law, cookie consent, cookie banner, privacy settings, google tag manager
Requires at least: 4.0
Tested up to: 4.6
Stable tag: 1.1.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A straightforward plugin to comply with the EU cookie law, including implied consent.

== Description ==

### Add a cookie banner

Insert a simple banner on your website to inform users about the cookies you are using on your site. Users can give consent, or they can choose to block all or specific cookies. 

### Place sensitive cookies after consent has been given

Cookies that require consent will not be placed before consent has been given. This feature is essential to comply with the EU cookie law. Sadly, this is the part where many other cookie banner plugins fall short. 

Of course, cookies which do not require consent are placed immediately.

### Privacy settings

Users can adjust their privacy settings at any time through a small form on your site. Even after they have given consent.

### Built for speed

Caching plugins will not interfere with the Hayona Cookie Consent plugin. 

### Implied consent (optional)

When you enable implied consent, clicking to the next page will count as consent; cookies are placed and the banner disappears.

### Google Tag Manager

This plugin does not handle the actual loading of tracking scripts but uses [Google Tag Manager](https://www.google.com/analytics/tag-manager/). Read more about this in the [FAQ](https://wordpress.org/plugins/hayona-cookies/faq/).

### Installation

Read the [installation page](https://wordpress.org/plugins/hayona-cookies/installation/) for a step by step guide.

### About the cookie law in the EU

Compliance with the EU cookie law comes down to three basic steps:

1.  Make sure you know exactly which cookies your site sets, what they are used for and if they are privacy sensitive, which means that they contain personal identifiable information (PII).
2.  Inform your visitors precisely how you use cookies.
3.  Obtain the visitor’s consent and give them some control over their preferences.

Read the [installation page](https://wordpress.org/plugins/hayona-cookies/installation/) for more information.

### Detect your cookies

This plugin uses Google Tag Manager to place tracking scripts / cookies. Therefore the plugin itself does not block scripts that you have placed directly in your website. This includes all default youtube videos and many social media buttons. Here are some suggestions on how to work around this issue:

- Enable privacy mode when embedding a YouTube video. This way the embedded video will not place cookies.
- Do not use social media share buttons that place cookies. This includes many well-known solutions like the Facebook like button and the AddThis toolbar.
- We suggest that you use the [ghostery browser add-on](https://www.ghostery.com/en/our-solutions/ghostery-add-on/) to measure which other cookies are placed on your website.

### Disclaimer

IMPORTANT NOTICE: Implementing this plugin will NOT automatically make your website in compliance with the EU cookie law. It gives you all the functionality you need, but you will have to use it correctly.

### Credits

Thanks to David from [Admin Columns](https://www.admincolumns.com/) for his technical review and advice.

### Translation

We currently have support for two languages: English and Dutch. Would you like to help translating this plugin? Please contact us trough [this page](http://www.hayona.com/).

### Developers

Development for this plugin takes place at [GitHub](https://github.com/Hayona/hayona-cookies). Please let us know if you have any feature requests / bugs or if you would like to contribute.

== Installation ==

### Step 1: Install the plugin 
Follow these steps to install the plugin: 

1. Go to 'Plugins » Add new'.
2. Search for 'Hayona Cookie Consent'.
3. Click 'Install now'.
4. Click 'Activate Plugin'.

### Step 2: Make a privacy settings page

1. Add a new page under 'Pages » Add New'.
2. Name it 'Privacy statement' and paste in your privacy statement. In this statement you explain your users in detail which cookies you use and what you are using them for.
3. Go to 'Settings » Cookie Consent'. 
4. Select your 'Privacy Statement' and hit 'Save changes'. 

### Step 3: Review banner text

1. Go to 'Settings » Cookie Consent'.
2. Go to the tab 'Banner settings'.
3. Review the banner text and adjust if needed. See some examples further down this page under 'Banner text examples'.

### Step 4: Name all your cookies

1. Go to 'Settings » Cookie Consent'.
2. Go to the tab 'Cookie settings'.
3. List all the cookies cookies in the proper category. See some examples further down this page under 'Cookie examples'.

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

Here are some examples of content you can place in your banner. 

English example:

> This site uses cookies. By continuing to browse the site or clicking OK, you are agreeing to our use of cookies. Select ‘Change settings’ for more information.

Dutch example: 

> Deze website gebruikt cookies. Door OK te kiezen of gebruik te maken van deze website geeft u hiervoor toestemming. Kies 'Instellingen wijzigen' voor meer informatie.

### Cookie examples

Below are some examples of functional and cookies that do **not** store personal identifiable information (non-PII cookies). You don't need the visitor’s consent for these cookies:

- Language choice
- Shopping basket
- Google Analytics – only if implemented correctly in non-PII mode!
- Affiliate cookies

Here are some examples of cookies that do store personal identifiable information (PII cookies). You need the visitor's consent to place these cookies:

- Comscore
- Google Adsense
- Google Adwords remarketing
- Facebook remarketing
- All retargeting and advertising cookies
- Many social media buttons (Add-this toolbar, Facebook like box, etc.)

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

= 1.1.1 =
* Fix error in cookie settings admin

= 1.1.2 =
* Add compatibility for PHP 5.3

= 1.1.3 =
* Added 100% width to privacy settings table
* Banner is now smaller
* Edit banner examples on readme.txt

= 1.1.4 =
* Remove admin css conflict
