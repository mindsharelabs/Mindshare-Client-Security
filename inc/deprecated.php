<?php
/**
 * this file contain unused code from older versions, just a nice place to grow old and die.
 */

/**
 * stop ajax news updates on dashboard
 *
 * @deprecated
 */
function mcms_remove_dashboard_js() {
	remove_action('admin_head', 'index_js');
}

/**
 * add a private footer
 *
 * @deprecated
 */
function mcms_admin_footer() {
	// Admin-only features
	global $userdata;
	get_currentuserinfo();
	if (current_user_can('administrator') && (stristr($userdata->user_email, 'mindsharestudios.com'))) {
		echo '<style type="text/css">#footer { display:block !important; }</style>';
		echo '<p style="background-color:#000;font-size:12px;font-weight:bold;color:#fff;margin:0;padding:5px;position:absolute;bottom:0;left:0;width:100%;">';
		echo 'Hi, there! <a href="http://demo.mindsharestudios.com/wp-content/plugins/mindshare_read_servers.php">View all servers running Mindshare Labs\' code.</a>';
		echo '</p>';
	}
}

/**
 * @deprecated
 */
function mcms_create_files() {
	// close open directories
	mcms_create_file('index.php', WP_PLUGIN_DIR, TRUE);
	mcms_create_file('index.php', WP_CONTENT_DIR . '/uploads', TRUE);
	mcms_create_file('index.php', WP_CONTENT_DIR . '/backup-db', TRUE);
	mcms_create_file('index.php', WP_CONTENT_DIR . '/upgrade', TRUE);
	mcms_create_file('index.php', WP_CONTENT_DIR . '/themes', TRUE);
	mcms_create_file('.htaccess', WP_CONTENT_DIR . '/backup-db', TRUE);
}

/**
 * @deprecated
 */
function mcms_create_file($filename, $path, $enable) {
	$file = trailingslashit($path) . $filename;
	if ($enable) {
		if (!file_exists($file)) {
			$fh = @fopen($file, 'w');
			if ($fh) {
				fclose($fh);
			}
		}
	} else {
		if (file_exists($file) && filesize($file) === 0) {
			unlink($file);
		}
	}
}

/**
 * @deprecated
 */
function mcms_permissions($path, $perm) {
	clearstatcache();
	if (file_exists($path)) {
		$current_permissions = substr(sprintf(".%o.", fileperms($path)), -4);
		if ($current_permissions != $perm) {
			// change permissions
			@chmod($path, $perm);
		}
	}
}

/**
 * @deprecated
 */
function mcms_permissions_check() {
	mcms_permissions(ABSPATH, 0755);
	mcms_permissions(ABSPATH . 'wp-includes', 0755);
	mcms_permissions(ABSPATH . '.htaccess', 0644);
	mcms_permissions(ABSPATH . 'index.php', 0644);
	mcms_permissions(ABSPATH . 'wp-config.php', 0644);
	mcms_permissions(ABSPATH . 'js/', 0755);
	mcms_permissions(ABSPATH . 'wp-content/themes', 0755);
	mcms_permissions(ABSPATH . 'wp-content/plugins', 0755);
	mcms_permissions(ABSPATH . 'wp-admin', 0755);
	mcms_permissions(ABSPATH . 'wp-content', 0755);
}

//mcms_update_plugin($metadata->download_url, ABSPATH.'wp-content/plugins');

// mwp_premium_update_notification filter
//
// Hook to this filter to provide the new version of your plugin if available
//

//add_filter('mwp_premium_update_notification', 'mcms_mwp_update_notification');
if (!function_exists('mcms_mwp_update_notification')) {
	/**
	 * @deprecated
	 */
	function mcms_mwp_update_notification($premium_updates) {
		global $metadata_url, $metadata, $update_checker;

		if (!function_exists('get_plugin_data')) {
			include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}

		$mcms = get_plugin_data(__FILE__);   // or path to your main plugin file, we expect it to have standard header with plugin info
		$mcms[ 'type' ] = 'plugin';
		$mcms[ 'new_version' ] = $metadata->version;

		array_push($premium_updates, $mcms);

		return $premium_updates;
	}
}

// mwp_premium_perform_update filter
//
// Hook to this filter to return either the URL to the new version 
// or your callback function which will perform the update when called
//

//add_filter('mwp_premium_perform_update', 'mcms_mwp_perform_update');
if (!function_exists('mcms_mwp_perform_update')) {
	/**
	 * @deprecated
	 */
	function mcms_mwp_perform_update($update) {
		global $metadata_url, $metadata, $update_checker;

		if (!function_exists('get_plugin_data')) {
			include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}

		$mcms = get_plugin_data(__FILE__); // or path to your main plugin file, we expect it to have standard header with plugin info

		//$mcms['url'] = $metadata->download_url; // provide URL to the archive file with the new version
		//$mcms['callback'] = 'my_update_callback';
		//mcms_update_plugin($metadata->download_url, ABSPATH.'wp-content/plugins');

		array_push($update, $mcms);

		return $update;
	}
}

// mwp_premium_update_check filter
//
// Hook to this filter to supply your function that handles the update check
//
/**
 * @deprecated
 */
function mcms_update_plugin($source, $destination) {
	if (!class_exists('Plugin_Upgrader')) {
		include_once(ABSPATH . 'wp-admin/includes/misc.php');
		include(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
	}
	$upgrader = new Plugin_Upgrader();

	return $upgrader->run(array(
							  'source'      => $source, // required
							  'destination' => $destination // required
							  //'clear_destination' => false,
							  //'clear_working' => false,
							  //'hook_extra' => array()
						  ));
}

//slidepress
//if (!current_user_can("administrator")) {
//	echo "jQuery('li#toplevel_page_slidepress').remove();";
//}

// fix the admin menus 
//add_action('admin_menu','mcms_admin_menu');
/**
 * @deprecated
 */
function mcms_admin_menu() {
	// if the admin menu editor plugin is enabled don't change anything
	if (!is_plugin_active('admin-menu-editor/menu-editor.php')) {
		global $menu;
		global $submenu;
		//die(print_r($menu));

		if (!current_user_can('manage_links')) {
			unset($menu[ 15 ]); // remove links menu
		}
		if (!current_user_can('edit_posts')) {
			unset($menu[ 5 ]); // remove posts menu
		}
		if (!current_user_can('moderate_comments')) {
			unset($menu[ 25 ]); // remove comments menu
		}

		// Admin-only features
		global $userdata;
		get_currentuserinfo();
		if (current_user_can('level_10')) {
			// do nothing		
		} else {
			//unset($menu[0]); // remove dashboard menu
			unset($menu[ 60 ]); // remove appearance menu
			unset($menu[ 65 ]); // remove plugins menu
			unset($menu[ 75 ]); // remove tools menu
			unset($menu[ 80 ]); // remove settings menu
		}
	}
}

// Set the default language
//add_filter('locale', 'mcms_set_lang'); DISABLED
/**
 * @deprecated
 */
function mcms_set_lang() {
	global $locale;
	$locale = 'ms_US';

	return $locale;
}

//if (is_admin()) {
//	//add_action('admin_init', 'remove_core_update'); // remove core update for non admins
//	//add_action('admin_init', 'remove_plugin_update'); // remove plugin update for non admins
//}
// remove core-Update-Information
/**
 * @deprecated
 */
function remove_core_update() {
	if (!current_user_can('edit_plugins')) {
		# 2.3 to 2.7:
		add_action('init', create_function('$a', "remove_action('init', 'wp_version_check');"), 2);
		add_filter('pre_option_update_core', '__return_false');

		# 2.8 to 3.0:
		remove_action('wp_version_check', 'wp_version_check');
		remove_action('admin_init', '_maybe_update_core');
		add_filter('pre_transient_update_core', '__return_false');

		# 3.0:
		add_filter('pre_site_transient_update_core', '__return_false');
	}
}

// remove plugin-Update-Information
function remove_plugin_update() {
	if (!current_user_can('edit_plugins')) {
		# 2.3 to 2.7:
		add_action('admin_menu', create_function('$a', "remove_action('load-plugins.php', 'wp_update_plugins');"));
		# Why use the admin_menu hook? It's the only one available between the above hook being added and being applied
		add_action('admin_init', create_function('$a', "remove_action('admin_init', 'wp_update_plugins');"), 2);
		add_action('init', create_function('$a', "remove_action('init', 'wp_update_plugins');"), 2);
		add_filter('pre_option_update_plugins', '__return_false');

		# 2.8 to 3.0:
		remove_action('load-plugins.php', 'wp_update_plugins');
		remove_action('load-update.php', 'wp_update_plugins');
		remove_action('admin_init', '_maybe_update_plugins');
		remove_action('wp_update_plugins', 'wp_update_plugins');
		add_filter('pre_transient_update_plugins', '__return_false');

		# 3.0:
		remove_action('load-update-core.php', 'wp_update_plugins');
		add_filter('pre_site_transient_update_plugins', '__return_false');
	}
}

// remove theme-Update-Information
/**
 * @deprecated
 */
function remove_theme_update() {
	if (!current_user_can('edit_themes')) {
		# 2.8 to 3.0:
		remove_action('load-themes.php', 'wp_update_themes');
		remove_action('load-update.php', 'wp_update_themes');
		remove_action('admin_init', '_maybe_update_themes');
		remove_action('wp_update_themes', 'wp_update_themes');
		add_filter('pre_transient_update_themes', '__return_false');

		# 3.0:
		remove_action('load-update-core.php', 'wp_update_themes');
		add_filter('pre_site_transient_update_themes', '__return_false');
	}
}

//register_activation_hook(__FILE__, 'add_update_notification');
//register_activation_hook(__FILE__, 'remove_update_notification'); // disabled update email completely b/c they were not getting used
//register_deactivation_hook(__FILE__, 'remove_update_notification');
//add_action('update_email_evt', 'wordpress_update_task');

/**
 * @deprecated
 *
 * @return string
 */
function current_wp_version() {
	// check which version of wordpress is installed
	global $wp_version;
	$current_wp_version = $wp_version;

	return $current_wp_version;
}

/**
 * @deprecated
 *
 * @return mixed
 */
function latest_wp_version() {
	// check which version is latest
	$domain = "www.wordpress.org";
	$portno = 80;
	$method = "HEAD";
	$url = "/latest";

	$http_response = "";
	$http_request = $method . " " . $url . " HTTP/1.0\r\n";
	$http_request .= "\r\n";

	$fp = fsockopen($domain, $portno, $errno, $errstr);
	if ($fp) {
		fputs($fp, $http_request);
		while (!feof($fp)) {
			$http_response .= fgets($fp, 128);
		}
		fclose($fp);
	}

	$arr = explode("\r\n", $http_response);
	foreach ($arr as $k => $v) {
		if (strstr($v, ':')) {
			$arr2 = explode(':', $v);
			$headers[ $arr2[ 0 ] ] = $arr2[ 1 ];
		}
	}

	$datarep = $headers[ 'Content-Disposition' ];
	if (isset($datarep)) {
		$wordpress_filename = substr($datarep, strpos($datarep, '=') + 1);
		$version = str_replace('.tar.gz', '', $wordpress_filename);
		$version = str_replace('wordpress-', '', $version);

		//$version .= '999'; // DEBUGGING ONLY
		return $version;
	}
}

// adds extra recurrence options to wp scheduling
/**
 * @deprecated
 * @return array
 */
function more_recurrences() {
	return array(
		'weekly'  => array('interval' => 604800, 'display' => 'Once Weekly'),
		'monthly' => array('interval' => 2592000, 'display' => 'Once Monthly'),
	);
}

//add_filter('cron_schedules', 'more_recurrences');

/**
 * @deprecated
 */
function add_update_notification() {
	wp_schedule_event(time(), 'monthly', 'update_email_evt'); // send update reminder email

	wp_schedule_event(time(), 'daily', 'mcms_phone_home'); // send the current version to our DB
}

/**
 * @deprecated
 */
function wordpress_update_task() {

	if (version_compare(current_wp_version(), latest_wp_version(), "<")) {
		//$wpsender = get_option('admin_email');
		//$forwhom = get_option('admin_email');
		$wpsender = 'update-helper@mindsharestudios.com';
		$forwhom = get_option('admin_email');
		$subject = "WordPress UPDATE Available: " . wp_specialchars(get_option('blogname')) . " (" . current_wp_version() . "/" . latest_wp_version() . ")";
		$headers = "From: " . wp_specialchars(get_option('blogname')) . " <$wpsender>\n";
		$headers .= "Content-Type: text/html\n";
		$headers .= "Content-Transfer-Encoding: 8bit\n";
		$mailtext = "The WordPress installation at <strong>" . wp_specialchars(get_option('blogname')) . "</strong> (http://" . MCMS_DOMAIN_ROOT . "/wp-admin/update-core.php) is out-of-date. The currently installed version is " . current_wp_version() . " and the latest is " . latest_wp_version();
		mail($forwhom, $subject, $mailtext, $headers);
	}
}

/**
 * @deprecated
 */
function remove_update_notification() {
	wp_clear_scheduled_hook('update_email_evt');
}

if (function_exists('register_uninstall_hook')) {
	// it should run after all the other hooks, so it gets a late priority.
	add_action('admin_menu', 'mcms_sort_dashboard_menu', 999999);
} else {
	// it should run before any CSS menu plugins, so it gets the unusual "-1" priority.
	add_action('dashmenu', 'mcms_sort_dashboard_menu', -1);
}

// sign out reminder
//add_action('admin_notices', 'sign_out_reminder');
function sign_out_reminder() {
	if (strpos(wp_get_referer(), 'wp-login.php')) {
		?>
		<div class="updated fade" id="signoutreminder">
			<p><strong>Welcome!</strong> If you are using a shared computer, always remember <a href="<?php echo wp_logout_url(); ?>">Log Out</a> once you&#0146;re finished.</p>
		</div>
		<?php
	}
}

/*
// one press
jQuery('#dashboard_op').remove();

// yoast, sociable plugin
jQuery('#yoast_posts').remove();
jQuery('#yst_db_widget').remove();
jQuery('div.metabox-prefs label[for*="yoast"]').remove();
jQuery('div.metabox-prefs label[for*="yst_db_widget-hide"]').remove();

// page mash
jQuery('li#menu-pages div.wp-submenu li a:contains("pageMash")').html('Page Manager');
jQuery('a#pageMashInfo_toggle').css('display','none');
jQuery('div#pageMashInfo').css('display','none');
jQuery('a.rename').css('display','none');
jQuery('div#pageMash h2:contains("pageMash - pageManagement")').html('Page Manager');
var pageManagerNotice = '<h3 style=\'margin-bottom:0; clear:none;\'>Edit Page Order and Visibility</h3><ul style=\'margin-top:4px;\'><li>Simply drag the pages <strong>up</strong> or <strong>down</strong> to change your site\'s page order. You can also drag pages to the <strong>left</strong> or <strong>right</strong> to change the page\'s parent. The icon to the left of each page indicates whether it has child pages, <strong>double click</strong> the icon to <strong>expand or collapse</strong> it\'s children (a.k.a. subpages). Click <strong>hide</strong> to toggle the <strong>page visibility</strong> on your site navigation menu and sitemap. This does not unpublish the page, just removes the link to it from the menu. Press the <strong>Update</strong> button to save your changes.</li></ul>';
jQuery('div#pageMash p:contains("Just drag")').html(pageManagerNotice);

// post mash
jQuery('li div.wp-submenu li a:contains("postMash")').html('Post Manager');
jQuery('a#postMashInfo_toggle').css('display','none');
jQuery('div#postMashInfo').css('display','none');
jQuery('div#postMash h2:contains("postMash")').html('Post Manager');*/
