Mindshare Client Security
=============

- Author: Mindshare Studios, Inc.
- License: GPL v3
- Copyright: 2006-2014
- Link: http://mindsharelabs.com/downloads/mindshare-client-security/

Provides security updates and additional features for WordPress CMS websites.

# Advanced Usage:

Turn off Dashboard cleanup with:

	remove_action('admin_menu', array('mcms_ui', 'clear_dashboard'));
	remove_action('admin_head', array('mcms_ui', 'admin_head'));

Turn off Admin Bar tweaks with:

	remove_action('admin_bar_menu', array('mcms_ui', 'admin_bar_menu'));

# Changelog:

## 3.7.4.4

Made GZIP enabled by default for Mindshare hosting accounts in .htaccess

## 3.7.4.3

WP3.8 reading settings fix

## 3.7.4.2

add composer.json file, WP3.8 auto update support

## 3.7.4.1

minor bugfix

## 3.7.4

minor bugfix, upgraded Options for WordPress

## 3.7.3

re-enable EDD

## 3.7.2

temp disable EDD

## 3.7.1

critical bugfix

## 3.7

switch to EDD, Options for WordPress, updates for WP 3.8, moved to Git, improved htaccess rules

## 3.6.3

minor changes, remove tri.be widget, made .htaccess defaults more conservative, code cleanup

## 3.6.1&2

updated externals

## 3.6

removed login page functions, menu sorting, migrated to Mindshare Theme API

## 3.5.4

updated externals

## 3.5.3

updated MSAD lib to 0.4.2

bugfixes for update mechanism

## 3.5.2

updated externals (MSAD lib)

## 3.5.1

updated externals

## 3.5

updated externals, bugfixes, removed MS branding, updated settings

## 3.4.5

updated externals

## 3.4.4

updated externals, fixed login screen

## 3.4.3

updated externals

## 3.4.2

updated externals

## 3.4.1

minor change to user admin, fixed menu sorting

## 3.4

made this sucka respectable

## 3.3.9.3

removed blc more link

## 3.3.9.2

removed permissions check

## 3.3.9.1

bugfix for ACF

## 3.3.9

disable admin email override entirely due to form plugin issues (formidable, cf7)

## 3.3.8

disabled manageWP API, minor updates

## 3.3.7

major reorganization & cleanup

## 3.3.6

added fix for contact-form-7 plugin

## 3.3.5

added auto update mechanism

## 3.3.4

added security service indicator and check

## 3.3.3

added GetSupport menu to wp_admin_bar

## 3.3.2

added support for manageWP API (sort of... doesn't seem to be working)

## 3.3.1

bugfix

## 3.3

compatibility for WP 3.3

## 3.1.1

re-enabled custom fields and trackbacks

## 3.1

revamped htaccess rules, cleaned up structure / general code overhaul, removed pre WP 2.6 compatibility

## 3.0.9.4/5

optimized display:none css calls

## 3.0.9.3

removed #footer div from admin

## 3.0.9.2

fixed 'Howdy'

## 3.0.9.1

bugfixes

## 3.0.9

added security updates and a few other fixes

## 3.0.8.5

changed screen options so that author is visible

## 3.0.8.4

fixed notice at footer

## 3.0.8.3

updates for WP 3.0, removed Hide Dashboard for wpmu compat

## 3.0.8.2

syntax error fixed that affects user who are not level10

## 3.0.8.1

fixed js error introduced in version 3.0.8

## 3.0.8

fixed htaccess for adding www, added wpmu support, made update notices monthly, support for admin menu editor, removed howdy, crossdomain.xml

## 3.0.7

added extra security measures + update notification by email + tons of great new stuff

## 3.0.6.2

fixed permission errors for SWCP & removed WP-DB Manager nag

## 3.0.6.1

removed stupid yst_db_widget-hide again!!

## 3.0.6

removed header-logo div, sorted long admin menus, added hide dashboard capability, removed wpgeo dash widget

## removed pressthis from tools, removed yoast widget, added screen options, fixed admin menu indexes for unsets,

## profile page tweaks

## 3.0.5

css fix for wp shopping cart

## 3.0.4

added support postMash

## 3.0.3

added support for WP Geo, removed custom fields and trackbacks by default

## 3.0.2

added more security features, added htaccess redirects for login and logout pages

## 3.0.1

fixed JS error for expand/collapse all links in pagemash

## 3.0

updated dashboard widget removal mechanism, modded for page mash plugin

## 2.7.1

mod for Page Lists Plus, removed update nag for non-editors, removed info@ for hiding menu items, remove WordPress from title

## 2.7

overhauled for WP 2.7

## 2.3

Updated to work with Ozh Admin Menu

## 2.2

Added sign out reminder
