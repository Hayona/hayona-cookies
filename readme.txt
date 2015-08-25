=== Plugin Name ===
Contributors: lkorteweg@hayona.nl
Donate link: http://www.hayona.nl
Tags: eu cookie law, cookie consent, cookie banner, privacy settings, google tag manager
Requires at least: 
Tested up to: 
Stable tag: 
License: GPLv2

Comply with EU cookie law: tell your visitors how you use cookies, obtain their consent and give them some control.

== Description ==

This simple and lightweight plugin helps you to comply with the EU cookie law:

1. Insert a simple banner on your website to *inform users* about the cookies you are using, to *obtain consent* for the cookies and to offer them the possibility to *change their preferences*. 
2. Block cookies for which permission is required until you obtain their consent.
3. Enable visitors to adjust their *cookie preferences* through a small form on your site.
4. Enable *implied consent* which means that visitors give consent by clicking to the next page. The cookies will be placed and the banner will disappear.
5. Cookies that do not require permission are placed immediately. For instance *Google Analytics* under [certain conditions](http://www.interpedia.nl), affiliate cookies and functional cookies.

This plugin offers some unique characteristics:

1. Scripts are placed through the Google Tag Manager
2. Caching has no influence on this plugin or vice-versa
3. PII cookies are blocked until visitors give consent
4. Functional and non-PII cookies are placed immediately
5. Implied consent is optional

Using Google Tag Manager to place scripts on your website is a very simple and straightforward process. It only takes five minutes. Watch this video to see how it works:

-- video --

# About the cookie law in the EU

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
- Social media buttons
- Etc.

If you don't comply you risk enforcement action from regulators, including a very high fine. However, non-compliance could also have other consequences. There is evidence that consumers avoid engaging with websites where they believe their privacy is at risk. In general, consumers just don’t like cookies and they don’t like being tracked.

Hayona, the developer of this plugin, therefore believes that the ideal situation is a website that does not need this plugin at all! Most websites do not need PII cookies and without PII cookies the only thing you need is a page describing your privacy policy. No banner or visitor consent is needed.

Be therefore very critical towards your website builder and online marketing people; don’t accept any cookie-placing scripts unless you are absolutely sure it is needed for your business. Implement Google Analytics in non-PII mode by masking ip addresses and not sharing your data with Google or others. Do not use social media buttons with scripts; why would you help the social media to follow and track your website visitors? Use simple hyperlinks instead; it even improves the performance of your site. Think twice about remarketing; consumers will get the feeling they are being tracked. And if you embed YouTube videos, always enable the privacy mode!

# Detect your cookies

WordPress plugins are powerful tools to build amazing things in relatively short time. The downside of using someone else’s scripts is that you don't always know exactly what is happening when someone visits your website and which cookies are placed. We suggest that you use the [ghostery browser add-on](https://www.ghostery.com/en/our-solutions/ghostery-add-on/) to measure which cookies are placed on your website. 

This plugin does not block scripts that you have placed directly in your website. This includes all default youtube videos and most social media buttons. Here are some suggestions on how to work around this issue: 

- Enable privacy mode when embedding a YouTube video. This way the embedded video will not place cookies.
- Do not use social media share buttons. Many well-known solutions place heaps of cookies. Some common examples are the Facebook like button and the AddThis toolbar. 

# Translations

We currently have support for two languages: English and Dutch. Would you like to help translating this plugin? Please contact us trough [this page](http://www.hayona.nl/contact).

== Installation ==

Follow these steps to install the plugin: 

1. Upload the 'hayona-cookies' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings » Hayona Cookies to review the plugin settings and enable the plugin
4. Install Google Tag Manager to your website 

If you've never worked with the Google Tag Manager before, please watch [this short video](https://www.youtube.com/watch?v=buEZdno55SU) to get started. It's really not that complicated. 

== Screenshots ==

1. Add a customisable banner to the top of your website. 
2. Add a privacy settings form to a page of your choice
3. Customize the plugin through the options page under Settings » Hayona Cookies
