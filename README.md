# WP-Translations Sniffer

WP-Translations Theme Sniffer plugin using only i18n sniffs. `WordPress Coding Standard` is used from [WordPress-Coding-Standards](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards).

This is a fork of the original WordPress Plugin Theme Sniffer project, with specific i18n support.

![Screenshot](screenshot.png?raw=true)

## Installing

### Option 1: Easy

* Download [zip file](https://github.com/WP-Translations/theme-sniffer/releases/download/0.14/wp-t-theme-sniffer-0.14.zip). [Note: Please use this distribution plugin zip. GitHub provided zip will not work.]
* Install this as you normally install a WordPress plugin
* Activate plugin

### Option 2: Nerdy

* Clone this repo under `wp-content/plugins/`
* Run `composer install`
* Run `npm install`
* Activate plugin

## Using

* Go to `Appearance` -> `WP-T Sniffer`
* Select theme from the dropdown
* Click `GO`

## Options

* `Hide Warning` - Enable this to hide warnings.
* `Raw Output` - Enable this to display sniff report in plaintext format. Suitable to copy/paste report to trac ticket.

## Credits

The Theme Sniffer plugin was created by [Nilambar Sharma](http://nilambar.net) and he included a number of great contributions.

The current version of the WP-Translations Theme Sniffer was developed in conjunction with [FX Bénard](https://twitter.com/fxbenard), [Jérome Sadler](https://twitter.com/sadlerjerome) both members of the [WP-Translations Team](https://twitter.com/sadlerjerome).
