<?php
/**
 * mindshare-admin-options.php
 *
 * @created   12/19/13 11:22 AM
 * @author    Mindshare Studios, Inc.
 * @copyright Copyright (c) 2013
 * @link      http://www.mindsharelabs.com/documentation/
 *
 */
if(!class_exists('mindshare_admin_config')) :

	class mindshare_admin_config extends mindshare_admin_options {

		public $settings = array();
		public $setup;

		public function __construct() {

			$this->setup = array(
				'project_name' => MCMS_PLUGIN_NAME,
				'project_slug' => MCMS_PLUGIN_SLUG,
				'page_title'   => 'Mindshare Default Settings',
				'menu_title'   => 'Mindshare Defaults',
				'option_group' => 'mindshare_admin_options',
				'slug'         => MCMS_PLUGIN_SLUG.'-settings'
			);

			$this->create_settings();

			add_action('init', array($this, 'initialize'));
		}

		private function create_settings() {

			$this->settings['mcms_load_defaults'] = array(
				'title' => 'Load Mindshare default WordPress options?',
				'std'   => FALSE,
				'type'  => 'checkbox'
			);
			$this->settings['mcms_heading1'] = array(
				'title' => 'This sets the following WordPress settings:',
				'desc'  => '',
				'type'  => 'heading'
			);

			$this->settings['mcms_p1'] = array(
				'desc' => '
				  <strong>&bull;</strong> sets the name/tagline
			<br /><strong>&bull;</strong> turns off organization of uploads into year and month
			<br /><strong>&bull;</strong> disables commenting by default
			<br /><strong>&bull;</strong> deletes Hello Dolly, readme.html, license.txt
			<br /><strong>&bull;</strong> set site admin email to info@mindsharestudios.com
			<br /><strong>&bull;</strong> set RSS feeds to summary mode
			<br /><strong>&bull;</strong> sets time / date settings
			<br /><strong>&bull;</strong> sets avatar_rating to PG
			<br /><strong>&bull;</strong> clears default_pingback_flag
			<br /><strong>&bull;</strong> sets default_ping_status to closed
			<br /><strong>&bull;</strong> disabled comment emails
			<br /><strong>&bull;</strong> enables comment_moderation
			<br /><strong>&bull;</strong> enables comment_registration
			<br /><strong>&bull;</strong> enables comment_whitelist
			<br /><strong>&bull;</strong> disables trackbacks
			<br /><strong>&bull;</strong> populates blacklist words
			<br /><strong>&bull;</strong> enables HTML tag cleanup
			<br /><strong>&bull;</strong> disbales use_smilies
			<br /><strong>&bull;</strong> changes default_post_edit_rows to 15
			<br /><strong>&bull;</strong> sets frontpage to static page
			<br /><strong>&bull;</strong> sets permalinks to "/%category%/%postname%/"',
				'type'  => 'paragraph'
			);



			$this->settings['mcms_p2'] = array(
				'title' => 'Additional Notes',
				'desc' => 'This feature initializes WordPress with some default settings. It is meant to save a little time when setting up new WordPress installs <strong>ONLY</strong>. After turning this on and saving you will need to deactivate and then reactivate the '.MCMS_PLUGIN_NAME.' plugin.  After reactivation this setting will automatically return to OFF. This admin page is only visible for Mindshare Studios staff.',
				'type'  => 'paragraph'
			);


		}

		public function initialize() {
			parent::__construct($this->setup, $this->settings);
		}
	}
endif;

$mindshare_admin_options = new mindshare_admin_config();
