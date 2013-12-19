<?php
/*
 Plugin Name: Mindshare Client Security
 Plugin URI: http://svn.mindsharestudios.com/mcms-admin/
 Description: Provides security updates and additional features for WordPress CMS websites.
 Author: Mindshare Studios, Inc
 Version: 3.6.3
 Author URI: http://mind.sh/are/
 */

/*
 
 Changelog:
 
	 3.6.3 - minor changes, remove tri.be widget, made .htaccess defaults more conservative, code cleanup
	 3.6.1&2 - updated externals
	 3.6 - removed login page functions, menu sorting, migrated to Mindshare Theme API
	 3.5.4 - updated externals
	 3.5.3 - updated MSAD lib to 0.4.2 - bugfixes for update mechanism
	 3.5.2 - updated externals (MSAD lib)
	 3.5.1 - updated externals
	 3.5 - updated externals, bugfixes, removed MS branding, updated settings
	 3.4.5 - updated externals
	 3.4.4 - updated externals, fixed login screen
	 3.4.3 - updated externals
	 3.4.2 - updated externals
	 3.4.1 - minor change to user admin, fixed menu sorting
	 3.4 - made this sucka respectable
	 3.3.9.3 - removed blc more link
	 3.3.9.2 - removed permissions check
	 3.3.9.1 - bugfix for ACF
	 3.3.9 - disbable admin email override entirely bdue to form plugin issues (formiddable, cf7)
	 3.3.8 - disabled manageWP API, minor updates
	 3.3.7 - major reorganization & cleanup
	 3.3.6 - added fix for contact-form-7 plugin
	 3.3.5 - added auto update mechanism 
	 3.3.4 - added secuirty service indicator and check
	 3.3.3 - added GetSupport menu to wp_admin_bar
	 3.3.2 - added support for manageWP API (sort of... doesn't seem to be working)
	 3.3.1 - bugfix
	 3.3 - compatibility for WP 3.3
	 3.1.1 - re-enabled custom fields and trackbacks
	 3.1 - revamped htaccess rules, cleaned up structure / general code overhaul, removed pre WP 2.6 compatibility
	 3.0.9.4/5 - optimized display:none css calls
	 3.0.9.3 - removed #footer div from admin
	 3.0.9.2 - fixed 'Howdy'
	 3.0.9.1 - bugfixes
	 3.0.9 - added security updates and a few other fixes
	 3.0.8.5 - changed screen options so that author is visible
	 3.0.8.4 - fixed notice at footer
	 3.0.8.3 - updates for WP 3.0, removed Hide Dashboard for wpmu compat
	 3.0.8.2 - syntax error fixed that affects user who are not level10
	 3.0.8.1 - fixed js error introduced in version 3.0.8
	 3.0.8 - fixed htaccess for adding www, added wpmu support, made update notices monthly, support for admin menu editor, removed howdy, crossdomain.xml
	 3.0.7 - added extra security measures + update notification by email + tons of great new stuff
	 3.0.6.2 - fixed permission errors for SWCP & removed WP-DB Manager nag
	 3.0.6.1 - removed stupid yst_db_widget-hide again!!
	 3.0.6 - removed header-logo div, sorted long admin menus, added hide dashboard capability, removed wpgeo dash widget
	 removed pressthis from tools, removed yoast widget, added screen options, fixed admin menu indexes for unsets,
	 profile page tweaks
	 3.0.5 - css fix for wp shopping cart
	 3.0.4 - added support postMash
	 3.0.3 - added support for WP Geo, removed custom fields and trackbacks by default
	 3.0.2 - added more security features, added htaccess redirects for login and logout pages
	 3.0.1 - fixed JS error for expand/collapse all links in pagemash
	 3.0 - updated dashboard widget removal mechanism, modded for page mash plugin
	 2.7.1 - mod for Page Lists Plus, removed update nag for non-editors, removed info@ for hiding menu items, remove WordPress from title
	 2.7 - overhauled for WP 2.7
	 2.3 - Updated to work with Ozh Admin Menu
	 2.2 - Added sign out reminder
	 
 */

if(!defined('MCMS_DOMAIN_ROOT')) {
	define('MCMS_DOMAIN_ROOT', preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])));
}
if(!defined('MCMS_PLUGIN_NAME')) {
	define('MCMS_PLUGIN_NAME', 'Mindshare Client Security');
}

if(!defined('MCMS_ADMIN_PATH')) {
	define('MCMS_ADMIN_PATH', plugin_dir_path(__FILE__));
}

if(!defined('GF_LICENSE_KEY')) {
	define('GF_LICENSE_KEY', '25322ade6953d1770a492559697c1480');
}

if(!class_exists('mcms_admin')) :

	class mcms_admin {

		/**
		 * @var mixed options
		 */
		public $options;

		function __construct() {

			$this->options = get_option('mindshare_admin_options');

			require_once('inc/mcms-files.php');
			require_once('inc/mcms-ui.php');
			require_once('inc/mcms-settings.php');

			add_action('pre_user_query', array('mcms_ui', 'user_list'));
			add_action('plugins_loaded', array('mcms_ui', 'options_page'));
			add_action('admin_init', array($this, 'init'));
			add_action('admin_init', array('mcms_ui', 'admin_list'));
			add_action('admin_menu', array('mcms_ui', 'clear_dashboard'));
			register_activation_hook(__FILE__, array($this, 'install'));
		}

		/**
		 * @return string
		 */
		function __toString() {
			return MCMS_PLUGIN_NAME.' 3.6.3';
		}

		/**
		 * install
		 *
		 */
		public function install() {

			self::register_site();
			mcms_files::htaccess_defaults();
			mcms_files::htaccess_defaults_backupdb();
			mcms_files::delete_files();
			mcms_files::robots_defaults();
			mcms_files::crossdomain();

			if($this->options !== FALSE) {
				if(array_key_exists('mcms_load_defaults', $this->options)) {
					mcms_settings::defaults();
					mcms_settings::rewrite();
					//add_action('init', array('mcms_settings', 'rewrite'));
					$this->options['mcms_load_defaults'] = FALSE;
					update_option('mindshare_admin_options', $this->options);
				}
			}
		}

		/**
		 * init
		 *
		 */
		public function init() {
			require_once('lib/mindshare-auto-update/mindshare-auto-update.php');
			new mindshare_auto_update(plugin_basename(__FILE__), plugin_dir_path(__FILE__));

			add_action('admin_head', array('mcms_ui', 'admin_head'));
			add_action('admin_bar_menu', array('mcms_ui', 'admin_bar_menu'));
			add_action('wp_dashboard_setup', array('mcms_ui', 'register_dashboard_widget'));

			add_filter('all_plugins', array('mcms_ui', 'plugin_replace'));
		}

		/**
		 * register_site
		 *
		 */
		public static function register_site() {

			global $wp_version;
			$regurl = 'demo.mindsharestudios.com';
			$regfile = '/wp-content/plugins/mindshare_register_server.php?version='.$wp_version;
			$fp = fsockopen($regurl, 80, $errno, $errstr, 30);
			if(!$fp) {
				//die ($errstr.' ('.$errno.')<br />\n');
			} else {
				fputs($fp, 'GET '.$regfile.' HTTP/1.0\r\n');
				fputs($fp, 'Host: '.$regurl.'\r\n');
				fputs($fp, 'Referer: http://'.$_SERVER['SERVER_NAME'].'\r\n');
				fputs($fp, 'User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n\r\n');
			}
		}
	}
endif;

$mcms = new mcms_admin();
