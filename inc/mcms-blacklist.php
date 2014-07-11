<?php
/**
 * mcms-blacklist.php
 *
 * Forked from Blacklist Auto Updater by Sergej Müller, https://github.com/sergejmueller/wp-blacklist-updater
 *
 * ui changes
 *
 * @created   7/11/14 3:11 PM
 * @author    Mindshare Studios, Inc.
 * @copyright Copyright (c) 2014
 * @link      http://www.mindsharelabs.com/documentation/
 *
 */

if(!class_exists('mcms_blacklist')) :

	class mcms_blacklist {

		public static function install() {
			add_site_option(
				'blacklist_keys__last_request',
				array(
					'time' => NULL,
					'etag' => NULL
				)
			);
		}

		public static function uninstall() {
			delete_site_option(
				'blacklist_keys__last_request'
			);
			delete_transient(
				'blacklist_keys__last_touch'
			);
		}

		public static function plugin_row_meta($input, $file) {
			if($file !== plugin_basename(MCMS_ADMIN_FILE)) {
				return $input;
			}

			$options = get_site_option(
				'blacklist_keys__last_request'
			);

			// Get update time
			if(!empty($options['time'])) {
				$updated = sprintf(
					'%s %s',
					human_time_diff(
						$options['time'],
						current_time('timestamp')
					),
					translate('ago', 'blacklist_auto_updater')
				);
			} else {
				$updated = translate('Never');
			}

			//Plugin rows
			return array_merge(
				$input,
				array(

					sprintf(
						'%s: %s',
						translate('Blacklist updated', 'blacklist_auto_updater'),
						$updated
					)
				)
			);
		}

		public static function plugins_loaded() {
			if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) OR (defined('DOING_AJAX') && DOING_AJAX) OR (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST)) {
				return;
			}

			if(!get_transient('blacklist_keys__last_touch')) {
				self::get_blacklist_from_github();
			}
		}

		public static function get_blacklist_from_github() {
			// Simulate cron
			set_transient(
				'blacklist_keys__last_touch',
				current_time('timestamp'),
				DAY_IN_SECONDS
			);

			// Plugin options
			$options = get_site_option(
				'blacklist_keys__last_request'
			);

			// Request header
			if(!empty($options['etag'])) {
				$args = array(
					'headers' => array(
						'If-None-Match' => $options['etag']
					)
				);
			} else {
				$args = array();
			}

			// Output debug infos
			if(defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
				error_log('Get blacklist');
			}

			// Start request
			$response = wp_remote_get(
				'https://raw.githubusercontent.com/splorp/wordpress-comment-blacklist/master/blacklist.txt',
				$args
			);

			// Exit on error
			if(!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
				if(defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
					error_log('Update blacklist');
				}

				update_option(
					'blacklist_keys',
					wp_remote_retrieve_body($response)
				);

				update_site_option(
					'blacklist_keys__last_request',
					array(
						'time' => current_time('timestamp'),
						'etag' => wp_remote_retrieve_header(
							$response,
							'etag'
						)
					)
				);
			}
		}
	}
endif;


