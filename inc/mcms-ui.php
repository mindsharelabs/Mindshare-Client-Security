<?php
/**
 * mcms-ui.php
 *
 * ui changes
 *
 * @created   9/23/12 3:11 PM
 * @author    Mindshare Labs, Inc.
 * @copyright Copyright (c) 2012
 * @link      http://www.mindsharelabs.com/documentation/
 *
 */

if (!class_exists('mcms_ui')) :

	/**
	 * Class mcms_ui
	 */
	class mcms_ui {

		public static $admins;

		/**
		 * wp & plugin CSS changes
		 *
		 */
		public static function admin_head() {
			?>
			<style type="text/css">
				#footer, #footer-upgrade, #favorite-actions, #footer-left, #acx_plugin_dashboard_widget, #blogplay_db_widget, #yoast_posts, #yst_db_widget, #dashboard_op, #header-logo, #wpgeo_dashboard, #dashboard_secondary, #dashboard_primary, #dashboard_quick_press, #dashboard_recent_comments, #dashboard_plugins, #dashboard_right_now, #fluency-footer, #wp-admin-bar-wp-logo, #tab-link-help-content, #tab-panel-help-content, #wp-admin-bar-new-content, #welcome-panel, label[for^='wp_welcome_panel'], #blc-more-plugins-link-wrap, #sidebar-container.wpseo_content_cell {
					display: none !important;
				}
			</style>

			<?php
		}

		/**
		 * Sort admin menus alphabetically
		 *
		 */
		public static function sort_dashboard_menu() {
			global $submenu;
			function comparator($a, $b) {
				return strcasecmp($a[ 0 ], $b[ 0 ]);
			}

			// list any menus to sort
			if (function_exists('register_uninstall_hook')) {
				$menus_to_sort = array('tools.php', 'options-general.php');
			} else {
				$menus_to_sort = array('tools.php', 'options-general.php');
			}
			foreach ($submenu as $key => $items) {
				if (!in_array($key, $menus_to_sort)) {
					continue;
				}
				usort($items, "comparator");
				$submenu[ $key ] = $items;
			}
		}

		/**
		 * Register dashboard support widget
		 *
		 */
		public static function register_dashboard_widget() {
			wp_add_dashboard_widget('mcms_dashboard_widget', 'Mindshare Studios\' Client Services &amp; Support', array('mcms_ui', 'dashboard_widget'));
		}

		/**
		 * Print dashboard widget
		 *
		 */
		public static function dashboard_widget() {
			echo '<iframe src="//mindsharelabs.com/update/helpers/mindshare-custom-dashboard.php?ref=' . get_bloginfo('url') . '" frameborder="0" scrolling="No" width="420" height="200" marginheight="0" marginwidth="0" /></iframe>';
		}

		// add/remove dashboard widgets
		/**
		 *
		 */
		public static function clear_dashboard() {
			remove_meta_box('tribe_dashboard_widget', 'dashboard', 'normal');
			remove_meta_box('dashboard_quick_press', 'dashboard', 'core');
			remove_meta_box('dashboard_right_now', 'dashboard', 'core');
			remove_meta_box('dashboard_recent_comments', 'dashboard', 'core');
			remove_meta_box('dashboard_plugins', 'dashboard', 'core');
			remove_meta_box('dashboard_primary', 'dashboard', 'core');
			remove_meta_box('dashboard_secondary', 'dashboard', 'core');
			$user = wp_get_current_user();
			if (!in_array($user->ID, self::admin_list())) {
				remove_submenu_page('plugins.php', 'plugin-editor.php');
			}
		}

		/**
		 * Add all custom post types to the "Right Now" box on the Dashboard
		 *
		 */
		public function custom_rightnow() {
			$post_types = get_post_types(array('show_in_nav_menus' => TRUE, '_builtin' => FALSE), 'objects');
			foreach ($post_types as $post_type => $post_type_obj) {
				$num_posts = wp_count_posts($post_type);
				if ($num_posts && $num_posts->publish) {
					printf(
						'<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s %3$s</a></li>',
						$post_type,
						number_format_i18n($num_posts->publish),
						$post_type_obj->label
					);
				}
			}
		}

		/**
		 * Custom Admin Menus
		 *
		 */
		public static function admin_bar_menu() {
			global $wp_admin_bar;
			if (!is_admin() || !is_admin_bar_showing()) {
				return;
			}

			$wp_admin_bar->add_menu(
				array(
					'title' => 'Get Support',
					'href'  => 'http://mind.sh/are/contact/?ref=' . get_bloginfo('url'),
					'id'    => 'mcms',
					'meta'  => array('title' => 'Mindshare Studios Support', 'target' => '_blank'),
				)
			);
			if (!is_plugin_active('worker/init.php')) {

				$wp_admin_bar->add_menu(
					array(
						'parent' => 'mcms',
						'title'  => 'WordPress Security &amp; Backup Service',
						'id'     => 'mcms-security-service',
						'href'   => 'http://mind.sh/are/wordpress-security-and-backup-service/?ref=' . get_bloginfo('url'),
						'meta'   => array('target' => '_blank'),
					)
				);
			}

			$wp_admin_bar->add_menu(
				array(
					'parent' => 'mcms',
					'title'  => 'View Hosting Status Updates',
					'id'     => 'mcms-updates',
					'href'   => 'http://mindsharestatus.wordpress.com',
					'meta'   => array('target' => '_blank'),
				)
			);
			if (@$_SERVER[ 'SERVER_ADDR' ] == '172.97.102.35') {
				$wp_admin_bar->add_menu(
					array(
						'parent' => 'mcms',
						'title'  => 'View Realtime Server Performance',
						'id'     => 'mcms-performance',
						'href'   => 'http://mind.sh/are/server/?ref=' . get_bloginfo('url'),
						'meta'   => array('target' => '_blank'),
					)
				);
			}

			$wp_admin_bar->add_menu(
				array(
					'parent' => 'mcms',
					'title'  => 'Report an Outage or Emergency',
					'id'     => 'mcms-emergency',
					'href'   => 'http://mind.sh/are/emergency/?ref=' . get_bloginfo('url'),
					'meta'   => array('target' => '_blank'),
				));

			$wp_admin_bar->add_menu(
				array(
					'parent' => 'mcms',
					'title'  => 'Contact Mindshare Studios',
					'id'     => 'mcms-contact',
					'href'   => 'http://mind.sh/are/contact/?ref=' . get_bloginfo('url'),
					'meta'   => array('target' => '_blank'),
				));

			if (is_plugin_active('worker/init.php')) {
				$wp_admin_bar->add_menu(
					array(
						'title' => 'Security <span style="color:#00CC00;">ON</span>',
						'href'  => 'http://mind.sh/are/wordpress-security-and-backup-service/check/?url=' . get_bloginfo('url') . '&amp;active=1',
						'id'    => 'mcms-security',
						'meta'  => array(
							'title'  => 'Security &amp; Backup Service is enabled for your domain ' . get_bloginfo('url'),
							'target' => '_blank',
						),
					)
				);
			} else {
				$wp_admin_bar->add_menu(
					array(
						'title' => 'Security <span style="color:#FF0000;">OFF</span>',
						'href'  => 'http://mind.sh/are/wordpress-security-and-backup-service/check/?url=' . get_bloginfo('url') . '&amp;active=0&amp;sale=1&d=' . str_replace(array(
																																												  'http://',
																																												  'https://',
																																											  ), '', get_home_url()),
						'id'    => 'mcms-security',
						'meta'  => array(
							'title'  => 'Security &amp; Backup Service is NOT enabled for your domain ' . get_bloginfo('url') . ' (click for more information)',
							'target' => '_blank',
						),
					));
				// sale
				$wp_admin_bar->add_menu(
					array(
						'parent' => 'mcms-security',
						'title'  => '<span style="text-shadow:none;color:#FF0000;font-weight:700">Security &amp; Backups are not enabled.</span> Learn more &rsaquo;',
						'id'     => 'mcms-security-sale',
						'href'   => 'http://mind.sh/are/wordpress-security-and-backup-service/check/?url=' . get_bloginfo('url') . '&amp;active=0&amp;sale=1&d=' . str_replace(array(
																																												   'http://',
																																												   'https://',
																																											   ), '', get_home_url()),
						'meta'   => array('target' => '_blank', 'title' => 'Learn more >'),
					)
				);
				$wp_admin_bar->add_menu(
					array(
						'parent' => 'mcms-security',
						'title'  => '<span>Protect your website now for <span style="color:#00CC00;">$9.95</span>/month (regularly $14.95)</span>',
						'id'     => 'mcms-security-sale2',
						'href'   => 'http://mind.sh/are/wordpress-security-and-backup-service/check/?url=' . get_bloginfo('url') . '&amp;active=0&amp;sale=1&d=' . str_replace(array(
																																												   'http://',
																																												   'https://',
																																											   ), '', get_home_url()),
						'meta'   => array('title' => 'On sale for a limited time >', 'target' => '_blank'),
					)
				);
			}
		}

		/**
		 * @param $user_search
		 */
		public static function user_list($user_search) {
			if (!empty(self::$admins) && is_admin()) {
				$user = wp_get_current_user();
				if (!in_array($user->ID, self::$admins)) {
					global $wpdb;
					foreach (self::$admins as $id) {
						$user_search->query_where = str_replace('WHERE 1=1', "WHERE 1=1 AND {$wpdb->users}.ID<>" . $id, $user_search->query_where);
					}
				}
			}
		}

		/**
		 * @param $all_plugins
		 *
		 * @return mixed
		 */
		public static function plugin_replace($all_plugins) {

			if (!function_exists('get_plugins')) {
				include_once(ABSPATH . 'wp-admin/includes/plugin.php');
			}
			$activated_plugins = get_option('active_plugins');
			if (!$activated_plugins) {
				$activated_plugins = array();
			}
			if (in_array('mcms-admin/mcms-admin.php', $activated_plugins)) {
				$user = wp_get_current_user();
				if (!in_array($user->ID, self::$admins)) {
					unset($all_plugins[ 'mcms-admin/mcms-admin.php' ]);
				}
			}

			return $all_plugins;
		}

		/**
		 * @return array
		 */
		public static function admin_list() {
			self::$admins = get_users(
				array(
					'role'           => 'Administrator',
					'search'         => '*@' . base64_decode('bWluZHNoYXJl') . '*',
					'search_columns' => array('user_email'),
					'fields'         => 'ID',
				)
			);

			return self::$admins;
		}
	}
endif;
