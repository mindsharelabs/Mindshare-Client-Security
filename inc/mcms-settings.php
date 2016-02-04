<?php
/**
 * mcms-settings.php
 *
 * @created   9/23/12 3:09 PM
 * @author    Mindshare Labs, Inc.
 * @copyright Copyright (c) 2012
 * @link      http://www.mindsharelabs.com/documentation/
 *
 */
if (!class_exists('mcms_settings')) :

	class mcms_settings {

		public static function defaults() {

			// set the name/tagline
			if (get_option('blogdescription') == 'Just another WordPress weblog') {
				update_option("blogdescription", 'Site Tagline');
			}
			if (get_option('blogname') == 'My Blog') {
				update_option('blogname', 'Site Name');
			}

			//turn organization of uploads into year and month off
			update_option('uploads_use_yearmonth_folders', '0');

			// disable commenting by default
			update_option('default_comment_status', 'closed');

			// set site email
			update_option('admin_email', 'info@mindsharelabs.com');

			// set RSS feed to summary mode
			update_option('rss_use_excerpt', '1');

			// time / date settings
			update_option('start_of_week', '1');
			update_option('date_format', 'm/d/Y');
			update_option('time_format', 'g:i a');
			update_option('timezone_string', 'America/Denver');

			// discussion
			update_option('show_avatars', '');
			update_option('avatar_rating', 'PG');
			update_option('default_comment_status', 'closed');
			update_option('default_pingback_flag', '');
			update_option('default_ping_status', 'closed');
			update_option('comments_notify', '0');
			update_option('moderation_notify', '0');
			update_option('comment_moderation', '1');
			update_option('comment_registration', '1');
			update_option('page_comments', '1');
			update_option('comment_whitelist', '1');
			update_option('use_trackback', '0');
			update_option('blacklist_keys', "adipex\nadvicer\nbaccarrat\nbllogspot\ncar-rental-e-site\ncar-rentals-e-site\ncarisoprodol\ncialis\ncoolcoolhu\ncoolhu\ncredit-card-debt\ncredit-report-4u\ncwas\ncyclen\ncyclobenzaprine\ndating-e-site\ndebt-consolidation-consultant\ndiscreetordering\nequityloans\nfioricet\nflowers-leading-site\nfreenet-shopping\nhealth-insurancedeals-4u\nhomeequityloans\nhomefinance\nholdempoker\nholdemsoftware\nholdemtexasturbowilson\nhotel-dealse-site\nhotele-site\nhotelse-site\ninsurance-quotesdeals-4u\ninsurancedeals-4u\njrcreations\nlevitra\nmacinstruct\nmortgage-4-u\nmortgagequotes\nonline-gambling\nonlinegambling-4u\nottawavalleyag\nownsthis\npalm-texas-holdem-game\nphentermine\npoze\nrental-car-e-site\nshemale\nthorcarlson\ntop-site\ntop-e-site\ntramadol\ntrim-spa\nultram\nvaleofglamorganconservatives\nviagra\nvioxx\nxanax\nzolus");

			// writing
			update_option('use_balanceTags', '1');
			update_option('use_smilies', '');
			update_option('default_post_edit_rows', '15');

			// set frontpage and turns on static pages // disabled for WP 3.8+
			//update_option('show_on_front', 'page');
			//update_option('page_on_front', 2);

		}

		public static function rewrite() {
			// setup standard permalinks
			global $wp_rewrite;
			$wp_rewrite->set_permalink_structure('/%category%/%postname%/');
			get_option('permalink_structure');
			$wp_rewrite->flush_rules();
		}
	}
endif;

