<?php
/*
 Plugin Name: Mindshare Security
 Plugin URI: https://mindsharelabs.com/downloads/mindshare-client-security/
 Description: Provides security updates and additional features for WordPress CMS websites. <a href="https://mind.sh/are/wordpress-security-and-backup-service/" target="_blank">Learn more &rsaquo;</a>
 Author: Mindshare Labs, Inc.
 Version: 3.8.2
 Author URI: https://mind.sh/are/
 */

// @todo eventually replace this with something cleaner, all we want is the damn domain
if (!defined('MCMS_DOMAIN_ROOT')) {
	if (array_key_exists('SERVER_NAME', $_SERVER) && isset($_SERVER[ 'SERVER_NAME' ])) {
		define('MCMS_DOMAIN_ROOT', preg_replace('#^www\.#', '', strtolower($_SERVER[ 'SERVER_NAME' ])));
	} else {
		define('MCMS_DOMAIN_ROOT', preg_replace('#^www\.#', '', strtolower($_SERVER[ 'HTTP_HOST' ])));
	}
}
if (!defined('MCMS_UPDATE_URL')) {
	define('MCMS_UPDATE_URL', 'https://mindsharelabs.com');
}

if (!defined('MCMS_PLUGIN_NAME')) {
	define('MCMS_PLUGIN_NAME', 'Mindshare Security');
}

if (!defined('MCMS_PLUGIN_SLUG')) {
	define('MCMS_PLUGIN_SLUG', 'mcms-admin');
}

if (!defined('MCMS_ADMIN_PATH')) {
	define('MCMS_ADMIN_PATH', plugin_dir_path(__FILE__));
}
if (!defined('MCMS_ADMIN_FILE')) {
	define('MCMS_ADMIN_FILE', __FILE__);
}

if (!defined('GF_LICENSE_KEY')) {
	define('GF_LICENSE_KEY', '25322ade6953d1770a492559697c1480');
}

// EDD updater
if (!class_exists('Mindshare_Security_Plugin_Updater')) {
	// load our custom updater
	include_once(MCMS_ADMIN_PATH . 'lib/Mindshare_Security_Plugin_Updater.php');
}

require_once(MCMS_ADMIN_PATH . 'inc/mcms-email.php');

if (!class_exists('mcms_admin')) :

	class mcms_admin {

		/**
		 * This version number for the Mindshare Auto Update library
		 * This value is returned when this class or its children if they are
		 * treated as a string (via __toString())
		 *
		 * @var string
		 */
		private $class_version = '3.8.2';

		/**
		 * Used for automatic updates
		 *
		 * @var string
		 */
		private $license_key = 'mindshare-client-security-free';

		function __construct() {

			require_once('inc/mcms-files.php');
			require_once('inc/mcms-ui.php');
			require_once('inc/mcms-settings.php');
			//require_once('inc/mcms-blacklist.php');

			add_action('admin_init', array('mcms_ui', 'admin_list'));
			add_action('admin_head', array('mcms_ui', 'admin_head'));
			add_action('admin_menu', array('mcms_ui', 'clear_dashboard'));
			add_action('admin_bar_menu', array('mcms_ui', 'admin_bar_menu'));
			add_action('dashboard_glance_items', array('mcms_ui', 'custom_rightnow'));
			add_action('pre_user_query', array('mcms_ui', 'user_list'));

			add_action('wp_dashboard_setup', array('mcms_ui', 'register_dashboard_widget'));

			add_filter('all_plugins', array('mcms_ui', 'plugin_replace'));
			add_filter('auto_update_plugin', '__return_true'); // add WP3.8+ auto-update support

			add_action('admin_init', array($this, 'check_update'));
			//add_action('admin_init', array($this, 'install')); //debugging

			register_activation_hook(MCMS_ADMIN_FILE, array($this, 'install'));
			//register_deactivation_hook(MCMS_ADMIN_FILE, array($this, 'uninstall'));
		}

		/**
		 * @return string
		 */
		function __toString() {
			return MCMS_PLUGIN_NAME . ' ' . $this->class_version;
		}

		/**
		 * install
		 *
		 */
		public function install() {

			//self::register_site(); // deprecated web service, maybe we should reactivate at some point?
			self::activate_acf();

			mcms_files::htaccess_defaults();
			mcms_files::htaccess_defaults_backupdb();
			mcms_files::delete_files();
			mcms_files::robots_defaults();
		}

		/**
		 * uninstall
		 *
		 */
		public function uninstall() {
			// nothing much here
		}

		/**
		 * Check for available updates
		 *
		 */

		public function check_update() {

			$edd_active = get_option('mcmsadmin_license_status');

			// EDD updates are already activated for this site, so exit
			if ($edd_active != 'valid') {
				$response = wp_remote_get(
					add_query_arg(
						array(
							'edd_action' => 'activate_license',
							'license'    => $this->license_key,
							'item_name'  => urlencode(MCMS_PLUGIN_NAME) // the name of our product in EDD
						),
						MCMS_UPDATE_URL
					),
					array(
						'timeout'   => 15,
						'sslverify' => FALSE,
					)
				);

				// make sure the response came back okay
				if (is_wp_error($response)) {
					return FALSE;
				}

				// decode the license data
				$license_data = json_decode(wp_remote_retrieve_body($response));

				// $license_data->license will be either "valid" or "invalid"
				if (is_object($license_data)) {
					update_option('mcmsadmin_license_status', $license_data->license);
				}
			}

			$mindshare_security_updater = new Mindshare_Security_Plugin_Updater(
				MCMS_UPDATE_URL,
				MCMS_ADMIN_FILE,
				array(
					'version'   => $this->class_version, // current version number
					'license'   => $this->license_key,
					'item_name' => MCMS_PLUGIN_NAME, // name of this plugin
					'author'    => 'Mindshare Labs, Inc.',
				)
			);
		}

		/**
		 * register_site
		 *
		 * @deprecated
		 *
		 */
		public static function register_site() {

			global $wp_version;
			$regurl = 'demo.mindsharelabs.com';
			$regfile = '/wp-content/plugins/mindshare_register_server.php?version=' . $wp_version;
			$fp = fsockopen($regurl, 80, $errno, $errstr, 30);
			if (!$fp) {
				//die ($errstr.' ('.$errno.')<br />\n');
			} else {
				fputs($fp, 'GET ' . $regfile . ' HTTP/1.0\r\n');
				fputs($fp, 'Host: ' . $regurl . '\r\n');
				fputs($fp, 'Referer: http://' . $_SERVER[ 'SERVER_NAME' ] . '\r\n');
				fputs($fp, 'User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n\r\n');
			}
		}

		/**
		 * Activate ACF
		 *
		 */
		public static function activate_acf() {
			// add ACF pro key
			$save = array(
				'key' => 'b3JkZXJfaWQ9MzI5NTN8dHlwZT1kZXZlbG9wZXJ8ZGF0ZT0yMDE0LTA3LTA3IDE1OjU4OjE5',
				'url' => get_bloginfo('url'),
			);
			$save = maybe_serialize($save);
			$save = base64_encode($save);
			update_option('acf_pro_license', $save);
		}
	}
endif;

$mcms = new mcms_admin();
