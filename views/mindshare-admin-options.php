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
		public $setup = array(
			'project_name' => 'Plugin Name',
			'project_slug' => 'plugin-name',
			'page_title'   => 'Plugin Name Settings',
			'menu_title'   => 'Plugin Name',
			'option_group' => 'PLUGINNAME_options',
			'slug'         => 'plugin-name-settings'
		);

		public function __construct() {
			$this->create_settings();
			add_action('init', array($this, 'initialize'));
		}

		private function create_settings() {

			$this->settings['text_field_one'] = array(
				'title'   => __('Text field one'),
				'desc'    => __('Text field description.'),
				'std'     => 'Text field default',
				'type'    => 'text'
			);
		}

		public function initialize() {
			parent::__construct($this->setup, $this->settings);
		}

	}
endif;

$mindshare_admin_options = new mindshare_admin_config();
