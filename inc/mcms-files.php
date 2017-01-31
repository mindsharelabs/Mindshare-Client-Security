<?php
/**
 * mcms-files.php
 *
 * @created   9/23/12 3:11 PM
 * @author    Mindshare Labs, Inc.
 * @copyright Copyright (c) 2012-2016
 * @link      https://mindsharelabs.com/
 *
 */

if (!function_exists('get_home_path')) {
	include(ABSPATH . 'wp-admin/includes/file.php');
}
if (!function_exists('extract_from_markers')) {
	include(ABSPATH . 'wp-admin/includes/misc.php');
}
if (!function_exists('is_plugin_active')) {
	include(ABSPATH . 'wp-admin/includes/plugin.php');
}

if (!class_exists('mcms_files')) :

	/**
	 * Class mcms_files
	 *
	 * Creates, deletes and populates various files on plugin activation
	 */
	class mcms_files {

		/**
		 *  Check web server software.
		 *
		 * @return string|bool
		 */
		public static function web_server() {
			if (array_key_exists('SERVER_SOFTWARE', $_SERVER)) {
				$server = $_SERVER[ 'SERVER_SOFTWARE' ];
				if (stripos($server, 'nginx') !== FALSE) {
					$server = 'nginx';
				} elseif (stripos($server, 'apache') !== FALSE) {
					$server = 'apache';
				}
			}

			if (!empty($server)) {
				return $server;
			} else {
				return FALSE;
			}
		}

		/**
		 * Delete files
		 *
		 */
		public static function delete_files() {
			// remove readme.html
			$readme = ABSPATH . 'readme.html';
			if (file_exists($readme)) {
				unlink($readme);
			}
			$qstart = ABSPATH . 'quickstart.html';
			if (file_exists($qstart)) {
				unlink($qstart);
			}
			// remove license.txt
			$lic = ABSPATH . 'license.txt';
			if (file_exists($lic)) {
				unlink($lic);
			}
			// remove Thumbs.db
			$thumbs = ABSPATH . 'Thumbs.db';
			if (file_exists($thumbs)) {
				unlink($thumbs);
			}

			// remove PHP crash 'core' file
			$core = ABSPATH . 'core';
			if (file_exists($core)) {
				unlink($core);
			}

			// terminate Hello Dolly with extreme prejudice
			if (file_exists(WP_PLUGIN_DIR . '/hello.php')) {
				delete_plugins(array('hello.php'));
			}
		}

		/**
		 * Create default .htaccess
		 *
		 */
		public static function htaccess_defaults() {

			if (self::web_server() == 'apache') {

				$home_path = get_home_path();
				$home_root = parse_url(home_url());
				$https_status = is_ssl() ? 'http' : 'https';
				if (isset($home_root[ 'path' ])) {
					$home_root = trailingslashit($home_root[ 'path' ]);
				} else {
					$home_root = '/';
				}

				// check to see if our rules are already there so we don't overwrite any subsequent changes
				if (sizeof(extract_from_markers($home_path . '.htaccess', 'MINDSHARE')) === 0) {

					$rules = "# .htaccess created automatically by " . MCMS_PLUGIN_NAME . "\n";
					$rules .= "# https://mind.sh/are\n";
					$rules .= "\n";
					$rules .= "# Adjust PHP and Apache settings\n";
					$rules .= "ServerSignature Off\n";
					$rules .= "#php_value memory_limit 256M\n";
					$rules .= "#php_value max_execution_time 18000	\n";
					$rules .= "#LimitRequestBody 8192 \n";
					$rules .= "\n";
					$rules .= "# Declare index files\n";
					$rules .= "DirectoryIndex index.php index.html \n";
					$rules .= "Options -Indexes\n";
					$rules .= "\n";
					$rules .= "# Make server match local timezone\n";
					$rules .= "#SetEnv TZ 'US/Eastern'\n";
					$rules .= "#SetEnv TZ 'US/Central'\n";
					$rules .= "#SetEnv TZ 'US/Pacific'\n";
					$rules .= "#SetEnv TZ 'US/Mountain'\n";
					$rules .= "\n";
					$rules .= "<FilesMatch '\.(ttf|otf|woff)$'>\n";
					$rules .= "<IfModule mod_headers.c>\n";
					$rules .= "#Header set Access-Control-Allow-Origin: " . MCMS_DOMAIN_ROOT . "\n";
					$rules .= "Header set Access-Control-Allow-Origin: *\n";
					$rules .= "</IfModule>\n";
					$rules .= "</FilesMatch>\n";
					$rules .= "\n";
					$rules .= "# Security: deny viewing of .htaccess, wp-config.php, etc\n";
					$rules .= "<FilesMatch '\.(htaccess|htapsswd|wp-config|ini|phps|fla|psd|log|sh)$'>\n";
					$rules .= "order allow,deny\n";
					$rules .= "deny from all\n";
					$rules .= "</FilesMatch>\n";
					$rules .= "\n";
					$rules .= "# Security: Stop automated spam\n";
					$rules .= "#RewriteCond %{REQUEST_METHOD} POST\n";
					$rules .= "#RewriteCond %{REQUEST_URI} .wp-comments-post\.php*\n";
					$rules .= "#RewriteCond %{HTTP_REFERER} !.*" . MCMS_DOMAIN_ROOT . ".* [OR]\n";
					$rules .= "#RewriteCond %{HTTP_USER_AGENT} ^$\n";
					$rules .= "#RewriteRule (.*) ^" . $https_status . "://%{REMOTE_ADDR}/$ [R=301,L] \n";
					$rules .= "\n";
					$rules .= "# Security: Prevent hot linking\n";
					$rules .= "#RewriteBase $home_root\n";
					$rules .= "#RewriteCond %{HTTP_REFERER} !^$\n";
					$rules .= "#RewriteCond %{HTTP_REFERER} !^" . $https_status . "://(www\.)?" . MCMS_DOMAIN_ROOT . "/.*$ [NC]\n";
					$rules .= "#RewriteRule \.(gif|jpg|png|pdf|mp3|flv|swf)$ " . get_bloginfo('template_directory') . "/img/hotlinking.png [R=302,L]\n";
					$rules .= "\n";
					$rules .= "# Enable rewrite engine\n";
					$rules .= "<IfModule mod_rewrite.c>\n";
					$rules .= "RewriteEngine On\n";
					$rules .= "#Options +FollowSymlinks\n";
					$rules .= "\n";
					$rules .= "# Remove www\n";
					$rules .= "RewriteCond %{HTTPS} off\n";
					$rules .= "RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]\n";
					$rules .= "RewriteRule ^(.*)$ " . $https_status . "://%1/$1 [R=301,L]\n";
					$rules .= "\n";
					$rules .= "RewriteCond %{HTTPS} on\n";
					$rules .= "RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]\n";
					$rules .= "RewriteRule ^(.*)$ https://%1/$1 [R=301,L]\n";
					$rules .= "\n";
					$rules .= "# Add www\n";
					$rules .= "#RewriteCond %{HTTPS} off\n";
					$rules .= "#RewriteCond %{HTTP_HOST} !^www\.(.*)$ [NC]\n";
					$rules .= "#RewriteRule ^(.*)$ " . $https_status . "://www.%{HTTP_HOST}/$1 [R=301,L]\n";
					$rules .= "\n";
					$rules .= "#RewriteCond %{HTTPS} on\n";
					$rules .= "#RewriteCond %{HTTP_HOST} !^www\.(.*)$ [NC]\n";
					$rules .= "#RewriteRule ^(.*)$ https://www.%{HTTP_HOST}/$1 [R=301,L]\n";
					$rules .= "\n";
					$rules .= "# Force SSL\n";
					$rules .= "#RewriteCond %{SERVER_PORT} !^443$\n";
					$rules .= "#RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]\n";
					$rules .= "#RewriteBase /\n";
					$rules .= "</IfModule>\n";
					$rules .= "\n";
					$rules .= "# Add login/out redirects\n";
					$rules .= "#Redirect 301 " . $home_root . "login " . $https_status . "://" . MCMS_DOMAIN_ROOT . $home_root . "wp-login.php\n";
					$rules .= "#Redirect 301 " . $home_root . "logout " . $https_status . "://" . MCMS_DOMAIN_ROOT . $home_root . "wp-login.php?action=logout\n";
					$rules .= "\n";

					// GZIP rules start

					if (@$_SERVER[ 'SERVER_ADDR' ] == '172.97.102.35' || @$_SERVER[ 'SERVER_ADDR' ] == '66.33.194.144' /*|| @$_SERVER['SERVER_ADDR'] == '::1'*/) {
						// enabled by default
						$rules .= "# Performance: add default Expires header, http://developer.yahoo.com/performance/rules.html#expires\n";
						$rules .= "<IfModule mod_expires.c>\n";
						$rules .= "ExpiresActive on\n";
						$rules .= "ExpiresDefault 'access plus 1 month'\n";
						$rules .= "</IfModule>\n";
						$rules .= "\n";
						$rules .= "# Performance: remove inode from Etag configuration\n";
						$rules .= "FileETag MTime Size\n";
						$rules .= "\n";
						$rules .= "# Performance: add gzip compression, http://developer.yahoo.com/performance/rules.html#gzip\n";
						$rules .= "<IfModule mod_deflate.c>\n";
						$rules .= "# Insert filter on all content:\n";
						$rules .= "SetOutputFilter DEFLATE\n";
						$rules .= "# Insert filter on selected content types only:\n";
						$rules .= "AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript\n";
						$rules .= "# Netscape 4.x has some problems...\n";
						$rules .= "BrowserMatch ^Mozilla/4 gzip-only-text/html\n";
						$rules .= "# Netscape 4.06-4.08 have some more problems\n";
						$rules .= "BrowserMatch ^Mozilla/4\.0[678] no-gzip\n";
						$rules .= "# MSIE masquerades as Netscape, but it is fine\n";
						$rules .= "BrowserMatch \bMSIE !no-gzip !gzip-only-text/html\n";
						$rules .= "# Don't compress images\n";
						$rules .= "SetEnvIfNoCase Request_URI \.(?:gif|jpe?g|png)$ no-gzip dont-vary\n";
						$rules .= "# Make sure proxies don't deliver the wrong content\n";
						$rules .= "Header append Vary User-Agent env=!dont-vary\n";
						$rules .= "</IfModule>\n";
						$rules .= "\n";
						$rules .= "# Performance: uncomment if running in cluster environment, http://developer.yahoo.com/performance/rules.html#etags\n";
						$rules .= "FileETag none\n";
						$rules .= "\n";
					} else {
						// disabled by default
						$rules .= "# Performance: add default Expires header, http://developer.yahoo.com/performance/rules.html#expires\n";
						$rules .= "#<IfModule mod_expires.c>\n";
						$rules .= "#ExpiresActive on\n";
						$rules .= "#ExpiresDefault 'access plus 1 month'\n";
						$rules .= "#</IfModule>\n";
						$rules .= "\n";
						$rules .= "# Performance: remove inode from Etag configuration\n";
						$rules .= "#FileETag MTime Size\n";
						$rules .= "\n";
						$rules .= "# Performance: add gzip compression, http://developer.yahoo.com/performance/rules.html#gzip\n";
						$rules .= "#<IfModule mod_deflate.c>\n";
						$rules .= "# Insert filter on all content:\n";
						$rules .= "#SetOutputFilter DEFLATE\n";
						$rules .= "# Insert filter on selected content types only:\n";
						$rules .= "#AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript\n";
						$rules .= "# Netscape 4.x has some problems...\n";
						$rules .= "#BrowserMatch ^Mozilla/4 gzip-only-text/html\n";
						$rules .= "# Netscape 4.06-4.08 have some more problems\n";
						$rules .= "#BrowserMatch ^Mozilla/4\.0[678] no-gzip\n";
						$rules .= "# MSIE masquerades as Netscape, but it is fine\n";
						$rules .= "#BrowserMatch \bMSIE !no-gzip !gzip-only-text/html\n";
						$rules .= "# Don't compress images\n";
						$rules .= "#SetEnvIfNoCase Request_URI \.(?:gif|jpe?g|png)$ no-gzip dont-vary\n";
						$rules .= "# Make sure proxies don't deliver the wrong content\n";
						$rules .= "#Header append Vary User-Agent env=!dont-vary\n";
						$rules .= "#</IfModule>\n";
						$rules .= "\n";
						$rules .= "# Performance: uncomment if running in cluster environment, http://developer.yahoo.com/performance/rules.html#etags\n";
						$rules .= "#FileETag none\n";
						$rules .= "\n";
					}
					// GZIP rules end

					$rules .= "# Maintenance: take site offline except one IP\n";
					$rules .= "#RewriteBase $home_root\n";
					$rules .= "#RewriteCond %{REMOTE_ADDR} !^111\.111\.111\.111$\n";
					$rules .= "#RewriteCond %{REQUEST_URI} !^/security\.html$\n";
					$rules .= "#RewriteRule ^(.*)$ " . str_ireplace(array($https_status . '://' . MCMS_DOMAIN_ROOT, $https_status . '://' . MCMS_DOMAIN_ROOT), array(
							'',
							'',
						), get_bloginfo('template_directory')) . "/security.html [L]\n";

					insert_with_markers($home_path . '.htaccess', 'MINDSHARE', explode("\n", $rules));
				}
				// these rules get added every time the plugin is activated
				$force_rules = "Redirect 301 " . $home_root . "wp-admin/credits.php " . $https_status . "://" . MCMS_DOMAIN_ROOT . $home_root . "wp-admin/index.php\n";
				$force_rules .= "Redirect 301 " . $home_root . "wp-admin/about.php " . $https_status . "://" . MCMS_DOMAIN_ROOT . $home_root . "wp-admin/index.php\n";
				$force_rules .= "Redirect 301 " . $home_root . "wp-admin/freedoms.php " . $https_status . "://" . MCMS_DOMAIN_ROOT . $home_root . "wp-admin/index.php\n";
				$force_rules .= "# Prevent access to .git files and folders\n";
				$force_rules .= "RedirectMatch 404 /\.git\n";

				insert_with_markers($home_path . '.htaccess', 'MINDSHARE-FORCE', explode("\n", $force_rules));
			}
		}

		/**
		 * Create .htaccess for WP-DBManager
		 *
		 */
		public static function htaccess_defaults_backupdb() {
			if (is_plugin_active('wp-dbmanager/wp-dbmanager.php')) {
				if (self::web_server() == 'apache') {
					$home_path = get_home_path();
					// check to see if our rules are already there so we don't overwrite any subsequent changes
					if (sizeof(extract_from_markers($home_path . '/wp-content/backup-db/.htaccess', 'MINDSHARE')) == '0') {
						$rules = "# .htaccess created automatically by " . MCMS_PLUGIN_NAME . "\n";
						$rules .= "# https://mind.sh/are\n";
						$rules .= '<Files ~ ".*\..*">';
						$rules .= "\n";
						$rules .= "order allow,deny\n";
						$rules .= "deny from all\n";
						$rules .= "</Files>\n";
						insert_with_markers($home_path . '/wp-content/backup-db/.htaccess', 'MINDSHARE', explode("\n", $rules));
					}
				}
			}
		}

		/**
		 * Create robots.txt file
		 *
		 */
		public static function robots_defaults() {
			$https_status = is_ssl() ? 'https' : 'http';
			$robots_txt = ABSPATH . "robots.txt";
			if (!file_exists($robots_txt)) {
				$fh = fopen($robots_txt, 'w');
				$stringData = "
# robots.txt created automatically by " . MCMS_PLUGIN_NAME . "
# https://mind.sh/are/

Sitemap: " . $https_status . "://" . MCMS_DOMAIN_ROOT . "/sitemap.xml

User-agent: *

Disallow: /tmp/
Disallow: /feed/
Disallow: /trackback/
Disallow: /cgi-bin/
Disallow: /wp-content/uploads/
Disallow: /wp-content/themes/
Disallow: /wp-content/plugins/
";
				fwrite($fh, $stringData);
				fclose($fh);
			}
		}

		/**
		 * Create crossdomain.xml file for Flash
		 *
		 * @deprecated
		 */
		public static function crossdomain() {
			_deprecated_function(__FUNCTION__, '3.7.6');
			$crossdomainFile = ABSPATH . "crossdomain.xml";
			if (!file_exists($crossdomainFile)) {
				$fh = fopen($crossdomainFile, 'w');
				$stringData = "<?xml version=\"1.0\"?>
<!DOCTYPE cross-domain-policy SYSTEM \"http://www.macromedia.com/xml/dtds/cross-domain-policy.dtd\">
<cross-domain-policy>\n";
				$stringData .= "\t<allow-access-from domain=\"*." . MCMS_DOMAIN_ROOT . "\" />";
				$stringData .= "\n</cross-domain-policy>
<!-- crossdomain.xml created automatically by " . MCMS_PLUGIN_NAME . " -->
<!-- https://mind.sh/are/ -->";
				fwrite($fh, $stringData);
				fclose($fh);
			}
		}
	}
endif;
