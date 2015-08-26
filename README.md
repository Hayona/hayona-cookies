# Hayona Cookies

Comply with EU cookie law: tell your visitors how you use cookies, obtain their consent and give them some control.

## Description

This simple and lightweight WordPress plugin helps you to comply with the EU cookie law:

1. Insert a simple banner on your website to *inform users* about the cookies you are using, to *obtain consent* for the cookies and to offer them the possibility to *change their preferences*. 
2. Block cookies for which permission is required until you obtain their consent.
3. Enable visitors to adjust their *cookie preferences* through a small form on your site.
4. Enable *implied consent* which means that visitors give consent by clicking to the next page. The cookies will be placed and the banner will disappear.
5. Cookies that do not require permission are placed immediately. For instance *Google Analytics* under [certain conditions](https://www.interpedia.nl/analytics/beheer/google-analytics-instellen-cookiewetgeving), affiliate cookies and functional cookies.

This plugin offers some unique characteristics:

1. Scripts are placed through the Google Tag Manager
2. Caching has no influence on this plugin or vice-versa
3. PII cookies are blocked until visitors give consent
4. Functional and non-PII cookies are placed immediately
5. Implied consent is optional

Using Google Tag Manager to place scripts on your website is a very simple and straightforward process. It only takes five minutes. Watch this video to see how it works:

[https://www.youtube.com/watch?v=buEZdno55SU](https://www.youtube.com/watch?v=buEZdno55SU)

## Screenshots

### Add a customizable cookie banner to the top of your website

![Cookie banner](/assets/screenshot-1.png?raw=true)

### Add a privacy settings form to a page of your choice

![Privacy settings form](/assets/screenshot-2.png?raw=true)

### Customize the plugin through the options page under Settings » Hayona Cookies

![WordPress plugin options](/assets/screenshot-3.png?raw=true)

## Installation

Follow these steps to install the plugin: 

1. Upload the 'hayona-cookies' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings » Hayona Cookies to review the plugin settings and enable the plugin

The plugin needs Google Tag Manager installed on your site for it to work. If you've never worked with the Google Tag Manager before, please watch [this short video](https://www.youtube.com/watch?v=buEZdno55SU) to get started. It's really not that complicated. In the video we go through the following steps:

1. Make sure Google Tag Manager (GMT) is installed on your website
2. Go to triggers and add a new trigger
3. Select 'Custom Event' and give it the name 'consent'.
4. Add scripts to your website under 'Tags'. 
5. Add the trigger from step 3 to each script that needs consent from your users. 

If you're done adding scripts to GMT, don't forget to press publish to push the changes to your live website.

## About the cookie law in the EU

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

If you don't comply you risk enforcement action from regulators, including a very high fine.

However, non-compliance could also have other consequences. There is evidence that consumers avoid engaging with websites where they believe their privacy is at risk. In general, consumers just don’t like cookies and they don’t like being tracked.

Hayona, the developer of this plugin, therefore believes that the ideal situation is a website that does not need this plugin at all! Most websites do not need PII cookies and without PII cookies the only thing you need is a page describing your privacy policy. No banner or visitor consent is needed.

Be therefore very critical towards your website builder and online marketing people; don’t accept any cookie-placing scripts unless you are absolutely sure it is needed for your business. Implement Google Analytics in non-PII mode by masking ip addresses and not sharing your data with Google or others. Do not use social media buttons with scripts; why would you help the social media to follow and track your website visitors? Use simple hyperlinks instead; it even improves the performance of your site. Think twice about remarketing; consumers will get the feeling they are being tracked. And if you embed YouTube videos, always enable the privacy mode!

## Detect your cookies

WordPress plugins are powerful tools to build amazing things in relatively short time. The downside of using someone else’s scripts is that you don't always know exactly what is happening when someone visits your website and which cookies are placed. We suggest that you use the [ghostery browser add-on](https://www.ghostery.com/en/our-solutions/ghostery-add-on/) to measure which cookies are placed on your website. 

This plugin does not block scripts that you have placed directly in your website. This includes all default youtube videos and most social media buttons. Here are some suggestions on how to work around this issue: 

- Enable privacy mode when embedding a YouTube video. This way the embedded video will not place cookies.
- Do not use social media share buttons. Many well-known solutions place heaps of cookies. Some common examples are the Facebook like button and the AddThis toolbar. 

## Translations

We currently have support for two languages: English and Dutch. Would you like to help translating this plugin? Please contact us trough [this page](http://www.hayona.nl/contact).
