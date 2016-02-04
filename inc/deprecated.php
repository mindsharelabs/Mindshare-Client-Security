<?php
/**
 * this file contain unused code from older versions, just a nice place to grow old and die.
 */

// @todo is this in the theme api? if so we can prob remove this
if (function_exists('register_uninstall_hook')) {
	// it should run after all the other hooks, so it gets a late priority.
	add_action('admin_menu', 'mcms_sort_dashboard_menu', 999999);
} else {
	// it should run before any CSS menu plugins, so it gets the unusual "-1" priority.
	add_action('dashmenu', 'mcms_sort_dashboard_menu', -1);
}
