if("object"!=typeof JSON&&(JSON={}),function(){"use strict";function f(e){return 10>e?"0"+e:e}function this_value(){return this.valueOf()}function quote(e){return rx_escapable.lastIndex=0,rx_escapable.test(e)?'"'+e.replace(rx_escapable,function(e){var t=meta[e];return"string"==typeof t?t:"\\u"+("0000"+e.charCodeAt(0).toString(16)).slice(-4)})+'"':'"'+e+'"'}function str(e,t){var n,o,r,i,s=gap,a,c=t[e];switch(c&&"object"==typeof c&&"function"==typeof c.toJSON&&(c=c.toJSON(e)),"function"==typeof rep&&(c=rep.call(t,e,c)),typeof c){case"string":return quote(c);case"number":return isFinite(c)?String(c):"null";case"boolean":case"null":return String(c);case"object":if(!c)return"null";if(gap+=indent,a=[],"[object Array]"===Object.prototype.toString.apply(c)){for(i=c.length,n=0;i>n;n+=1)a[n]=str(n,c)||"null";return r=0===a.length?"[]":gap?"[\n"+gap+a.join(",\n"+gap)+"\n"+s+"]":"["+a.join(",")+"]",gap=s,r}if(rep&&"object"==typeof rep)for(i=rep.length,n=0;i>n;n+=1)"string"==typeof rep[n]&&(o=rep[n],r=str(o,c),r&&a.push(quote(o)+(gap?": ":":")+r));else for(o in c)Object.prototype.hasOwnProperty.call(c,o)&&(r=str(o,c),r&&a.push(quote(o)+(gap?": ":":")+r));return r=0===a.length?"{}":gap?"{\n"+gap+a.join(",\n"+gap)+"\n"+s+"}":"{"+a.join(",")+"}",gap=s,r}}var rx_one=/^[\],:{}\s]*$/,rx_two=/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,rx_three=/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,rx_four=/(?:^|:|,)(?:\s*\[)+/g,rx_escapable=/[\\\"\u0000-\u001f\u007f-\u009f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,rx_dangerous=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;"function"!=typeof Date.prototype.toJSON&&(Date.prototype.toJSON=function(){return isFinite(this.valueOf())?this.getUTCFullYear()+"-"+f(this.getUTCMonth()+1)+"-"+f(this.getUTCDate())+"T"+f(this.getUTCHours())+":"+f(this.getUTCMinutes())+":"+f(this.getUTCSeconds())+"Z":null},Boolean.prototype.toJSON=this_value,Number.prototype.toJSON=this_value,String.prototype.toJSON=this_value);var gap,indent,meta,rep;"function"!=typeof JSON.stringify&&(meta={"\b":"\\b","	":"\\t","\n":"\\n","\f":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"},JSON.stringify=function(e,t,n){var o;if(gap="",indent="","number"==typeof n)for(o=0;n>o;o+=1)indent+=" ";else"string"==typeof n&&(indent=n);if(rep=t,t&&"function"!=typeof t&&("object"!=typeof t||"number"!=typeof t.length))throw new Error("JSON.stringify");return str("",{"":e})}),"function"!=typeof JSON.parse&&(JSON.parse=function(text,reviver){function walk(e,t){var n,o,r=e[t];if(r&&"object"==typeof r)for(n in r)Object.prototype.hasOwnProperty.call(r,n)&&(o=walk(r,n),void 0!==o?r[n]=o:delete r[n]);return reviver.call(e,t,r)}var j;if(text=String(text),rx_dangerous.lastIndex=0,rx_dangerous.test(text)&&(text=text.replace(rx_dangerous,function(e){return"\\u"+("0000"+e.charCodeAt(0).toString(16)).slice(-4)})),rx_one.test(text.replace(rx_two,"@").replace(rx_three,"]").replace(rx_four,"")))return j=eval("("+text+")"),"function"==typeof reviver?walk({"":j},""):j;throw new SyntaxError("JSON.parse")})}(),function(e){if("function"==typeof define&&define.amd)define(e);else if("object"==typeof exports)module.exports=e();else{var t=window.Cookies,n=window.Cookies=e();n.noConflict=function(){return window.Cookies=t,n}}}(function(){function e(){for(var e=0,t={};e<arguments.length;e++){var n=arguments[e];for(var o in n)t[o]=n[o]}return t}function t(n){function o(t,r,i){var s;if(arguments.length>1){if(i=e({path:"/"},o.defaults,i),"number"==typeof i.expires){var a=new Date;a.setMilliseconds(a.getMilliseconds()+864e5*i.expires),i.expires=a}try{s=JSON.stringify(r),/^[\{\[]/.test(s)&&(r=s)}catch(c){}return r=encodeURIComponent(String(r)),r=r.replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),t=encodeURIComponent(String(t)),t=t.replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent),t=t.replace(/[\(\)]/g,escape),document.cookie=[t,"=",r,i.expires&&"; expires="+i.expires.toUTCString(),i.path&&"; path="+i.path,i.domain&&"; domain="+i.domain,i.secure?"; secure":""].join("")}t||(s={});for(var u=document.cookie?document.cookie.split("; "):[],f=/(%[0-9A-Z]{2})+/g,p=0;p<u.length;p++){var l=u[p].split("="),h=l[0].replace(f,decodeURIComponent),d=l.slice(1).join("=");'"'===d.charAt(0)&&(d=d.slice(1,-1));try{if(d=n&&n(d,h)||d.replace(f,decodeURIComponent),this.json)try{d=JSON.parse(d)}catch(c){}if(t===h){s=d;break}t||(s[h]=d)}catch(c){}}return s}return o.get=o.set=o,o.getJSON=function(){return o.apply({json:!0},[].slice.call(arguments))},o.defaults={},o.remove=function(t,n){o(t,"",e(n,{expires:-1}))},o.withConverter=t,o}return t()}),"undefined"==typeof dataLayer)var dataLayer=[];var CookieBanner=function(){this.settings={timestamp:0,isSettingsPage:!1,implicitConsentEnabled:!1,cookieExpiration:365},this.debounce=function(e,t,n){var o;return function(){var r=this,i=arguments,s=function(){o=null,n||e.apply(r,i)},a=n&&!o;clearTimeout(o),o=setTimeout(s,t),a&&e.apply(r,i)}},this.isRefresh=function(){var e=Cookies.get("hc_landing_url"),t=window.location.href;return e===t},this.consentIsValid=function(){var e=this,t=Cookies.getJSON("hc_consent");if(null!=t){var n=t.timestamp;return n>e.settings.timestamp}},this.hasConsent=function(){var e=this,t=Cookies.getJSON("hc_consent");if(null!=t){var n=t.consent;return!(n!==!0||!e.consentIsValid())}},this.hasRejection=function(){var e=this,t=Cookies.getJSON("hc_consent");if(null!=t){var n=t.consent;return!(n!==!1||!e.consentIsValid())}},this.hasImplicitConsent=function(){var e=Cookies.get("hc_implicit");return null!=e},this.saveConsent=function(e){var t=this;Cookies.remove("hc_consent");var n={timestamp:+new Date,consent:e};Cookies.set("hc_consent",n,{expires:t.settings.cookieExpiration})},this.saveLandingUrl=function(e){var t=window.location.href;null==e&&Cookies.set("hc_landing_url",t)},this.resetBodyPadding=function(){var e=jQuery(".hc-banner").outerHeight();jQuery(".hc-banner__faux-padding").css("height",e);var t=jQuery(".hc-banner__faux-padding").offset();jQuery(".hc-banner").css("top",t.top)},this.showBanner=function(){var e=this;jQuery(".hc-banner").show(),jQuery("body").prepend('<div class="hc-banner__faux-padding"></div>'),jQuery(window).on("load",e.resetBodyPadding);var t=e.debounce(function(){e.resetBodyPadding()},250);window.addEventListener("resize",t)},this.closeBanner=function(){jQuery(".hc-banner").hide(),jQuery(".hc-banner__faux-padding").hide()},this.init=function(e){var t=this;if(jQuery.extend(t.settings,t.settings,e),t.hasConsent()?dataLayer.push({event:"consent"}):t.hasRejection()||(t.hasImplicitConsent()&&!t.settings.isSettingsPage&&!t.isRefresh()&&t.settings.implicitConsentEnabled?(t.saveConsent(!0),dataLayer.push({event:"consent"})):(t.showBanner(),t.saveLandingUrl(Cookies.get("hc_landing_url")),t.settings.implicitConsentEnabled&&Cookies.set("hc_implicit",!0))),jQuery(".accept-cookies").length&&(jQuery(".accept-cookies").on("click",function(e){e.preventDefault(),t.closeBanner(),t.saveConsent(!0),dataLayer.push({event:"consent"})}),jQuery(".reject-cookies").on("click",function(e){e.preventDefault(),t.closeBanner(),t.saveConsent(!1)})),t.settings.isSettingsPage){jQuery(".hc-settings").show();var n=function(){jQuery(".reject-cookies").addClass("hc-button--grey"),jQuery(".accept-cookies").removeClass("hc-button--grey"),jQuery(".reject-cookies span").html(""),jQuery(".accept-cookies span").html("✓ ")},o=function(){jQuery(".reject-cookies").removeClass("hc-button--grey"),jQuery(".accept-cookies").addClass("hc-button--grey"),jQuery(".reject-cookies span").html("✓ "),jQuery(".accept-cookies span").html("")};t.hasConsent()?n():t.hasRejection()&&o(),jQuery(".accept-cookies").on("click",n),jQuery(".reject-cookies").on("click",o)}}},hayonaCookies=new CookieBanner,hasHayonaCookieConsent=function(){return!!hayonaCookies.hasConsent()};