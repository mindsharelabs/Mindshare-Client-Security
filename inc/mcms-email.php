<?php
/**
 * mcms-email.php
 *
 * @created   7/11/14 10:00 AM
 * @author    Mindshare Labs, Inc.
 * @copyright Copyright (c) 2014
 * @link      http://www.mindsharelabs.com/documentation/
 *
 */

/**
 * Disable Admin notification of new user accounts.
 *
 */

if (!function_exists('wp_new_user_notification')) :

	/**
	 *
	 * @param        $user_id
	 * @param string $plaintext_pass
	 */
	function wp_new_user_notification($user_id, $plaintext_pass = '') {

		// Return early if no password is set
		if (empty($plaintext_pass)) {
			return;
		}

		$user = get_userdata($user_id);
		$user_login = stripslashes($user->user_login);
		$user_email = stripslashes($user->user_email);

		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		$message = sprintf(__('Username: %s'), $user_login) . "\r\n";
		$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
		$message .= wp_login_url() . "\r\n";

		wp_mail($user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);
	}
endif;
