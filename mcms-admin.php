<?php
/*
 Plugin Name: Mindshare Client Security
 Plugin URI: http://mindsharelabs.com/downloads/mindshare-client-security/
 Description: Provides security updates and additional features for WordPress CMS websites. <a href="http://mind.sh/are/wordpress-security-and-backup-service/" target="_blank">Learn more &rsaquo;</a>
 Author: Mindshare Studios, Inc
 Version: 3.7.4.4
 Author URI: http://mind.sh/are/
 */


if(!defined('MCMS_DOMAIN_ROOT')) {
	define('MCMS_DOMAIN_ROOT', preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])));
}
if(!defined('MCMS_UPDATE_URL')) {
	define('MCMS_UPDATE_URL', 'http://mindsharelabs.com');
}

if(!defined('MCMS_PLUGIN_NAME')) {
	define('MCMS_PLUGIN_NAME', 'Mindshare Client Security');
}

if(!defined('MCMS_PLUGIN_SLUG')) {
	define('MCMS_PLUGIN_SLUG', 'mcms-admin');
}

if(!defined('MCMS_ADMIN_PATH')) {
	define('MCMS_ADMIN_PATH', plugin_dir_path(__FILE__));
}

if(!defined('GF_LICENSE_KEY')) {
	define('GF_LICENSE_KEY', '25322ade6953d1770a492559697c1480');
}

// EDD updater
if(!class_exists('Mindshare_Security_Plugin_Updater')) {
	// load our custom updater
	include_once(MCMS_ADMIN_PATH.'lib/Mindshare_Security_Plugin_Updater.php');
}

if(!class_exists('mindshare_admin_options')) {
	require_once(MCMS_ADMIN_PATH.'lib/options/options.php'); // include options framework
}

if(!class_exists('mcms_admin')) :

	class mcms_admin {

		/**
		 * @var mixed options
		 */
		public $options;

		/**
		 * This version number for the Mindshare Auto Update library
		 * This value is returned when this class or its children if they are
		 * treated as a string (via __toString())
		 *
		 * @var string
		 */
		private $class_version = '3.7.4.4';

		/**
		 * Used for automatic updates
		 *
		 * @var string
		 */
		private $license_key = 'mindshare-client-security-free';

		function __construct() {

			$this->options = get_option('mindshare_admin_options');

			require_once('inc/mcms-files.php');
			require_once('inc/mcms-ui.php');
			require_once('inc/mcms-settings.php');

			add_action('admin_init', array('mcms_ui', 'admin_list'));
			add_action('admin_head', array('mcms_ui', 'admin_head'));
			add_action('admin_menu', array('mcms_ui', 'clear_dashboard'));
			add_action('admin_bar_menu', array('mcms_ui', 'admin_bar_menu'));
			add_action('dashboard_glance_items', array('mcms_ui', 'custom_rightnow'));
			add_action('pre_user_query', array('mcms_ui', 'user_list'));
			add_action('plugins_loaded', array('mcms_ui', 'options_page'));
			add_action('wp_dashboard_setup', array('mcms_ui', 'register_dashboard_widget'));
			add_filter('all_plugins', array('mcms_ui', 'plugin_replace'));

			add_filter('auto_update_plugin', '__return_true'); // add WP3.8+ auto-update support
			add_action('admin_init', array($this, 'check_update'));
			//add_action('admin_init', array($this, 'install')); //debugging
			register_activation_hook(__FILE__, array($this, 'install'));
		}

		/**
		 * @return string
		 */
		function __toString() {
			return MCMS_PLUGIN_NAME.' '.$this->class_version;
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
		 * Check for available updates
		 *
		 */

		public function check_update() {

			$edd_active = get_option('mcmsadmin_license_status');

			// EDD updates are already activated for this site, so exit
			if($edd_active != 'valid') {
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
						'sslverify' => FALSE
					)
				);

				// make sure the response came back okay
				if(is_wp_error($response)) {
					return FALSE;
				}

				// decode the license data
				$license_data = json_decode(wp_remote_retrieve_body($response));

				// $license_data->license will be either "valid" or "invalid"
				if(is_object($license_data)) {
					update_option('mcmsadmin_license_status', $license_data->license);
				}
			}

			$mindshare_security_updater = new Mindshare_Security_Plugin_Updater(
				MCMS_UPDATE_URL,
				__FILE__,
				array(
					'version'   => $this->class_version, // current version number
					'license'   => $this->license_key,
					'item_name' => MCMS_PLUGIN_NAME, // name of this plugin
					'author'    => 'Mindshare Studios, Inc.'
				)
			);
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
