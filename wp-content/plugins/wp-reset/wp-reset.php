<?php
/*
  Plugin Name: WP Reset
  Plugin URI: https://wpreset.com/
  Description: Reset the site to default installation values without modifying any files. Deletes all customizations and content.
  Version: 1.60
  Author: WebFactory Ltd
  Author URI: https://www.webfactoryltd.com/
  Text Domain: wp-reset

  Copyright 2015 - 2019  Web factory Ltd  (email: wpreset@webfactoryltd.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// include only file
if (!defined('ABSPATH')) {
  wp_die(__('Do not open this file directly.', 'wp-reset'));
}


// load WP-CLI commands, if needed
if (defined('WP_CLI') && WP_CLI) {
  require_once dirname(__FILE__) . '/wp-reset-cli.php';
}


class WP_Reset
{
  protected static $instance = null;
  public $version = 0;
  public $plugin_url = '';
  public $plugin_dir = '';
  public $snapshots_folder = 'wp-reset-snapshots-export';
  protected $options = array();
  private $delete_count = 0;
  private $licensing_servers = array('https://license1.wpreset.com/', 'https://license2.wpreset.com/');
  private $core_tables = array('commentmeta', 'comments', 'links', 'options', 'postmeta', 'posts', 'term_relationships', 'term_taxonomy', 'termmeta', 'terms', 'usermeta', 'users');


  /**
   * Creates a new WP_Reset object and implements singleton
   *
   * @return WP_Reset
   */
  static function getInstance()
  {
    if (!is_a(self::$instance, 'WP_Reset')) {
      self::$instance = new WP_Reset();
    }

    return self::$instance;
  } // getInstance


  /**
   * Initialize properties, hook to filters and actions
   *
   * @return null
   */
  private function __construct()
  {
    $this->version = $this->get_plugin_version();
    $this->plugin_dir = plugin_dir_path(__FILE__);
    $this->plugin_url = plugin_dir_url(__FILE__);
    $this->load_options();

    add_action('admin_menu', array($this, 'admin_menu'));
    add_action('admin_init', array($this, 'do_all_actions'));
    add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
    add_action('wp_ajax_wp_reset_dismiss_notice', array($this, 'ajax_dismiss_notice'));
    add_action('wp_ajax_wp_reset_run_tool', array($this, 'ajax_run_tool'));
    add_action('wp_ajax_wp_reset_submit_survey', array($this, 'ajax_submit_survey'));
    add_action('admin_action_install_webhooks', array($this, 'install_webhooks'));

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));
    add_filter('plugin_row_meta', array($this, 'plugin_meta_links'), 10, 2);
    add_filter('admin_footer_text', array($this, 'admin_footer_text'));
    add_filter('install_plugins_table_api_args_featured', array($this, 'featured_plugins_tab'));

    $this->core_tables = array_map(function ($tbl) {
      global $wpdb;
      return $wpdb->prefix . $tbl;
    }, $this->core_tables);
  } // __construct


  /**
   * Get plugin version from file header
   *
   * @return string
   */
  function get_plugin_version()
  {
    $plugin_data = get_file_data(__FILE__, array('version' => 'Version'), 'plugin');

    return $plugin_data['version'];
  } // get_plugin_version


  /**
   * Load and prepare the options array
   * If needed create a new DB entry
   *
   * @return array
   */
  private function load_options()
  {
    $options = get_option('wp-reset', array());
    $change = false;

    if (!isset($options['meta'])) {
      $options['meta'] = array('first_version' => $this->version, 'first_install' => current_time('timestamp', true), 'reset_count' => 0);
      $change = true;
    }
    if (!isset($options['dismissed_notices'])) {
      $options['dismissed_notices'] = array();
      $change = true;
    }
    if (!isset($options['last_run'])) {
      $options['last_run'] = array();
      $change = true;
    }
    if (!isset($options['options'])) {
      $options['options'] = array();
      $change = true;
    }
    if ($change) {
      update_option('wp-reset', $options, true);
    }

    $this->options = $options;
    return $options;
  } // load_options


  /**
   * Get meta part of plugin options
   *
   * @return array
   */
  function get_meta()
  {
    return $this->options['meta'];
  } // get_meta


  /**
   * Get all dismissed notices, or check for one specific notice
   *
   * @param string  $notice_name  Optional. Check if specified notice is dismissed.
   *
   * @return bool|array
   */
  function get_dismissed_notices($notice_name = '')
  {
    $notices = $this->options['dismissed_notices'];

    if (empty($notice_name)) {
      return $notices;
    } else {
      if (empty($notices[$notice_name])) {
        return false;
      } else {
        return true;
      }
    }
  } // get_dismissed_notices


  /**
   * Get options part of plugin options
   *
   * todo: not completed
   *
   * @param string  $key  Optional.
   *
   * @return array
   */
  function get_options($key = '')
  {
    return $this->options['options'];
  } // get_options


  /**
   * Update plugin options, currently entire array
   *
   * todo: this handles the entire options array although it should only do the options part - it's confusing
   *
   * @param string  $key   Data to save.
   * @param string  $data  Option key.
   *
   * @return bool
   */
  function update_options($key, $data)
  {
    $this->options[$key] = $data;
    $tmp = update_option('wp-reset', $this->options);

    return $tmp;
  } // set_options


  /**
   * Add plugin menu entry under Tools menu
   *
   * @return null
   */
  function admin_menu()
  {
    add_management_page(__('WP Reset', 'wp-reset'), __('WP Reset', 'wp-reset'), 'administrator', 'wp-reset', array($this, 'plugin_page'));
  } // admin_menu


  /**
   * Dismiss notice via AJAX call
   *
   * @return null
   */
  function ajax_dismiss_notice()
  {
    check_ajax_referer('wp-reset_dismiss_notice');

    if (!current_user_can('administrator')) {
      wp_send_json_error(__('You are not allowed to run this action.', 'wp-reset'));
    }

    $notice_name = trim(@$_GET['notice_name']);
    if (!$this->dismiss_notice($notice_name)) {
      wp_send_json_error(__('Notice is already dismissed.', 'wp-reset'));
    } else {
      wp_send_json_success();
    }
  } // ajax_dismiss_notice


  /**
   * Dismiss notice by adding it to dismissed_notices options array
   *
   * @param string  $notice_name  Notice to dismiss.
   *
   * @return bool
   */
  function dismiss_notice($notice_name)
  {
    if ($this->get_dismissed_notices($notice_name)) {
      return false;
    } else {
      $notices = $this->get_dismissed_notices();
      $notices[$notice_name] = true;
      $this->update_options('dismissed_notices', $notices);
      return true;
    }
  } // dismiss_notice


  /**
   * Returns all WP pointers
   *
   * @return array
   */
  function get_pointers()
  {
    $pointers = array();

    $pointers['welcome'] = array('target' => '#menu-tools', 'edge' => 'left', 'align' => 'right', 'content' => 'Thank you for installing the <b style="font-weight: 800;">WP Reset</b> plugin!<br>Open <a href="' . admin_url('tools.php?page=wp-reset') . '">Tools - WP Reset</a> to access resetting tools and start developing &amp; debugging faster.');

    return $pointers;
  } // get_pointers


  /**
   * Enqueue CSS and JS files
   *
   * @return null
   */
  function admin_enqueue_scripts($hook)
  {
    // welcome pointer is shown on all pages except WPR to admins, until dismissed
    $pointers = $this->get_pointers();
    $dismissed_notices = $this->get_dismissed_notices();
    $meta = $this->get_meta();

    foreach ($dismissed_notices as $notice_name => $tmp) {
      if ($tmp) {
        unset($pointers[$notice_name]);
      }
    } // foreach

    if (!empty($pointers) && !$this->is_plugin_page() && current_user_can('administrator')) {
      $pointers['_nonce_dismiss_pointer'] = wp_create_nonce('wp-reset_dismiss_notice');

      wp_enqueue_style('wp-pointer');

      wp_enqueue_script('wp-reset-pointers', $this->plugin_url . 'js/wp-reset-pointers.js', array('jquery'), $this->version, true);
      wp_enqueue_script('wp-pointer');
      wp_localize_script('wp-pointer', 'wp_reset_pointers', $pointers);
    }

    // exit early if not on WP Reset page
    if (!$this->is_plugin_page()) {
      return;
    }

    // features survey is shown 5min after install or after first reset
    $survey = false;
    if ($this->is_survey_active('features')) {
      $survey = true;
    }

    $js_localize = array(
      'undocumented_error' => __('An undocumented error has occurred. Please refresh the page and try again.', 'wp-reset'),
      'documented_error' => __('An error has occurred.', 'wp-reset'),
      'plugin_name' => __('WP Reset', 'wp-reset'),
      'settings_url' => admin_url('tools.php?page=wp-reset'),
      'icon_url' => $this->plugin_url . 'img/wp-reset-icon.png',
      'invalid_confirmation' => __('Please type "reset" in the confirmation field.', 'wp-reset'),
      'invalid_confirmation_title' => __('Invalid confirmation', 'wp-reset'),
      'cancel_button' => __('Cancel', 'wp-reset'),
      'open_survey' => $survey,
      'ok_button' => __('OK', 'wp-reset'),
      'confirm_button' => __('Reset WordPress', 'wp-reset'),
      'confirm_title' => __('Are you sure you want to proceed?', 'wp-reset'),
      'confirm1' => __('Clicking "Reset WordPress" will reset your site to default values. All content will be lost. There is NO UNDO.', 'wp-reset'),
      'confirm2' => __('Click "Cancel" to abort.', 'wp-reset'),
      'doing_reset' => __('Resetting in progress. Please wait.', 'wp-reset'),
      'nonce_dismiss_notice' => wp_create_nonce('wp-reset_dismiss_notice'),
      'nonce_run_tool' => wp_create_nonce('wp-reset_run_tool'),
      'nonce_do_reset' => wp_create_nonce('wp-reset_do_reset'),
    );

    if ($survey) {
      $js_localize['nonce_submit_survey'] = wp_create_nonce('wp-reset_submit_survey');
    }
    if (!$this->is_webhooks_active()) {
      $js_localize['webhooks_install_url'] = add_query_arg(array('action' => 'install_webhooks'), admin_url('admin.php'));
    }

    wp_enqueue_style('wp-jquery-ui-dialog');
    wp_enqueue_style('wp-reset', $this->plugin_url . 'css/wp-reset.css', array(), $this->version);
    wp_enqueue_style('wp-reset-sweetalert2', $this->plugin_url . 'css/sweetalert2.min.css', array(), $this->version);

    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('wp-reset-sweetalert2', $this->plugin_url . 'js/sweetalert2.min.js', array('jquery'), $this->version, true);
    wp_enqueue_script('wp-reset', $this->plugin_url . 'js/wp-reset.js', array('jquery'), $this->version, true);
    wp_localize_script('wp-reset', 'wp_reset', $js_localize);

    // fix for aggressive plugins that include their CSS on all pages
    wp_dequeue_style('uiStyleSheet');
    wp_dequeue_style('wpcufpnAdmin');
    wp_dequeue_style('unifStyleSheet');
    wp_dequeue_style('wpcufpn_codemirror');
    wp_dequeue_style('wpcufpn_codemirrorTheme');
    wp_dequeue_style('collapse-admin-css');
    wp_dequeue_style('jquery-ui-css');
    wp_dequeue_style('tribe-common-admin');
    wp_dequeue_style('file-manager__jquery-ui-css');
    wp_dequeue_style('file-manager__jquery-ui-css-theme');
    wp_dequeue_style('wpmegmaps-jqueryui');
    wp_dequeue_style('wp-botwatch-css');
  } // admin_enqueue_scripts


  /**
   * Submit user selected survey answers to WPR servers
   *
   * @return null
   */
  function ajax_submit_survey()
  {
    check_ajax_referer('wp-reset_submit_survey');

    $meta = $this->get_meta();

    $vars = wp_parse_args($_POST, array('survey' => '', 'answers' => '', 'custom_answer' => '', 'emailme' => ''));
    $vars['answers'] = trim($vars['answers'], ',');
    $vars['custom_answer'] = substr(trim(strip_tags($vars['custom_answer'])), 0, 256);

    if (empty($vars['survey']) || empty($vars['answers'])) {
      wp_send_json_error();
    }

    $request_params = array('sslverify' => false, 'timeout' => 15, 'redirection' => 2);
    $request_args = array(
      'action' => 'submit_survey',
      'survey' => $vars['survey'],
      'email' => $vars['emailme'],
      'answers' => $vars['answers'],
      'custom_answer' => $vars['custom_answer'],
      'first_version' => $meta['first_version'],
      'version' => $this->version,
      'codebase' => 'free',
      'site' => get_home_url()
    );

    $url = add_query_arg($request_args, $this->licensing_servers[0]);
    $response = wp_remote_get(esc_url_raw($url), $request_params);

    if (is_wp_error($response) || !wp_remote_retrieve_body($response)) {
      $url = add_query_arg($request_args, $this->licensing_servers[1]);
      $response = wp_remote_get(esc_url_raw($url), $request_params);
    }

    $this->dismiss_notice('survey-' . $vars['survey']);

    wp_send_json_success();
  } // ajax_submit_survey


  /**
   * Check if named survey should be shown or not
   *
   * @param [string] $survey_name Name of the survey to check
   * @return boolean
   */
  function is_survey_active($survey_name)
  {
    if (empty($survey_name)) {
      return false;
    }

    // all surveys are curently disabled
    return false;

    if ($this->get_dismissed_notices('survey-' . $survey_name)) {
      return false;
    }

    $meta = $this->get_meta();
    if (current_time('timestamp', true) - $meta['first_install'] > 300 || $meta['reset_count'] > 0) {
      return true;
    }

    return false;
  } // is_survey_active

  /**
   * Check if WP-CLI is available and running
   *
   * @return bool
   */
  static function is_cli_running()
  {
    if (!is_null($value = apply_filters('wp-reset-override-is-cli-running', null))) {
      return (bool)$value;
    }

    if (defined('WP_CLI') && WP_CLI) {
      return true;
    } else {
      return false;
    }
  } // is_cli_running


  /**
   * Check if core WP Webhooks and WPR addon plugins are installed and activated
   *
   * @return bool
   */
  function is_webhooks_active()
  {
    if (!function_exists('is_plugin_active') || !function_exists('get_plugin_data')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if (false == is_plugin_active('wp-webhooks/wp-webhooks.php')) {
      return false;
    }

    if (false == is_plugin_active('wpwh-wp-reset-webhook-integration/wpwhpro-wp-reset-webhook-integration.php')) {
      return false;
    }

    return true;
  } // is_webhooks_active


  /**
   * Check if given plugin is installed
   *
   * @param [string] $slug Plugin slug
   * @return boolean
   */
  function is_plugin_installed($slug)
  {
    if (!function_exists('get_plugins')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $all_plugins = get_plugins();

    if (!empty($all_plugins[$slug])) {
      return true;
    } else {
      return false;
    }
  } // is_plugin_installed


  /**
   * Auto download/install/upgrade/activate WP Webhooks plugin
   *
   * @return null
   */
  static function install_webhooks()
  {
    $plugin_slug = 'wp-webhooks/wp-webhooks.php';
    $plugin_zip = 'https://downloads.wordpress.org/plugin/wp-webhooks.latest-stable.zip';

    @include_once ABSPATH . 'wp-admin/includes/plugin.php';
    @include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    @include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    @include_once ABSPATH . 'wp-admin/includes/file.php';
    @include_once ABSPATH . 'wp-admin/includes/misc.php';
    echo '<style>
		body{
			font-family: sans-serif;
			font-size: 14px;
			line-height: 1.5;
			color: #444;
		}
		</style>';

    echo '<div style="margin: 20px; color:#444;">';
    echo 'If things are not done in a minute <a target="_parent" href="' . admin_url('plugin-install.php?s=ironikus&tab=search&type=term') . '">install the plugin manually via Plugins page</a><br><br>';

    wp_cache_flush();
    $upgrader = new Plugin_Upgrader();
    echo 'Check if WP Webhooks plugin is already installed ... <br />';
    if (self::is_plugin_installed($plugin_slug)) {
      echo 'WP Webhooks is already installed!<br />Making sure it\'s the latest version.<br />';
      $upgrader->upgrade($plugin_slug);
      $installed = true;
    } else {
      echo 'Installing WP Webhooks.<br />';
      $installed = $upgrader->install($plugin_zip);
    }
    wp_cache_flush();

    if (!is_wp_error($installed) && $installed) {
      echo 'Activating WP Webhooks.<br />';
      $activate = activate_plugin($plugin_slug);

      if (is_null($activate)) {
        echo 'WP Webhooks activated.<br />';
      }
    } else {
      echo 'Could not install WP Webhooks. You\'ll have to <a target="_parent" href="' . admin_url('plugin-install.php?s=ironikus&tab=search&type=term') . '">download and install manually</a>.';
    }

    $plugin_slug = 'wpwh-wp-reset-webhook-integration/wpwhpro-wp-reset-webhook-integration.php';
    $plugin_zip = 'https://downloads.wordpress.org/plugin/wpwh-wp-reset-webhook-integration.latest-stable.zip';

    wp_cache_flush();
    $upgrader = new Plugin_Upgrader();
    echo '<br>Check if WP Webhooks WPR addon plugin is already installed ... <br />';
    if (self::is_plugin_installed($plugin_slug)) {
      echo 'WP Webhooks WPR addon is already installed!<br />Making sure it\'s the latest version.<br />';
      $upgrader->upgrade($plugin_slug);
      $installed = true;
    } else {
      echo 'Installing WP Webhooks WPR addon.<br />';
      $installed = $upgrader->install($plugin_zip);
    }
    wp_cache_flush();

    if (!is_wp_error($installed) && $installed) {
      echo 'Activating WP Webhooks WPR addon.<br />';
      $activate = activate_plugin($plugin_slug);

      if (is_null($activate)) {
        echo 'WP Webhooks WPR addon activated.<br />';

        echo '<script>setTimeout(function() { top.location = "tools.php?page=wp-reset"; }, 1000);</script>';
        echo '<br>If you are not redirected in a few seconds - <a href="tools.php?page=wp-reset" target="_parent">click here</a>.';
      }
    } else {
      echo 'Could not install WP Webhooks WPR addon. You\'ll have to <a target="_parent" href="' . admin_url('plugin-install.php?s=ironikus&tab=search&type=term') . '">download and install manually</a>.';
    }

    echo '</div>';
  } // install_webhooks


  /**
   * Deletes all transients.
   *
   * @return int  Number of deleted transient DB entries
   */
  function do_delete_transients()
  {
    global $wpdb;

    $count = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '\_transient\_%' OR option_name LIKE '\_site\_transient\_%'");

    do_action('wp_reset_delete_transients', $count);

    return $count;
  } // do_delete_transients


  /**
   * Resets all theme options (mods).
   *
   * @param bool $all_themes Delete mods for all themes or just the current one
   *
   * @return int  Number of deleted mod DB entries
   */
  function do_reset_theme_options($all_themes = true)
  {
    global $wpdb;

    $count = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'theme_mods\_%' OR option_name LIKE 'mods\_%'");

    do_action('wp_reset_reset_theme_options', $count);

    return $count;
  } // do_reset_theme_options


  /**
   * Deletes all files in uploads folder.
   *
   * @return int  Number of deleted files and folders.
   */
  function do_delete_uploads()
  {
    $upload_dir = wp_get_upload_dir();
    $this->delete_count = 0;

    $this->delete_folder($upload_dir['basedir'], $upload_dir['basedir']);

    do_action('wp_reset_delete_uploads', $this->delete_count);

    return $this->delete_count;
  } // do_delete_uploads


  /**
   * Recursively deletes a folder
   *
   * @param string $folder  Recursive param.
   * @param string $base_folder  Base folder.
   *
   * @return bool
   */
  private function delete_folder($folder, $base_folder)
  {
    $files = array_diff(scandir($folder), array('.', '..'));

    foreach ($files as $file) {
      if (is_dir($folder . DIRECTORY_SEPARATOR . $file)) {
        $this->delete_folder($folder . DIRECTORY_SEPARATOR . $file, $base_folder);
      } else {
        $tmp = @unlink($folder . DIRECTORY_SEPARATOR . $file);
        $this->delete_count = $this->delete_count + (int)$tmp;
      }
    } // foreach

    if ($folder != $base_folder) {
      $tmp = @rmdir($folder);
      $this->delete_count = $this->delete_count + (int)$tmp;
      return $tmp;
    } else {
      return true;
    }
  } // delete_folder


  /**
   * Deactivate and delete all plugins
   *
   * @param bool  $keep_wp_reset  Keep WP Reset active and installed
   * @param bool  $silent_deactivate  Skip individual plugin deactivation functions when deactivating
   *
   * @return int  Number of deleted plugins.
   */
  function do_delete_plugins($keep_wp_reset = true, $silent_deactivate = false)
  {
    if (!function_exists('get_plugins')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    if (!function_exists('request_filesystem_credentials')) {
      require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    $wp_reset_basename = plugin_basename(__FILE__);

    $all_plugins = get_plugins();
    $active_plugins = (array)get_option('active_plugins', array());
    if (true == $keep_wp_reset) {
      if (($key = array_search($wp_reset_basename, $active_plugins)) !== false) {
        unset($active_plugins[$key]);
      }
      unset($all_plugins[$wp_reset_basename]);
    }

    if (!empty($active_plugins)) {
      deactivate_plugins($active_plugins, $silent_deactivate, false);
    }

    if (!empty($all_plugins)) {
      delete_plugins(array_keys($all_plugins));
    }

    do_action('wp_reset_delete_plugins', $all_plugins, $all_plugins);

    return sizeof($all_plugins);
  } // do_delete_plugins


  /**
   * Delete all themes
   *
   * @param bool  $keep_default_theme  Keep default theme
   *
   * @return int  Number of deleted themes.
   */
  function do_delete_themes($keep_default_theme = true)
  {
    global $wp_version;

    if (!function_exists('delete_theme')) {
      require_once ABSPATH . 'wp-admin/includes/theme.php';
    }

    if (!function_exists('request_filesystem_credentials')) {
      require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    if (version_compare($wp_version, '5.0', '<') === true) {
      $default_theme = 'twentyseventeen';
    } else {
      $default_theme = 'twentynineteen';
    }

    $all_themes = wp_get_themes(array('errors' => null));

    if (true == $keep_default_theme) {
      unset($all_themes[$default_theme]);
    }

    foreach ($all_themes as $theme_slug => $theme_details) {
      $res = delete_theme($theme_slug);
    }

    if (false == $keep_default_theme) {
      update_option('template', '');
      update_option('stylesheet', '');
      update_option('current_theme', '');
    }

    do_action('wp_reset_delete_themes', $all_themes);

    return sizeof($all_themes);
  } // do_delete_themes


  /**
   * Truncate custom tables
   *
   * @return int  Number of truncated tables.
   */
  function do_truncate_custom_tables()
  {
    global $wpdb;
    $custom_tables = $this->get_custom_tables();

    foreach ($custom_tables as $tbl) {
      $wpdb->query('TRUNCATE TABLE ' . $tbl['name']);
    } // foreach

    do_action('wp_reset_truncate_custom_tables', $custom_tables);

    return sizeof($custom_tables);
  } // do_truncate_custom_tables


  /**
   * Drop custom tables
   *
   * @return int  Number of dropped tables.
   */
  function do_drop_custom_tables()
  {
    global $wpdb;
    $custom_tables = $this->get_custom_tables();

    foreach ($custom_tables as $tbl) {
      $wpdb->query('DROP TABLE IF EXISTS ' . $tbl['name']);
    } // foreach

    do_action('wp_reset_drop_custom_tables', $custom_tables);

    return sizeof($custom_tables);
  } // do_drop_custom_tables


  /**
   * Delete .htaccess file
   *
   * @return bool|WP_Error Action status.
   */
  function do_delete_htaccess()
  {
    global $wp_filesystem;

    if (empty($wp_filesystem)) {
      require_once ABSPATH . '/wp-admin/includes/file.php';
      WP_Filesystem();
    }

    $htaccess_path = $this->get_htaccess_path();
    clearstatcache();

    do_action('wp_reset_delete_htaccess', $htaccess_path);

    if (!$wp_filesystem->is_readable($htaccess_path)) {
      return new WP_Error(1, 'Htaccess file does not exist; there\'s nothing to delete.');
    }

    if (!$wp_filesystem->is_writable($htaccess_path)) {
      return new WP_Error(1, 'Htaccess file is not writable.');
    }

    if ($wp_filesystem->delete($htaccess_path, false, 'f')) {
      return true;
    } else {
      return new WP_Error(1, 'Unknown error. Unable to delete htaccess file.');
    }
  } // do_delete_htaccess


  /**
   * Get .htaccess file path.
   *
   * @return string
   */
  function get_htaccess_path()
  {
    if (!function_exists('get_home_path')) {
      require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    if ($this->is_cli_running()) {
      $_SERVER['SCRIPT_FILENAME'] = ABSPATH;
    }

    $filepath = get_home_path() . '.htaccess';

    return $filepath;
  } // get_htaccess_path


  /**
   * Run one tool via AJAX call
   *
   * @return null
   */
  function ajax_run_tool()
  {
    check_ajax_referer('wp-reset_run_tool');

    if (!current_user_can('administrator')) {
      wp_send_json_error(__('You are not allowed to run this action.', 'wp-reset'));
    }

    $tool = trim(@$_GET['tool']);
    $extra_data = trim(@$_GET['extra_data']);

    if ($tool == 'delete_transients') {
      $cnt = $this->do_delete_transients();
      wp_send_json_success($cnt);
    } elseif ($tool == 'reset_theme_options') {
      $cnt = $this->do_reset_theme_options(true);
      wp_send_json_success($cnt);
    } elseif ($tool == 'delete_themes') {
      $cnt = $this->do_delete_themes(false);
      wp_send_json_success($cnt);
    } elseif ($tool == 'delete_plugins') {
      $cnt = $this->do_delete_plugins(true);
      wp_send_json_success($cnt);
    } elseif ($tool == 'delete_uploads') {
      $cnt = $this->do_delete_uploads();
      wp_send_json_success($cnt);
    } elseif ($tool == 'delete_htaccess') {
      $tmp = $this->do_delete_htaccess();
      if (is_wp_error($tmp)) {
        wp_send_json_error($tmp->get_error_message());
      } else {
        wp_send_json_success($tmp);
      }
    } elseif ($tool == 'drop_custom_tables') {
      $cnt = $this->do_drop_custom_tables();
      wp_send_json_success($cnt);
    } elseif ($tool == 'truncate_custom_tables') {
      $cnt = $this->do_truncate_custom_tables();
      wp_send_json_success($cnt);
    } elseif ($tool == 'delete_snapshot') {
      $res = $this->do_delete_snapshot($extra_data);
      if (is_wp_error($res)) {
        wp_send_json_error($res->get_error_message());
      } else {
        wp_send_json_success();
      }
    } elseif ($tool == 'download_snapshot') {
      $res = $this->do_export_snapshot($extra_data);
      if (is_wp_error($res)) {
        wp_send_json_error($res->get_error_message());
      } else {
        $url = content_url() . '/' . $this->snapshots_folder . '/' . $res;
        wp_send_json_success($url);
      }
    } elseif ($tool == 'restore_snapshot') {
      $res = $this->do_restore_snapshot($extra_data);
      if (is_wp_error($res)) {
        wp_send_json_error($res->get_error_message());
      } else {
        wp_send_json_success();
      }
    } elseif ($tool == 'compare_snapshots') {
      $res = $this->do_compare_snapshots($extra_data);
      if (is_wp_error($res)) {
        wp_send_json_error($res->get_error_message());
      } else {
        wp_send_json_success($res);
      }
    } elseif ($tool == 'create_snapshot') {
      $res = $this->do_create_snapshot($extra_data);
      if (is_wp_error($res)) {
        wp_send_json_error($res->get_error_message());
      } else {
        wp_send_json_success();
      }
    } else {
      wp_send_json_error(__('Unknown tool.', 'wp-reset'));
    }
  } // ajax_run_tool


  /**
   * Reinstall / reset the WP site
   * There are no failsafes in the function - it reinstalls when called
   * Redirects when done
   *
   * @param array  $params  Optional.
   *
   * @return null
   */
  function do_reinstall($params = array())
  {
    global $current_user, $wpdb;

    // only admins can reset; double-check
    if (!$this->is_cli_running() && !current_user_can('administrator')) {
      return false;
    }

    // make sure the function is available to us
    if (!function_exists('wp_install')) {
      require ABSPATH . '/wp-admin/includes/upgrade.php';
    }

    // save values that need to be restored after reset
    // todo: use params to determine what gets restored after reset
    $blogname = get_option('blogname');
    $blog_public = get_option('blog_public');
    $wplang = get_option('wplang');
    $siteurl = get_option('siteurl');
    $home = get_option('home');
    $snapshots = $this->get_snapshots();

    $active_plugins = get_option('active_plugins');
    $active_theme = wp_get_theme();

    if (!empty($params['reactivate_webhooks'])) {
      $wpwh1 = get_option('wpwhpro_active_webhooks');
      $wpwh2 = get_option('wpwhpro_activate_translations');
      $wpwh3 = get_option('ironikus_webhook_webhooks');
    }

    // for WP-CLI
    if (!$current_user->ID) {
      $tmp = get_users(array('role' => 'administrator', 'order' => 'ASC', 'order_by' => 'ID'));
      if (empty($tmp[0]->user_login)) {
        return new WP_Error(1, 'Reset failed. Unable to find any admin users in database.');
      }
      $current_user = $tmp[0];
    }

    // delete custom tables with WP's prefix
    $prefix = str_replace('_', '\_', $wpdb->prefix);
    $tables = $wpdb->get_col("SHOW TABLES LIKE '{$prefix}%'");
    foreach ($tables as $table) {
      $wpdb->query("DROP TABLE $table");
    }

    // supress errors for WP_CLI
    // todo: find a better way to supress errors and send/not send email on reset
    $result = @wp_install($blogname, $current_user->user_login, $current_user->user_email, $blog_public, '', md5(rand()), $wplang);
    $user_id = $result['user_id'];

    // restore user pass
    $query = $wpdb->prepare("UPDATE {$wpdb->users} SET user_pass = %s, user_activation_key = '' WHERE ID = %d LIMIT 1", array($current_user->user_pass, $user_id));
    $wpdb->query($query);

    // restore rest of the settings including WP Reset's
    update_option('siteurl', $siteurl);
    update_option('home', $home);
    update_option('wp-reset', $this->options);
    update_option('wp-reset-snapshots', $snapshots);

    // remove password nag
    if (get_user_meta($user_id, 'default_password_nag')) {
      update_user_meta($user_id, 'default_password_nag', false);
    }
    if (get_user_meta($user_id, $wpdb->prefix . 'default_password_nag')) {
      update_user_meta($user_id, $wpdb->prefix . 'default_password_nag', false);
    }

    $meta = $this->get_meta();
    $meta['reset_count']++;
    $this->update_options('meta', $meta);

    // reactivate theme
    if (!empty($params['reactivate_theme'])) {
      switch_theme($active_theme->get_stylesheet());
    }

    // reactivate WP Reset
    if (!empty($params['reactivate_wpreset'])) {
      activate_plugin(plugin_basename(__FILE__));
    }

    // reactivate WP Webhooks
    if (!empty($params['reactivate_webhooks'])) {
      activate_plugin('wp-webhooks/wp-webhooks.php');
      activate_plugin('wpwh-wp-reset-webhook-integration/wpwhpro-wp-reset-webhook-integration.php');

      update_option('wpwhpro_active_webhooks', $wpwh1);
      update_option('wpwhpro_activate_translations', $wpwh2);
      update_option('ironikus_webhook_webhooks', $wpwh3);
    }

    // reactivate all plugins
    if (!empty($params['reactivate_plugins'])) {
      foreach ($active_plugins as $plugin_file) {
        activate_plugin($plugin_file);
      }
    }

    if (!$this->is_cli_running()) {
      // log out and log in the old/new user
      // since the password doesn't change this is potentially unnecessary
      wp_clear_auth_cookie();
      wp_set_auth_cookie($user_id);

      wp_redirect(admin_url() . '?wp-reset=success');
      exit;
    }
  } // do_reinstall


  /**
   * Checks wp_reset post value and performs all actions
   * todo: handle messages for various actions
   *
   * @return null|bool
   */
  function do_all_actions()
  {
    // only admins can perform actions
    if (!current_user_can('administrator')) {
      return;
    }

    if (!empty($_GET['wp-reset']) && stristr($_SERVER['HTTP_REFERER'], 'wp-reset')) {
      add_action('admin_notices', array($this, 'notice_successful_reset'));
    }

    // check nonce
    if (true === isset($_POST['wp_reset_confirm']) && false === wp_verify_nonce(@$_POST['_wpnonce'], 'wp-reset')) {
      add_settings_error('wp-reset', 'bad-nonce', __('Something went wrong. Please refresh the page and try again.', 'wp-reset'), 'error');
      return false;
    }

    // check confirmation code
    if (true === isset($_POST['wp_reset_confirm']) && 'reset' !== $_POST['wp_reset_confirm']) {
      add_settings_error('wp-reset', 'bad-confirm', __('<b>Invalid confirmation code.</b> Please type "reset" in the confirmation field.', 'wp-reset'), 'error');
      return false;
    }

    // only one action at the moment
    if (true === isset($_POST['wp_reset_confirm']) && 'reset' === $_POST['wp_reset_confirm']) {
      $defaults = array(
        'reactivate_theme' => '0',
        'reactivate_plugins' => '0',
        'reactivate_wpreset' => '0',
        'reactivate_webhooks' => '0'
      );
      $params = shortcode_atts($defaults, (array)@$_POST['wpr-post-reset']);

      $this->do_reinstall($params);
    }
  } // do_all_actions


  /**
   * Add "Open WP Reset Tools" action link to plugins table, left part
   *
   * @param array  $links  Initial list of links.
   *
   * @return array
   */
  function plugin_action_links($links)
  {
    $settings_link = '<a href="' . admin_url('tools.php?page=wp-reset') . '" title="' . __('Open WP Reset Tools', 'wp-reset') . '">' . __('Open WP Reset Tools', 'wp-reset') . '</a>';

    array_unshift($links, $settings_link);

    return $links;
  } // plugin_action_links


  /**
   * Add links to plugin's description in plugins table
   *
   * @param array  $links  Initial list of links.
   * @param string $file   Basename of current plugin.
   *
   * @return array
   */
  function plugin_meta_links($links, $file)
  {
    if ($file !== plugin_basename(__FILE__)) {
      return $links;
    }

    $support_link = '<a target="_blank" href="https://wordpress.org/support/plugin/wp-reset" title="' . __('Get help', 'wp-reset') . '">' . __('Support', 'wp-reset') . '</a>';
    $home_link = '<a target="_blank" href="' . $this->generate_web_link('plugins-table-right') . '" title="' . __('Plugin Homepage', 'wp-reset') . '">' . __('Plugin Homepage', 'wp-reset') . '</a>';
    $rate_link = '<a target="_blank" href="https://wordpress.org/support/plugin/wp-reset/reviews/#new-post" title="' . __('Rate the plugin', 'wp-reset') . '">' . __('Rate the plugin ★★★★★', 'wp-reset') . '</a>';

    $links[] = $support_link;
    $links[] = $home_link;
    $links[] = $rate_link;

    return $links;
  } // plugin_meta_links


  /**
   * Test if we're on WPR's admin page
   *
   * @return bool
   */
  function is_plugin_page()
  {
    $current_screen = get_current_screen();

    if ($current_screen->id == 'tools_page_wp-reset') {
      return true;
    } else {
      return false;
    }
  } // is_plugin_page


  /**
   * Add powered by text in admin footer
   *
   * @param string  $text  Default footer text.
   *
   * @return string
   */
  function admin_footer_text($text)
  {
    if (!$this->is_plugin_page()) {
      return $text;
    }

    $text = '<i><a href="' . $this->generate_web_link('admin_footer') . '" title="' . __('Visit WP Reset page for more info', 'wp-reset') . '" target="_blank">WP Reset</a> v' . $this->version . ' by <a href="https://www.webfactoryltd.com/" title="' . __('Visit our site to get more great plugins', 'wp-reset') . '" target="_blank">WebFactory Ltd</a>. Please help us out by <a target="_blank" href="https://wordpress.org/support/plugin/wp-reset/reviews/#new-post" title="Rate the plugin">rating the plugin ★★★★★</a>.</i>';

    return $text;
  } // admin_footer_text


  /**
   * Loads plugin's translated strings
   *
   * @return null
   */
  function load_textdomain()
  {
    load_plugin_textdomain('wp-reset');
  } // load_textdomain


  /**
   * Inform the user that WordPress has been successfully reset
   *
   * @return null
   */
  function notice_successful_reset()
  {
    global $current_user;

    echo '<div id="message" class="updated fade"><p>' . sprintf(__('<b>Site has been reset</b> to default settings. User "%s" was restored with the password unchanged. Open <a href="%s">WP Reset</a> to do another reset.', 'wp-reset'), $current_user->user_login, admin_url('tools.php?page=wp-reset')) . '</p></div>';
  } // notice_successful_reset


  /**
   * Outputs complete plugin's admin page
   *
   * @return null
   */
  function plugin_page()
  {
    $notice_shown = false;
    $meta = $this->get_meta();
    $snapshots = $this->get_snapshots();

    // double check for admin privileges
    if (!current_user_can('administrator')) {
      wp_die(__('Sorry, you are not allowed to access this page.', 'wp-reset'));
    }

    settings_errors();
    echo '<div class="wrap">';
    echo '<h1><img id="logo-icon" src="' . $this->plugin_url . 'img/wp-reset-logo.png" title="' . __('WP Reset', 'wp-reset') . '" alt="' . __('WP Reset', 'wp-reset') . '"></h1>';
    echo '<form id="wp_reset_form" action="' . admin_url('tools.php?page=wp-reset') . '" method="post" autocomplete="off">';

    if (false === $notice_shown && is_multisite()) {
      echo '<div class="card notice-wrapper notice-error">';
      echo '<h2>' . __('WP Reset is not compatible with multisite!', 'wp-reset') . '</h2>';
      echo '<p>' . __('Please be careful when using WP Reset with multisite enabled. It\'s not recommended to reset the main site. Sub-sites should be OK. We\'re working on making it fully compatible with WP-MU. <b>Till then please be careful.</b> Thank you for understanding.', 'wp-reset') . '</p>';
      echo '</div>';
      $notice_shown = true;
    }

    // ask for review
    // disabled due to survey
    if (false && (!empty($meta['reset_count']) || !empty($snapshots)) && false === $notice_shown && false == $this->get_dismissed_notices('rate')) {
      echo '<div class="card notice-wrapper">';
      echo '<h2>' . __('Please help us keep the plugin free &amp; up-to-date', 'wp-reset') . '</h2>';
      echo '<p>' . __('If you use &amp; enjoy WP Reset, <b>please rate it on WordPress.org</b>. It only takes a second and helps us keep the plugin free and maintained. Thank you!', 'wp-reset') . '</p>';
      echo '<p><a class="button-primary button" title="' . __('Rate WP Reset', 'wp-reset') . '" target="_blank" href="https://wordpress.org/support/plugin/wp-reset/reviews/#new-post">' . __('Help keep the plugin free - rate it!', 'wp-reset') . '</a>  <a href="#" class="wpr-dismiss-notice dismiss-notice-rate" data-notice="rate">' . __('I\'ve already rated it', 'wp-reset') . '</a></p>';
      echo '</div>';
      $notice_shown = true;
    }

    // Tidy Repo ad
    // disabled for now
    if (false && false === $notice_shown && $meta['reset_count'] >= 2 && false == $this->get_dismissed_notices('tidy')) {
      echo '<div class="card notice-wrapper">';
      echo '<h2>' . __('Are you a plugin author? Get your plugin reviewed on Tidy Repo', 'wp-reset') . '</h2>';
      echo '<p>' . __('Since 2013 Tidy Repo has been reviewing the best and most reliable WordPress plugins. <b>Submitting a plugin is free</b>, so you have nothing to lose and a lot of exposure to gain when it gets reviewed.', 'wp-reset') . '</p>';
      echo '<p><a class="button-primary button" title="' . __('Rate WP Reset', 'wp-reset') . '" target="_blank" href="https://tidyrepo.com/?utm_source=wp-reset-free&utm_medium=plugin&utm_content=notification&utm_campaign=wp-reset-free-v' . $this->version . '">' . __('Let Tidy Repo know you have a great plugin', 'wp-reset') . '</a>  <a href="#" class="wpr-dismiss-notice dismiss-notice-rate" data-notice="tidy">' . __('Thanks, I\'m not interested', 'wp-reset') . '</a></p>';
      echo '</div>';
      $notice_shown = true;
    }

    // Product Hunt banner
    if (
      false
      && false == $this->get_dismissed_notices('product-hunt')
      && current_time('mysql', false) >= '2019-04-26'
      && current_time('mysql', false) <= '2019-05-03'
    ) {
      echo '<div class="card notice-wrapper product-hunt"><a title="Check out WP Reset on Product Hunt" href="' . $this->generate_web_link('ph-banner', '/product-hunt-redirect/') . '" target="_blank">';
      echo '<img src="' . $this->plugin_url . '/img/product-hunt.png" title="Check out WP Reset on Product Hunt" alt="Check out WP Reset on Product Hunt"> ';
      echo '<h2>' . __('We\'re on <b>Product Hunt</b> this week! Come check out your favorite dev tool &amp; show support ;) <i>Thanks!</i>', 'wp-reset') . '</h2>';
      echo '</a>';
      echo '<a href="#" title="Dismiss notice" class="wpr-dismiss-notice dismiss-notice" data-notice="product-hunt"><span class="dashicons dashicons-no"></span></a>';
      echo '</div>';
      $notice_shown = true;
    }

    // tabs
    echo '<div id="wp-reset-tabs" class' . __('="', 'wp-reset') . 'ui-tabs">';

    echo '<ul class="wpr-main-tab">';
    echo '<li><a href="#tab-reset">' . __('Reset', 'wp-reset') . '</a></li>';
    echo '<li><a href="#tab-tools">' . __('Tools', 'wp-reset') . '</a></li>';
    echo '<li><a href="#tab-snapshots">' . __('DB Snapshots', 'wp-reset') . '</a></li>';
    echo '<li><a href="#tab-collections">' . __('Collections', 'wp-reset') . '</a></li>';
    echo '<li><a href="#tab-support">' . __('Support', 'wp-reset') . '</a></li>';
    echo '</ul>';

    echo '<div style="display: none;" id="tab-reset">';
    $this->tab_reset();
    echo '</div>';

    echo '<div style="display: none;" id="tab-tools">';
    $this->tab_tools();
    echo '</div>';

    echo '<div style="display: none;" id="tab-snapshots">';
    $this->tab_snapshots();
    echo '</div>';

    echo '<div style="display: none;" id="tab-collections">';
    $this->tab_collections();
    echo '</div>';

    echo '<div style="display: none;" id="tab-support">';
    $this->tab_support();
    echo '</div>';

    echo '</div>'; // tabs

    echo '</form>';
    echo '</div>'; // wrap

    // survey
    if ($this->is_survey_active('features')) {
      echo '<div id="survey-dialog" style="display: none;" title="Help us make WP Reset better for you"><span class="ui-helper-hidden-accessible"><input type="text"/></span>';
      echo '<p class="subtitle"><b>What new features do you need the most?</b> Choose one or two;</p>';

      $questions = array();
      $questions[] = '<div class="question-wrapper" data-value="backup" title="Click to select/unselect answer">' .
        '<span class="dashicons dashicons-yes"></span>' .
        '<div class="question"><b>Off-site backups</b><br>' .
        '<i>Backup the site to Dropbox, FTP or Google Drive before running any tools</i></div>' .
        '</div>';

      $questions[] = '<div class="question-wrapper" data-value="wpmu" title="Click to select/unselect answer">' .
        '<span class="dashicons dashicons-yes"></span>' .
        '<div class="question"><b>WordPress Network (WPMU) compatibility</b><br>' .
        '<i>Full support &amp; compatibility for all WP Reset tools for all sites in network</i></div>' .
        '</div>';

      $questions[] = '<div class="question-wrapper" data-value="nothing" title="Click to select/unselect answer">' .
        '<span class="dashicons dashicons-yes"></span>' .
        '<div class="question"><b>Don\'t add anything</b><br>' .
        '<i>WP Reset is perfect as is - I don\'t need any new features</i></div>' .
        '</div>';

      $questions[] = '<div class="question-wrapper" data-value="nuclear" title="Click to select/unselect answer">' .
        '<span class="dashicons dashicons-yes"></span>' .
        '<div class="question"><b>Nuclear reset - run all tools at once</b><br>' .
        '<i>Besides resetting, delete all files and all other customizations with one click</i></div>' .
        '</div>';

      $questions[] = '<div class="question-wrapper" data-value="plugin-collections" title="Click to select/unselect answer">' .
        '<span class="dashicons dashicons-yes"></span>' .
        '<div class="question"><b>Install a set of plugins/themes after reset</b><br>' .
        '<i>Save lists of plugins/themes and automatically install them after resetting</i></div>' .
        '</div>';

      $questions[] = '<div class="question-wrapper" data-value="change-wp-ver" title="Click to select/unselect answer">' .
        '<span class="dashicons dashicons-yes"></span>' .
        '<div class="question"><b>Change WordPress version - rollback or upgrade</b><br>' .
        '<i>Pick a version of WP you need (older or never) and switch to it with one click</i></div>' .
        '</div>';

      shuffle($questions);
      $questions[] = '<div class="question-wrapper" data-value="custom" title="Click to select/unselect answer">' .
        '<span class="dashicons dashicons-yes"></span>' .
        '<div class="question"><b>Something we missed?</b><br><i>Enter the feature you need below;</i>' .
        '<input type="text" class="custom-input"></div>' .
        '</div>';

      echo implode(' ', $questions);

      $current_user = wp_get_current_user();
      echo '<div class="footer">';
      echo '<input id="emailme" type="checkbox" value="' . $current_user->user_email . '"> <label for="emailme">Email me on ' . $current_user->user_email . ' when new features are added. We hate SPAM and never send it.</label><br>';
      echo '<a data-survey="features" class="submit-survey button-primary button button-large" href="#">Add those features ASAP!</a>';
      echo '<a href="#" class="dismiss-survey wpr-dismiss-notice" data-notice="survey-features" data-survey="features"><i>Close the survey and never show it again</i></a>';
      echo '</div>';

      echo '</div>';
    } // survey

    if (!$this->is_webhooks_active()) {
      echo '<div id="webhooks-dialog" style="display: none;" title="Webhooks"><span class="ui-helper-hidden-accessible"><input type="text"/></span>';
      echo '<div style="padding: 20px; font-size: 15px;">';
      echo '<ul class="plain-list">';
      echo '<li>Standard, platform-independant way of connecting WP to any 3rd party system</li>';
      echo '<li>Supports actions - WP receives data on 3rd party events</li>';
      echo '<li>And triggers - WP sends data on its events</li>';
      echo '<li>Works wonders with Zapier</li>';
      echo '<li>Compatible with any WordPress theme or plugin</li>';
      echo '<li>Available from the official <a href="https://wordpress.org/plugins/wp-webhooks/" target="_blank">WP plugins repository</a></li>';
      echo '</ul>';
      echo '<p class="webhooks-footer"><a class="button button-primary" id="install-webhooks">Install WP Webhooks &amp; connect WP to any 3rd party system</a></p>';
      echo '</div>';
      echo '</div>';
    }
  } // plugin_page


  /**
   * Echoes content for reset tab
   *
   * @return null
   */
  private function tab_reset()
  {
    global $current_user, $wpdb;

    echo '<div class="card" id="card-description">';
    echo '<a class="toggle-card" href="#" title="' . __('Collapse / expand box', 'wp-reset') . '"><span class="dashicons dashicons-arrow-up-alt2"></span></a>';
    echo '<h2>' . __('Please read carefully before proceeding. There is NO UNDO!', 'wp-reset') . '</h2>';
    echo '<b class="red">' . __('Resetting will delete:', 'wp-reset') . '</b>';
    echo '<ul class="plain-list">';
    echo '<li>' . __('all posts, pages, custom post types, comments, media entries, users', 'wp-reset') . '</li>';
    echo '<li>' . __('all default WP database tables', 'wp-reset') . '</li>';
    echo '<li>' . sprintf(__('all custom database tables that have the same prefix "%s" as default tables in this installation', 'wp-reset'), $wpdb->prefix) . '</li>';
    echo '</ul>';

    echo '<b class="green">' . __('Resetting will not delete:', 'wp-reset') . '</b>';
    echo '<ul class="plain-list">';
    echo '<li>' . __('media files - they\'ll remain in the <i>wp-uploads</i> folder but will no longer be listed under Media', 'wp-reset') . '</li>';
    echo '<li>' . __('no files are touched; plugins, themes, uploads - everything stays', 'wp-reset') . '</li>';
    echo '<li>' . __('site title, WordPress address, site address, site language and search engine visibility settings', 'wp-reset') . '</li>';
    echo '<li>' . sprintf(__('logged in user "%s" will be restored with the current password', 'wp-reset'), $current_user->user_login) . '</li>';
    echo '</ul>';

    echo '<b>' . __('What happens when I click the Reset button?', 'wp-reset') . '</b>';
    echo '<ul class="plain-list">';
    echo '<li>' . __('you will have to confirm the action one more time because there is NO UNDO', 'wp-reset') . '</li>';
    echo '<li>' . __('everything will be reset; see bullets above for details', 'wp-reset') . '</li>';
    echo '<li>' . __('site title, WordPress address, site address, site language, search engine visibility and current user will be restored', 'wp-reset') . '</li>';
    echo '<li>' . __('you will be logged out, automatically logged in and taken to the admin dashboard', 'wp-reset') . '</li>';
    echo '<li>' . __('WP Reset plugin will be reactivated if that option is chosen in the <a href="#card-post-reset">post-reset options</a>', 'wp-reset') . '</li>';
    echo '</ul>';

    echo '<b>' . __('WP-CLI Support', 'wp-reset') . '</b>';
    echo '<p>' . sprintf(__('All tools available via GUI are available in WP-CLI as well. To get the list of commands run %s. Instead of the active user, the first user with admin privileges found in the database will be restored. ', 'wp-reset'), '<code>wp help reset</code>');
    echo sprintf(__('All actions have to be confirmed. If you want to skip confirmation use the standard %s option. Please be careful - there is NO UNDO.', 'wp-reset'), '<code>--yes</code>') . '</p>';

    echo '<b>' . __('WP Webhooks Support', 'wp-reset') . '</b>';
    echo '<p>All WP Reset tools are integrated with <a href="https://wordpress.org/plugins/wp-webhooks/" target="_blank">WP Webhooks</a> and available as (receive data) actions. Webhooks are a standard, platform-independent way of connecting WordPress to any 3rd party system. This <a href="https://underconstructionpage.com/wp-webhooks-connect-integrate-wordpress/" target="_blank">article</a> has more info, videos and use-cases so you can see just how powerful and easy to use webhooks are.<br>';
    if ($this->is_webhooks_active()) {
      echo 'WP Webhooks are active. Make sure you enable WP Reset actions in <a href="' . admin_url('options-general.php?page=wp-webhooks-pro&wpwhvrs=settings') . '">settings</a>.';
    } else {
      echo '<a href="#" class="open-webhooks-dialog">Install WP Webhooks &amp; WPR addon</a> to automate your workflow, develop faster and connect WordPress to any web app or 3rd party system.';
    }
    echo '</p></div>'; // card description

    $theme =  wp_get_theme();

    echo '<div class="card" id="card-post-reset">';
    echo '<a class="toggle-card" href="#" title="' . __('Collapse / expand box', 'wp-reset') . '"><span class="dashicons dashicons-arrow-up-alt2"></span></a>';
    echo '<h2>' . __('Post-reset actions', 'wp-reset') . '</h2>';
    echo '<p><label for="reactivate-theme"><input name="wpr-post-reset[reactivate_theme]" type="checkbox" id="reactivate-theme" value="1"> ' . __('Reactivate current theme', 'wp-reset') . ' - ' . $theme->get('Name') . '</label></p>';
    echo '<p><label for="reactivate-wpreset"><input name="wpr-post-reset[reactivate_wpreset]" type="checkbox" id="reactivate-wpreset" value="1" checked> ' . __('Reactivate WP Reset plugin', 'wp-reset') . '</label></p>';
    if ($this->is_webhooks_active()) {
      echo '<p><label for="reactivate-webhooks"><input name="wpr-post-reset[reactivate_webhooks]" type="checkbox" id="reactivate-webhooks" value="1" checked> ' . __('Reactivate WP Webhooks plugin', 'wp-reset') . '</label></p>';
    }
    echo '<p><label for="reactivate-plugins"><input name="wpr-post-reset[reactivate_plugins]" type="checkbox" id="reactivate-plugins" value="1"> ' . __('Reactivate all currently active plugins', 'wp-reset') . '</label></p>';
    if ($this->is_webhooks_active()) {
      echo '<p><a href="' . admin_url('options-general.php?page=wp-webhooks-pro&wpwhvrs=settings') . '">Configure WP Webhooks</a> to run additional actions after reset, or connect to any 3rd party system.</p>';
    } else {
      echo '<p>If you need to run additional actions after reset, or connect to any 3rd party system, <a href="#" class="open-webhooks-dialog">install WP Webhooks &amp; WPR addon</a>. It\'s a standard platform-independent way of connecting WordPress to any other web app. It automates complex workflows and saves time when developing. Have a look at this <a href="https://www.youtube.com/watch?v=m8XDFXCNP9g" target="_blank">short video</a> for a demonstration.</p>';
    }
    echo '</div>';

    echo '<div class="card">';
    echo '<h2>' . __('Reset', 'wp-reset') . '</h2>';
    echo '<p>' . __('Type <b>reset</b> in the confirmation field to confirm the reset and then click the "Reset WordPress" button. <b>There is NO UNDO. No backups are made by WP Reset.</b>', 'wp-reset') . '</p>';

    wp_nonce_field('wp-reset');
    echo '<p><input id="wp_reset_confirm" type="text" name="wp_reset_confirm" placeholder="' . esc_attr__('Type in "reset"', 'wp-reset') . '" value="" autocomplete="off"> &nbsp;';
    echo '<input id="wp_reset_submit" type="button" class="button-primary" value="' . __('Reset WordPress', 'wp-reset') . '"></p>';
    echo '</div>';
  } // tab_reset


  /**
   * Echoes content for tools tab
   *
   * @return null
   */
  private function tab_tools()
  {
    echo '<div class="card">';
    echo '<h2>' . __('Delete Transients', 'wp-reset') . '</h2>';
    echo '<p>' . __('All transient related database entries will be deleted. Including expired and non-expired transients, and orphaned transient timeout entries.<br><b>There is NO UNDO. WP Reset does not make any backups.</b>', 'wp-reset') . '</p>';
    echo '<p><a data-btn-confirm="Delete all transients" data-text-wait="Deleting transients. Please wait." data-text-confirm="All database entries related to transients will be deleted. There is NO UNDO. WP Reset will not make any backups." data-text-done="%n transient database entries have been deleted." data-text-done-singular="One transient database entry has been deleted." class="button button-delete" href="#" id="delete-transients">Delete all transients</a></p>';
    echo '</div>';

    $upload_dir = wp_upload_dir(date('Y/m'), true);
    $upload_dir['basedir'] = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $upload_dir['basedir']);

    echo '<div class="card">';
    echo '<h2>' . __('Clean Uploads Folder', 'wp-reset') . '</h2>';
    echo '<p>' . __('All files in <code>' . $upload_dir['basedir'] . '</code> folder will be deleted. Including folders and subfolders, and files in subfolders.  Files associated with <a href="' . admin_url('upload.php') . '">media</a> entries will be deleted too.<br><b>There is NO UNDO. WP Reset does not make any backups.</b>', 'wp-reset') . '</p>';
    if (false != $upload_dir['error']) {
      echo '<p><span style="color:#dd3036;"><b>Tool is not available.</b></span> Folder is not writeable by WordPress. Please check file and folder access rights.</p>';
    } else {
      echo '<p><a data-btn-confirm="Delete everything in uploads folder" data-text-wait="Deleting uploads. Please wait." data-text-confirm="All files and folders in uploads will be deleted. There is NO UNDO. WP Reset will not make any backups." data-text-done="%n files &amp; folders have been deleted." data-text-done-singular="One file or folder has been deleted." class="button button-delete" href="#" id="delete-uploads">Delete all files &amp; folders in uploads folder</a></p>';
    }
    echo '</div>';

    echo '<div class="card">';
    echo '<h2>' . __('Reset Theme Options', 'wp-reset') . '</h2>';
    echo '<p>' . __('All options (mods) for all themes will be reset; not just for the active theme. The tool works only for themes that use the <a href="https://codex.wordpress.org/Theme_Modification_API" target="_blank">WordPress theme modification API</a>. If options are saved in some other, custom way they won\'t be reset.<br><b>There is NO UNDO. WP Reset does not make any backups.</b>', 'wp-reset') . '</p>';
    echo '<p><a data-btn-confirm="Reset theme options" data-text-wait="Resetting theme options. Please wait." data-text-confirm="All options (mods) for all themes will be reset. There is NO UNDO. WP Reset will not make any backups." data-text-done="Options for %n themes have been reset." data-text-done-singular="Options for one theme have been reset." class="button button-delete" href="#" id="reset-theme-options">Reset theme options</a></p>';
    echo '</div>';

    $theme =  wp_get_theme();

    echo '<div class="card">';
    echo '<h2>' . __('Delete Themes', 'wp-reset') . '</h2>';
    echo '<p>' . __('All themes will be deleted. Including the currently active theme - ' . $theme->get('Name') . '.<br><b>There is NO UNDO. WP Reset does not make any backups.</b>', 'wp-reset') . '</p>';
    echo '<p><a data-btn-confirm="Delete all themes" data-text-wait="Deleting all themes. Please wait." data-text-confirm="All themes will be deleted. There is NO UNDO. WP Reset will not make any backups." data-text-done="%n themes have been deleted." data-text-done-singular="One theme has been deleted." class="button button-delete" href="#" id="delete-themes">Delete all themes</a></p>';
    echo '</div>';

    echo '<div class="card">';
    echo '<h2>' . __('Delete Plugins', 'wp-reset') . '</h2>';
    echo '<p>' . __('All plugins will be deleted except for WP Reset which will remain active.<br><b>There is NO UNDO. WP Reset does not make any backups.</b>', 'wp-reset') . '</p>';
    echo '<p><a data-btn-confirm="Delete plugins" data-text-wait="Deleting plugins. Please wait." data-text-confirm="All plugins except WP Reset will be deleted. There is NO UNDO. WP Reset will not make any backups." data-text-done="%n plugins have been deleted." data-text-done-singular="One plugin has been deleted." class="button button-delete" href="#" id="delete-plugins">Delete plugins</a></p>';
    echo '</div>';

    global $wpdb;
    $custom_tables = $this->get_custom_tables();

    echo '<div class="card">';
    echo '<h2>' . __('Empty or Delete Custom Tables', 'wp-reset') . '</h2>';
    echo '<p>' . __('This action affects only custom tables with <code>' . $wpdb->prefix . '</code> prefix. Core WP tables and other tables in the database that do not have that prefix will not be deleted/emptied. Deleting (dropping) tables completely removes them from the database. Emptying (truncating) removes all content from them, but keeps the structure intact.<br><b>There is NO UNDO. WP Reset does not make any backups.</b></p>', 'wp-reset');
    if ($custom_tables) {
      echo '<p>' . __('The following ' . sizeof($custom_tables) . ' custom tables are affected by this tool: ');
      foreach ($custom_tables as $tbl) {
        echo '<code>' . $tbl['name'] . '</code>';
        if (next($custom_tables)) {
          echo ', ';
        }
      } // foreach
      echo '.</p>';
      $custom_tables_btns = '';
    } else {
      echo '<p>' . __('There are no custom tables. There\'s nothing for this tool to empty or delete.', 'wp-reset') . '</p>';
      $custom_tables_btns = ' disabled';
    }
    echo '<p><a data-btn-confirm="Empty custom tables" data-text-wait="Emptying custom tables. Please wait." data-text-confirm="All custom tables with prefix <code>' . $wpdb->prefix . '</code> will be emptied. There is NO UNDO. WP Reset will not make any backups." data-text-done="%n custom tables have been emptied." data-text-done-singular="One custom table has been emptied." class="button button-delete' . $custom_tables_btns . '" href="#" id="truncate-custom-tables">Empty (truncate) custom tables</a>&nbsp; &nbsp;';
    echo '<a data-btn-confirm="Delete custom tables" data-text-wait="Deleting custom tables. Please wait." data-text-confirm="All custom tables with prefix <code>' . $wpdb->prefix . '</code> will be deleted. There is NO UNDO. WP Reset will not make any backups." data-text-done="%n custom tables have been deleted." data-text-done-singular="One custom table has been deleted." class="button button-delete' . $custom_tables_btns . '" href="#" id="drop-custom-tables">Delete (drop) custom tables</a></p>';

    echo '</div>';

    echo '<div class="card">';
    echo '<h2>' . __('Delete .htaccess File', 'wp-reset') . '</h2>';
    echo '<p>' . __('This action deletes the .htaccess file located in <code>' . $this->get_htaccess_path() . '</code><br><b>There is NO UNDO. WP Reset does not make any backups.</b></p>', 'wp-reset');

    echo '<p>If you need to edit .htaccess, install our free <a href="' . admin_url('plugin-install.php?s=htaccess+editor&tab=search&type=term') . '" target="_blank">WP Htaccess Editor</a> plugin. It automatically creates backups when you edit .htaccess. To create the default .htaccess file open <a href="' . admin_url('options-permalink.php') . '">Settings - Permalinks</a> and re-save settings. WordPress will recreate the file.</p>';

    echo '<a data-btn-confirm="Delete .htaccess file" data-text-wait="Deleting .htaccess file. Please wait." data-text-confirm="Htaccess file will be deleted. There is NO UNDO. WP Reset will not make any backups." data-text-done="Htaccess file has been deleted." class="button button-delete" href="#" id="delete-htaccess">Delete .htaccess file</a></p>';

    echo '</div>';
  } // tab_tools


  /**
   * Echoes content for collections tab
   *
   * @return null
   */
  private function tab_collections()
  {
    echo '<div class="card">';
    echo '<h2>' . __('What are Plugin &amp; Theme Collections', 'wp-reset') . '</h2>';
    echo '<p>' . __('Another tools that will save your precious time! Have a set of plugins and themes that you install and activate after every reset? Or on every fresh WP installation? Well, no more clicking install/active for five minutes! Build the collection once and install it with one click the next time you need it. Collections are stored in the cloud so they\'re accessible on every site.', 'wp-reset') . '</p>';
    echo '</div>';

    echo '<div class="card">';
    echo '<h2>' . __('So where do I click?!', 'wp-reset') . '</h2>';
    echo '<p>' . __('We are working hard on building collections as we speak and plan to have a beta version done by late May. Want to be the first to try the beta? Or tell us to code faster? :) Give us a shout <a href="https://twitter.com/webfactoryltd" target="_blank">@webfactoryltd</a>.', 'wp-reset') . '</p>';
    echo '</div>';
  } // tab_collections


  /**
   * Echoes content for support tab
   *
   * @return null
   */
  private function tab_support()
  {
    echo '<div class="card">';
    echo '<h2>' . __('Documentation', 'wp-reset') . '</h2>';
    echo '<p>' . __('All tools and functions are explained in detail in <a href="' . $this->generate_web_link('support-tab', '/documentation/') . '" target="_blank">the documentation</a>. We did our best to describe how things work on both the code level and a "average user" level.', 'wp-reset') . '</p>';
    echo '</div>';

    echo '<div class="card">';
    echo '<h2>' . __('Public support forum', 'wp-reset') . '</h2>';
    echo '<p>' . __('We are very active on the <a href="https://wordpress.org/support/plugin/wp-reset" target="_blank">official WP Reset support forum</a>. If you found a bug, have a feature idea or just want to say hi - please drop by. We love to hear back from our users.', 'wp-reset') . '</p>';
    echo '</div>';

    echo '<div class="card">';
    echo '<h2>' . __('Private contact', 'wp-reset') . '</h2>';
    echo '<p>' . __('If there\'s a need to contact us privately send emails to <a href="mailto:wpreset@webfactoryltd.com">wpreset@webfactoryltd.com</a>. Please know that although we\'ll gladly have a look at issues you are having with any site, we can\'t promise we\'ll fix them. Thank you for understanding.', 'wp-reset') . '</p>';
    echo '</div>';

    echo '<div class="card">';
    echo '<h2>' . __('Care to help out?', 'wp-reset') . '</h2>';
    echo '<p>' . __('No need for donations or anything like that :) If you can give us a <a href="https://wordpress.org/support/plugin/wp-reset/reviews/#new-post" target="_blank">five star rating</a> you\'ll help out more than you can imagine. A public mention <a href="https://twitter.com/webfactoryltd" target="_blank">@webfactoryltd</a> also does wonders. Thank you!', 'wp-reset') . '</p>';
    echo '</div>';
  } // tab_support


  /**
   * Echoes content for snapshots tab
   *
   * @return null
   */
  private function tab_snapshots()
  {
    global $wpdb;
    $tbl_core = $tbl_custom = $tbl_size = $tbl_rows = 0;

    echo '<div class="card" id="card-snapshots">';
    echo '<a class="toggle-card" href="#" title="' . __('Collapse / expand box', 'wp-reset') . '"><span class="dashicons dashicons-arrow-up-alt2"></span></a>';
    echo '<h2>' . __('Database Snapshots', 'wp-reset') . '</h2>';
    echo '<p>A snapshot is a copy of all WP database tables, standard and custom ones, saved in your database. Files are not saved or included in snapshots in any way.<br>
    Snapshots are primarily a development tool. Although they can be used for backups (and downloaded), we suggest finding a more suitable tool for live sites, such as <a href="https://wordpress.org/plugins/updraftplus/" target="_blank">UpdraftPlus</a>. Use snapshots to find out what changes a plugin made to your database or to quickly restore the dev environment after testing database related changes.<br>Restoring a snapshot does not affect other snapshots, or WP Reset settings.</p>';

    $table_status = $wpdb->get_results('SHOW TABLE STATUS');
    if (is_array($table_status)) {
      foreach ($table_status as $index => $table) {
        if (0 !== stripos($table->Name, $wpdb->prefix)) {
          continue;
        }
        if (empty($table->Engine)) {
          continue;
        }

        $tbl_rows += $table->Rows;
        $tbl_size += $table->Data_length + $table->Index_length;
        if (in_array($table->Name, $this->core_tables)) {
          $tbl_core++;
        } else {
          $tbl_custom++;
        }
      } // foreach

      echo '<p><b>Currently used WordPress tables</b>, prefixed with <i>' . $wpdb->prefix . '</i>, consist of ' . $tbl_core . ' standard and ';
      if ($tbl_custom) {
        echo $tbl_custom . ' custom table' . ($tbl_custom == 1 ? '' : 's');
      } else {
        echo 'no custom tables';
      }
      echo ' totaling ' . $this->format_size($tbl_size) . ' in ' . number_format($tbl_rows) . ' rows.</p>';
    }

    echo '';
    echo '</div>';

    echo '<div class="card no-padding-bottom">';
    echo '<a id="create-new-snapshot-primary" data-msg-success="Snapshot created!" data-msg-wait="Creating snapshot. Please wait." data-btn-confirm="Create snapshot" data-placeholder="Snapshot name or brief description, ie: before plugin install" data-text="Enter snapshot name or brief description, up to 64 characters." data-title="Create a new snapshot" title="Create a new database snapshot" href="#" class="button button-primary create-new-snapshot create-new-snapshot-corner">' . __('Create new', 'wp-reset') . '</a>';
    echo '<h2>' . __('Saved Snapshots', 'wp-reset') . '</h2>';

    if ($snapshots = $this->get_snapshots()) {
      echo '<table id="wpr-snapshots">';
      echo '<tr><th>Name</th><th>Info &amp; Size</th><th class="ss-actions">Actions</th></tr>';
      foreach ($snapshots as $ss) {
        echo '<tr id="wpr-ss-' . $ss['uid'] . '">';
        if (!empty($ss['name'])) {
          echo '<td title="Created on ' . date(get_option('date_format'), strtotime($ss['timestamp'])) . ' @ ' . date(get_option('time_format'), strtotime($ss['timestamp'])) . '">' . $ss['name'] . '</td>';
          $name = $ss['name'];
        } else {
          echo '<td title="Created on ' . date(get_option('date_format'), strtotime($ss['timestamp'])) . ' @ ' . date(get_option('time_format'), strtotime($ss['timestamp'])) . '">' . '' . date(get_option('date_format'), strtotime($ss['timestamp'])) . '<br>@ ' . date(get_option('time_format'), strtotime($ss['timestamp'])) . '</td>';
          $name = 'created on ' . date(get_option('date_format'), strtotime($ss['timestamp'])) . ' @ ' . date(get_option('time_format'), strtotime($ss['timestamp']));
        }
        echo '<td>' . $ss['tbl_core'] . ' standard &amp; ';
        if ($ss['tbl_custom']) {
          echo $ss['tbl_custom'] . ' custom table' . ($ss['tbl_custom'] == 1 ? '' : 's');
        } else {
          echo 'no custom tables';
        }
        echo ' totaling ' . $this->format_size($ss['tbl_size']) . ' in ' . number_format($ss['tbl_rows']) . ' rows</td>';
        echo '<td>';
        echo '<a data-title="Current DB tables compared to snapshot %s" data-wait-msg="Comparing. Please wait." data-name="' . $name . '" title="Compare snapshot to current database tables" href="#" class="ss-action compare-snapshot" data-ss-uid="' . $ss['uid'] . '"><span class="dashicons dashicons-visibility"></span></a>';
        echo '<a data-btn-confirm="Restore snapshot" data-text-wait="Restoring snapshot. Please wait." data-text-confirm="Are you sure you want to restore the selected snapshot? There is NO UNDO.<br>Restoring the snapshot will delete all current standard and custom tables and replace them with tables from the snapshot." data-text-done="Snapshot has been restored. Click OK to reload the page with new data." title="Restore snapshot by overwriting current database tables" href="#" class="ss-action restore-snapshot" data-ss-uid="' . $ss['uid'] . '"><span class="dashicons dashicons-backup"></span></a>';
        echo '<a data-success-msg="Snapshot export created!<br><a href=\'%s\'>Download it</a>" data-wait-msg="Exporting snapshot. Please wait." title="Download snapshot as gzipped SQL dump" href="#" class="ss-action download-snapshot" data-ss-uid="' . $ss['uid'] . '"><span class="dashicons dashicons-download"></span></a>';
        echo '<a data-btn-confirm="Delete snapshot" data-text-wait="Deleting snapshot. Please wait." data-text-confirm="Are you sure you want to delete the selected snapshot and all its data? There is NO UNDO.<br>Deleting the snapshot will not affect the active database tables in any way." data-text-done="Snapshot has been deleted." title="Permanently delete snapshot" href="#" class="ss-action delete-snapshot" data-ss-uid="' . $ss['uid'] . '"><span class="dashicons dashicons-trash"></span></a></td>';
        echo '</tr>';
      } // foreach
      echo '</table>';
      echo '<p id="ss-no-snapshots" class="hidden">There are no saved snapshots. <a href="#" class="create-new-snapshot">Create a new snapshot.</a></p>';
    } else {
      echo '<p id="ss-no-snapshots">There are no saved snapshots. <a href="#" class="create-new-snapshot">Create a new snapshot.</a></p>';
    }

    echo '</div>';
  } // tab_snapshots


  /**
   * Helper function for generating UTM tagged links
   *
   * @param string  $placement  Optional. UTM content param.
   * @param string  $page       Optional. Page to link to.
   * @param array   $params     Optional. Extra URL params.
   * @param string  $anchor     Optional. URL anchor part.
   *
   * @return string
   */
  function generate_web_link($placement = '', $page = '/', $params = array(), $anchor = '')
  {
    $base_url = 'https://wpreset.com';

    if ('/' != $page) {
      $page = '/' . trim($page, '/') . '/';
    }
    if ($page == '//') {
      $page = '/';
    }

    $parts = array_merge(array('utm_source' => 'wp-reset-free', 'utm_medium' => 'plugin', 'utm_content' => $placement, 'utm_campaign' => 'wp-reset-free-v' . $this->version), $params);

    if (!empty($anchor)) {
      $anchor = '#' . trim($anchor, '#');
    }

    $out = $base_url . $page . '?' . http_build_query($parts, '', '&amp;') . $anchor;

    return $out;
  } // generate_web_link


  /**
   * Returns all saved snapshots from DB
   *
   * @return array
   */
  function get_snapshots()
  {
    $snapshots = get_option('wp-reset-snapshots', array());

    return $snapshots;
  } // get_snapshots


  /**
   * Returns all custom table names, with prefix
   *
   * @return array
   */
  function get_custom_tables()
  {
    global $wpdb;
    $custom_tables = array();

    $table_status = $wpdb->get_results('SHOW TABLE STATUS');
    if (is_array($table_status)) {
      foreach ($table_status as $index => $table) {
        if (0 !== stripos($table->Name, $wpdb->prefix)) {
          continue;
        }
        if (empty($table->Engine)) {
          continue;
        }

        if (false === in_array($table->Name, $this->core_tables)) {
          $custom_tables[] = array('name' => $table->Name, 'rows' => $table->Rows, 'data_length' => $table->Data_length, 'index_length' => $table->Index_length);
        }
      } // foreach
    }

    return $custom_tables;
  } // get_custom tables


  /**
   * Format file size to human readable string
   *
   * @param int  $bytes  Size in bytes to format.
   *
   * @return string
   */
  function format_size($bytes)
  {
    if ($bytes > 1073741824) {
      return number_format_i18n($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes > 1048576) {
      return number_format_i18n($bytes / 1048576, 1) . ' MB';
    } elseif ($bytes > 1024) {
      return number_format_i18n($bytes / 1024, 1) . ' KB';
    } else {
      return number_format_i18n($bytes, 0) . ' bytes';
    }
  } // format_size


  /**
   * Creates snapshot of current tables by copying them in the DB and saving metadata.
   *
   * @param int  $name  Optional. Name for the new snapshot.
   *
   * @return array|WP_Error Snapshot details in array on success, or error object on fail.
   */
  function do_create_snapshot($name = '')
  {
    global $wpdb;
    $snapshots = $this->get_snapshots();
    $snapshot = array();
    $uid = $this->generate_snapshot_uid();
    $tbl_core = $tbl_custom = $tbl_size = $tbl_rows = 0;

    if (!$uid) {
      return new WP_Error(1, 'Unable to generate a valid snapshot UID.');
    }

    if ($name) {
      $snapshot['name'] = substr(trim($name), 0, 64);
    } else {
      $snapshot['name'] = '';
    }
    $snapshot['uid'] = $uid;
    $snapshot['timestamp'] = current_time('mysql');

    $table_status = $wpdb->get_results('SHOW TABLE STATUS');
    if (is_array($table_status)) {
      foreach ($table_status as $index => $table) {
        if (0 !== stripos($table->Name, $wpdb->prefix)) {
          continue;
        }
        if (empty($table->Engine)) {
          continue;
        }

        $tbl_rows += $table->Rows;
        $tbl_size += $table->Data_length + $table->Index_length;
        if (in_array($table->Name, $this->core_tables)) {
          $tbl_core++;
        } else {
          $tbl_custom++;
        }

        $wpdb->query('OPTIMIZE TABLE ' . $table->Name);
        $wpdb->query('CREATE TABLE ' . $uid . '_' . $table->Name . ' LIKE ' . $table->Name);
        $wpdb->query('INSERT ' . $uid . '_' . $table->Name . ' SELECT * FROM ' . $table->Name);
      } // foreach
    } else {
      return new WP_Error(1, 'Can\'t get table status data.');
    }

    $snapshot['tbl_core']   = $tbl_core;
    $snapshot['tbl_custom'] = $tbl_custom;
    $snapshot['tbl_rows']   = $tbl_rows;
    $snapshot['tbl_size']   = $tbl_size;


    $snapshots[$uid] = $snapshot;
    update_option('wp-reset-snapshots', $snapshots);

    do_action('wp_reset_create_snapshot', $uid, $snapshot);

    return $snapshot;
  } // create_snapshot


  /**
   * Delete snapshot metadata and tables from DB
   *
   * @param string  $uid  Snapshot unique 6-char ID.
   *
   * @return bool|WP_Error True on success, or error object on fail.
   */
  function do_delete_snapshot($uid = '')
  {
    global $wpdb;
    $snapshots = $this->get_snapshots();

    if (strlen($uid) != 6) {
      return new WP_Error(1, 'Invalid UID format.');
    }

    if (!isset($snapshots[$uid])) {
      return new WP_Error(1, 'Unknown snapshot ID.');
    }

    $tables = $wpdb->get_col($wpdb->prepare('SHOW TABLES LIKE %s', array($uid . '\_%')));
    foreach ($tables as $table) {
      $wpdb->query('DROP TABLE IF EXISTS ' . $table);
    }

    $snapshot_copy = $snapshots[$uid];
    unset($snapshots[$uid]);
    update_option('wp-reset-snapshots', $snapshots);

    do_action('wp_reset_delete_snapshot', $uid, $snapshot_copy);

    return true;
  } // delete_snapshot


  /**
   * Exports snapshot as SQL dump; saved in gzipped file in WP_CONTENT folder.
   *
   * @param string  $uid  Snapshot unique 6-char ID.
   *
   * @return string|WP_Error Export base filename, or error object on fail.
   */
  function do_export_snapshot($uid = '')
  {
    $snapshots = $this->get_snapshots();

    if (strlen($uid) != 6) {
      return new WP_Error(1, 'Invalid snapshot ID format.');
    }

    if (!isset($snapshots[$uid])) {
      return new WP_Error(1, 'Unknown snapshot ID.');
    }

    require_once $this->plugin_dir . 'libs/dumper.php';

    try {
      $world_dumper = Shuttle_Dumper::create(array(
        'host' =>     DB_HOST,
        'username' => DB_USER,
        'password' => DB_PASSWORD,
        'db_name' =>  DB_NAME,
      ));

      $folder = wp_mkdir_p(trailingslashit(WP_CONTENT_DIR) . $this->snapshots_folder);
      if (!$folder) {
        return new WP_Error(1, 'Unable to create wp-content/' . $this->snapshots_folder . '/ folder.');
      }

      $world_dumper->dump(trailingslashit(WP_CONTENT_DIR) . $this->snapshots_folder . '/wp-reset-snapshot-' . $uid . '.sql.gz', $uid . '_');
    } catch (Shuttle_Exception $e) {
      return new WP_Error(1, "Couldn't dump snapshot: " . $e->getMessage());
    }

    do_action('wp_reset_export_snapshot', 'wp-reset-snapshot-' . $uid . '.sql.gz');

    return 'wp-reset-snapshot-' . $uid . '.sql.gz';
  } // export_snapshot


  /**
   * Replace current tables with ones in snapshot.
   *
   * @param string  $uid  Snapshot unique 6-char ID.
   *
   * @return bool|WP_Error True on success, or error object on fail.
   */
  function do_restore_snapshot($uid = '')
  {
    global $wpdb;
    $new_tables = array();
    $snapshots = $this->get_snapshots();

    if (($res = $this->verify_snapshot_integrity($uid)) !== true) {
      return $res;
    }

    $table_status = $wpdb->get_results('SHOW TABLE STATUS');
    if (is_array($table_status)) {
      foreach ($table_status as $index => $table) {
        if (0 !== stripos($table->Name, $uid . '_')) {
          continue;
        }
        if (empty($table->Engine)) {
          continue;
        }

        $new_tables[] = $table->Name;
      } // foreach
    } else {
      return new WP_Error(1, 'Can\'t get table status data.');
    }

    foreach ($table_status as $index => $table) {
      if (0 !== stripos($table->Name, $wpdb->prefix)) {
        continue;
      }
      if (empty($table->Engine)) {
        continue;
      }

      $wpdb->query('DROP TABLE ' . $table->Name);
    } // foreach

    // copy snapshot tables to original name
    foreach ($new_tables as $table) {
      $new_name = str_replace($uid . '_', '', $table);

      $wpdb->query('CREATE TABLE ' . $new_name . ' LIKE ' . $table);
      $wpdb->query('INSERT ' . $new_name . ' SELECT * FROM ' . $table);
    }

    wp_cache_flush();
    update_option('wp-reset', $this->options);
    update_option('wp-reset-snapshots', $snapshots);

    do_action('wp_reset_restore_snapshot', $uid);

    return true;
  } // restore_snapshot


  /**
   * Verifies snapshot integrity by comparing metadata and data in DB
   *
   * @param string  $uid  Snapshot unique 6-char ID.
   *
   * @return bool|WP_Error True on success, or error object on fail.
   */
  function verify_snapshot_integrity($uid)
  {
    global $wpdb;
    $tbl_core = $tbl_custom = 0;
    $snapshots = $this->get_snapshots();

    if (strlen($uid) != 6) {
      return new WP_Error(1, 'Invalid snapshot ID format.');
    }

    if (!isset($snapshots[$uid])) {
      return new WP_Error(1, 'Unknown snapshot ID.');
    }

    $snapshot = $snapshots[$uid];

    $table_status = $wpdb->get_results('SHOW TABLE STATUS');
    if (is_array($table_status)) {
      foreach ($table_status as $index => $table) {
        if (0 !== stripos($table->Name, $uid . '_')) {
          continue;
        }
        if (empty($table->Engine)) {
          continue;
        }

        if (in_array(str_replace($uid . '_', '', $table->Name), $this->core_tables)) {
          $tbl_core++;
        } else {
          $tbl_custom++;
        }
      } // foreach

      if ($tbl_core != $snapshot['tbl_core'] || $tbl_custom != $snapshot['tbl_custom']) {
        return new WP_Error(1, 'Snapshot data has been compromised. Saved metadata does not match data in the DB. Contact WP Reset support if data is critical, or restore it via a MySQL GUI.');
      }
    } else {
      return new WP_Error(1, 'Can\'t get table status data.');
    }

    return true;
  } // verify_snapshot_integrity


  /**
   * Compares a selected snapshot with the current table set in DB
   *
   * @param string  $uid  Snapshot unique 6-char ID.
   *
   * @return string|WP_Error Formatted table with details on success, or error object on fail.
   */
  function do_compare_snapshots($uid)
  {
    global $wpdb;
    $current = $snapshot = array();
    $out = $out2 = $out3 = '';

    if (($res = $this->verify_snapshot_integrity($uid)) !== true) {
      return $res;
    }

    $table_status = $wpdb->get_results('SHOW TABLE STATUS');
    foreach ($table_status as $index => $table) {
      if (empty($table->Engine)) {
        continue;
      }

      if (0 !== stripos($table->Name, $uid . '_') && 0 !== stripos($table->Name, $wpdb->prefix)) {
        continue;
      }

      $info = array();
      $info['rows'] = $table->Rows;
      $info['size_data'] = $table->Data_length;
      $info['size_index'] = $table->Index_length;
      $schema = $wpdb->get_row('SHOW CREATE TABLE ' . $table->Name, ARRAY_N);
      $info['schema'] = $schema[1];
      $info['engine'] = $table->Engine;
      $info['fullname'] = $table->Name;
      $basename = str_replace(array($uid . '_'), array(''), $table->Name);
      $info['basename'] = $basename;
      $info['corename'] = str_replace(array($wpdb->prefix), array(''), $basename);
      $info['uid'] = $uid;

      if (0 === stripos($table->Name, $uid . '_')) {
        $snapshot[$basename] = $info;
      }

      if (0 === stripos($table->Name, $wpdb->prefix)) {
        $info['uid'] = '';
        $current[$basename] = $info;
      }
    } // foreach

    $in_both = array_keys(array_intersect_key($current, $snapshot));
    $in_current_only = array_diff_key($current, $snapshot);
    $in_snapshot_only = array_diff_key($snapshot, $current);

    $out .= '<br><br>';
    foreach ($in_current_only as $table) {
      $out .= '<div class="wpr-table-container in-current-only" data-table="' . $table['basename'] . '">';
      $out .= '<table>';
      $out .= '<tr title="Click to show/hide more info" class="wpr-table-missing header-row">';
      $out .= '<td><b>' . $table['fullname'] . '</b></td>';
      $out .= '<td>table is not present in snapshot<span class="dashicons dashicons-arrow-down-alt2"></span></td>';
      $out .= '</tr>';
      $out .= '<tr class="hidden">';
      $out .= '<td>';
      $out .= '<p>' . number_format($table['rows']) . ' row' . ($table['rows'] == 1 ? '' : 's') . ' totaling ' . $this->format_size($table['size_data']) . ' in data and ' . $this->format_size($table['size_index']) . ' in index.</p>';
      $out .= '<pre>' . $table['schema'] . '</pre>';
      $out .= '</td>';
      $out .= '<td>&nbsp;</td>';
      $out .= '</tr>';
      $out .= '</table>';
      $out .= '</div>';
    } // foreach in current only

    foreach ($in_snapshot_only as $table) {
      $out .= '<div class="wpr-table-container in-snapshot-only" data-table="' . $table['basename'] . '">';
      $out .= '<table>';
      $out .= '<tr title="Click to show/hide more info" class="wpr-table-missing header-row">';
      $out .= '<td>table is not present in current tables</td>';
      $out .= '<td><b>' . $table['fullname'] . '</b><span class="dashicons dashicons-arrow-down-alt2"></span></td>';
      $out .= '</tr>';
      $out .= '<tr class="hidden">';
      $out .= '<td>&nbsp;</td>';
      $out .= '<td>';
      $out .= '<p>' . number_format($table['rows']) . ' row' . ($table['rows'] == 1 ? '' : 's') . ' totaling ' . $this->format_size($table['size_data']) . ' in data and ' . $this->format_size($table['size_index']) . ' in index.</p>';
      $out .= '<pre>' . $table['schema'] . '</pre>';
      $out .= '</td>';
      $out .= '</tr>';
      $out .= '</table>';
      $out .= '</div>';
    } // foreach in snapshot only

    foreach ($in_both as $tablename) {
      $tbl_current = $current[$tablename];
      $tbl_snapshot = $snapshot[$tablename];

      $schema1 = preg_replace('/(auto_increment=)([0-9]*) /i', '${1}1 ', $tbl_current['schema'], 1);
      $schema2 = preg_replace('/(auto_increment=)([0-9]*) /i', '${1}1 ', $tbl_snapshot['schema'], 1);
      $tbl_snapshot['tmp_schema'] = str_replace($tbl_snapshot['uid'] . '_' . $tablename, $tablename, $tbl_snapshot['schema']);
      $schema2 = str_replace($tbl_snapshot['uid'] . '_' . $tablename, $tablename, $schema2);

      if ($tbl_current['rows'] == $tbl_snapshot['rows'] && $tbl_current['schema'] == $tbl_snapshot['tmp_schema']) {
        $out3 .= '<div class="wpr-table-container identical" data-table="' . $tablename . '">';
        $out3 .= '<table>';
        $out3 .= '<tr title="Click to show/hide more info" class="wpr-table-match header-row">';
        $out3 .= '<td><b>' . $tbl_current['fullname'] . '</b></td>';
        $out3 .= '<td><b>' . $tbl_snapshot['fullname'] . '</b><span class="dashicons dashicons-arrow-down-alt2"></span></td>';
        $out3 .= '</tr>';
        $out3 .= '<tr class="hidden">';
        $out3 .= '<td>';
        $out3 .= '<p>' . number_format($tbl_current['rows']) . ' rows totaling ' . $this->format_size($tbl_current['size_data']) . ' in data and ' . $this->format_size($tbl_current['size_index']) . ' in index.</p>';
        $out3 .= '<pre>' . $tbl_current['schema'] . '</pre>';
        $out3 .= '</td>';
        $out3 .= '<td>';
        $out3 .= '<p>' . number_format($tbl_snapshot['rows']) . ' rows totaling ' . $this->format_size($tbl_snapshot['size_data']) . ' in data and ' . $this->format_size($tbl_snapshot['size_index']) . ' in index.</p>';
        $out3 .= '<pre>' . $tbl_snapshot['schema'] . '</pre>';
        $out3 .= '</td>';
        $out3 .= '</tr>';
        $out3 .= '</table>';
        $out3 .= '</div>';
      } elseif ($schema1 != $schema2) {
        require_once $this->plugin_dir . 'libs/diff.php';
        require_once $this->plugin_dir . 'libs/diff/Renderer/Html/SideBySide.php';
        $diff = new Diff(explode("\n", $tbl_current['schema']), explode("\n", $tbl_snapshot['schema']), array('ignoreWhitespace' => false));
        $renderer = new Diff_Renderer_Html_SideBySide;

        $out2 .= '<div class="wpr-table-container" data-table="' . $tbl_current['basename'] . '">';
        $out2 .= '<table>';
        $out2 .= '<tr title="Click to show/hide more info" class="wpr-table-difference header-row">';
        $out2 .= '<td><b>' . $tbl_current['fullname'] . '</b> table schemas do not match</td>';
        $out2 .= '<td><b>' . $tbl_snapshot['fullname'] . '</b> table schemas do not match<span class="dashicons dashicons-arrow-down-alt2"></span></td>';
        $out2 .= '</tr>';
        $out2 .= '<tr class="hidden">';
        $out2 .= '<td>';
        $out2 .= '<p>' . number_format($tbl_current['rows']) . ' rows totaling ' . $this->format_size($tbl_current['size_data']) . ' in data and ' . $this->format_size($tbl_current['size_index']) . ' in index.</p>';
        $out2 .= '</td>';
        $out2 .= '<td>';
        $out2 .= '<p>' . number_format($tbl_snapshot['rows']) . ' rows totaling ' . $this->format_size($tbl_snapshot['size_data']) . ' in data and ' . $this->format_size($tbl_snapshot['size_index']) . ' in index.</p>';
        $out2 .= '</td>';
        $out2 .= '</tr>';
        $out2 .= '<tr class="hidden">';
        $out2 .= '<td colspan="2" class="no-padding">';
        $out2 .= $diff->Render($renderer);
        $out2 .= '</td>';
        $out2 .= '</tr>';
        $out2 .= '</table>';
        $out2 .= '</div>';
      } else {
        $out2 .= '<div class="wpr-table-container" data-table="' . $tbl_current['basename'] . '">';
        $out2 .= '<table>';
        $out2 .= '<tr title="Click to show/hide more info" class="wpr-table-difference header-row">';
        $out2 .= '<td><b>' . $tbl_current['fullname'] . '</b> data in tables does not match</td>';
        $out2 .= '<td><b>' . $tbl_snapshot['fullname'] . '</b> data in tables does not match<span class="dashicons dashicons-arrow-down-alt2"></span></td>';
        $out2 .= '</tr>';
        $out2 .= '<tr class="hidden">';
        $out2 .= '<td>';
        $out2 .= '<p>' . number_format($tbl_current['rows']) . ' rows totaling ' . $this->format_size($tbl_current['size_data']) . ' in data and ' . $this->format_size($tbl_current['size_index']) . ' in index.</p>';
        $out2 .= '</td>';
        $out2 .= '<td>';
        $out2 .= '<p>' . number_format($tbl_snapshot['rows']) . ' rows totaling ' . $this->format_size($tbl_snapshot['size_data']) . ' in data and ' . $this->format_size($tbl_snapshot['size_index']) . ' in index.</p>';
        $out2 .= '</td>';
        $out2 .= '</tr>';

        $out2 .= '<tr class="hidden">';
        $out2 .= '<td colspan="2">';
        if ($tbl_current['corename'] == 'options') {
          $ss_prefix = $tbl_snapshot['uid'] . '_' . $wpdb->prefix;
          $diff_rows = $wpdb->get_results("SELECT {$wpdb->prefix}options.option_name, {$wpdb->prefix}options.option_value AS current_value, {$ss_prefix}options.option_value AS snapshot_value FROM {$wpdb->prefix}options LEFT JOIN {$ss_prefix}options ON {$ss_prefix}options.option_name = {$wpdb->prefix}options.option_name WHERE {$wpdb->prefix}options.option_value != {$ss_prefix}options.option_value LIMIT 100;");
          $only_current = $wpdb->get_results("SELECT {$wpdb->prefix}options.option_name, {$wpdb->prefix}options.option_value AS current_value, {$ss_prefix}options.option_value AS snapshot_value FROM {$wpdb->prefix}options LEFT JOIN {$ss_prefix}options ON {$ss_prefix}options.option_name = {$wpdb->prefix}options.option_name WHERE {$ss_prefix}options.option_value IS NULL LIMIT 100;");
          $only_snapshot = $wpdb->get_results("SELECT {$wpdb->prefix}options.option_name, {$wpdb->prefix}options.option_value AS current_value, {$ss_prefix}options.option_value AS snapshot_value FROM {$wpdb->prefix}options LEFT JOIN {$ss_prefix}options ON {$ss_prefix}options.option_name = {$wpdb->prefix}options.option_name WHERE {$wpdb->prefix}options.option_value IS NULL LIMIT 100;");
          $out2 .= '<table class="table_diff">';
          $out2 .= '<tr><td style="width: 100px;"><b>Option Name</b></td><td><b>Current Value</b></td><td><b>Snapshot Value</b></td></tr>';
          foreach ($diff_rows as $row) {
            $out2 .= '<tr>';
            $out2 .= '<td style="width: 100px;">' . $row->option_name . '</td>';
            $out2 .= '<td>' . (empty($row->current_value) ? '<i>empty</i>' : $row->current_value) . '</td>';
            $out2 .= '<td>' . (empty($row->snapshot_value) ? '<i>empty</i>' : $row->snapshot_value) . '</td>';
            $out2 .= '</tr>';
          } // foreach
          foreach ($only_current as $row) {
            $out2 .= '<tr>';
            $out2 .= '<td style="width: 100px;">' . $row->option_name . '</td>';
            $out2 .= '<td>' . (empty($row->current_value) ? '<i>empty</i>' : $row->current_value) . '</td>';
            $out2 .= '<td><i>not found in snapshot</i></td>';
            $out2 .= '</tr>';
          } // foreach
          foreach ($only_current as $row) {
            $out2 .= '<tr>';
            $out2 .= '<td style="width: 100px;">' . $row->option_name . '</td>';
            $out2 .= '<td><i>not found in current tables</i></td>';
            $out2 .= '<td>' . (empty($row->snapshot_value) ? '<i>empty</i>' : $row->snapshot_value) . '</td>';
            $out2 .= '</tr>';
          } // foreach
          $out2 .= '</table>';
        } else {
          $out2 .= '<p class="textcenter">Detailed data diff is not available for this table.</p>';
        }
        $out2 .= '</td>';
        $out2 .= '</tr>';

        $out2 .= '</table>';
        $out2 .= '</div>';
      }
    } // foreach in both

    return $out . $out2 . $out3;
  } // do_compare_snapshots


  /**
   * Generates a unique 6-char snapshot ID; verified non-existing
   *
   * @return string
   */
  function generate_snapshot_uid()
  {
    global $wpdb;
    $snapshots = $this->get_snapshots();
    $cnt = 0;
    $uid = false;

    do {
      $cnt++;
      $uid = sprintf('%06x', mt_rand(0, 0xFFFFFF));

      $verify_db = $wpdb->get_col($wpdb->prepare('SHOW TABLES LIKE %s', array('%' . $uid . '%')));
    } while (!empty($verify_db) && isset($snapshots[$uid]) && $cnt < 30);

    if ($cnt == 30) {
      $uid = false;
    }

    return $uid;
  } // generate_snapshot_uid


  /**
   * Helper function for adding plugins to featured list
   *
   * @return array
   */
  function featured_plugins_tab($args)
  {
    add_filter('plugins_api_result', array($this, 'plugins_api_result'), 10, 3);

    return $args;
  } // featured_plugins_tab


  /**
   * Add single plugin to featured list
   *
   * @return object
   */
  function add_plugin_featured($plugin_slug, $res)
  {
    // check if plugin is already on the list
    if (!empty($res->plugins) && is_array($res->plugins)) {
      foreach ($res->plugins as $plugin) {
        if (is_object($plugin) && !empty($plugin->slug) && $plugin->slug == $plugin_slug) {
          return $res;
        }
      } // foreach
    }

    if ($plugin_info = get_transient('wf-plugin-info-' . $plugin_slug)) {
      array_unshift($res->plugins, $plugin_info);
    } else {
      $plugin_info = plugins_api('plugin_information', array(
        'slug'   => $plugin_slug,
        'is_ssl' => is_ssl(),
        'fields' => array(
          'banners'           => true,
          'reviews'           => true,
          'downloaded'        => true,
          'active_installs'   => true,
          'icons'             => true,
          'short_description' => true,
        )
      ));
      if (!is_wp_error($plugin_info)) {
        $res->plugins = array_merge(array($plugin_info), $res->plugins);
        set_transient('wf-plugin-info-' . $plugin_slug, $plugin_info, DAY_IN_SECONDS * 7);
      }
    }

    return $res;
  } // add_plugin_featured


  /**
   * Add plugins to featured plugins list
   *
   * @return object
   */
  function plugins_api_result($res, $action, $args)
  {
    remove_filter('plugins_api_result', array($this, 'plugins_api_result'), 10, 3);

    $res = $this->add_plugin_featured('security-ninja', $res);
    $res = $this->add_plugin_featured('under-construction-page', $res);

    return $res;
  } // plugins_api_result


  /**
   * Clean up on uninstall; no action on deactive at the moment
   *
   * @return null
   */
  static function uninstall()
  {
    delete_option('wp-reset');
    delete_option('wp-reset-snapshots');
  } // uninstall


  /**
   * Disabled; we use singleton pattern so magic functions need to be disabled
   *
   * @return null
   */
  private function __clone()
  { }


  /**
   * Disabled; we use singleton pattern so magic functions need to be disabled
   *
   * @return null
   */
  private function __sleep()
  { }


  /**
   * Disabled; we use singleton pattern so magic functions need to be disabled
   *
   * @return null
   */
  private function __wakeup()
  { }
} // WP_Reset class


// Create plugin instance and hook things up
// Only if in admin - plugin has no frontend functionality
if (is_admin() || WP_Reset::is_cli_running()) {
  global $wp_reset;
  $wp_reset = WP_Reset::getInstance();
  add_action('plugins_loaded', array($wp_reset, 'load_textdomain'));
  register_uninstall_hook(__FILE__, array('WP_Reset', 'uninstall'));
}
