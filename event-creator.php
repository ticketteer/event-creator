<?php

/**
 * Plugin Name:     Event Creator
 * Plugin URI:      https://dev.ticketteer.com
 * Description:     Ticketteer's event creator plugin for wordpress
 * Author:          The Ticketteer Team
 * Author URI:      https://ticketteer.com
 * Text Domain:     event-creator
 * Domain Path:     /languages/
 * Version:         2.0.0
 *
 * License:         GPLv3
 * License URI:     https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package         Ticketteer\EventCreator
 */

namespace Ticketteer\EventCreator;

// define('WP_DEBUG', true); // To enable debugging. Leave things just like this to output errors, warnings, notices to the screen:
// define( 'WP_DEBUG_LOG', true ); // To turn on logging

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

if (!function_exists('tt_log')) :
	function tt_log($varname)
	{
		if ($varname == 'q') :
			global $wpdb;
			foreach ($wpdb->queries as $query) {
				if (strpos($query[2], 'WP_Query->get_posts') !== false) :
					error_log(print_r($query, true));
				endif;
			}
			return;
		endif;
		ob_start();
		var_dump($varname);
		error_log(ob_get_clean());
	}
endif;

require_once(dirname(__FILE__) . '/lib/json_entities.php');
require_once(dirname(__FILE__) . '/lib/functions.php');
require_once(dirname(__FILE__) . '/lib/post_types.php');
require_once(dirname(__FILE__) . '/lib/table_columns.php');
require_once(dirname(__FILE__) . '/lib/widgets/calendar.php');
require_once(dirname(__FILE__) . '/lib/widgets/upcoming.php');
require_once(dirname(__FILE__) . '/lib/widgets/artist_dates.php');


class EventCreator
{

	use PostTypes;

	public $plugin_slug = 'event-creator';
	public $api_endpoint = 'https://dashboard.ticketteer.com/api/';

	public $event_dates;

	public static $instance;

	public $filename = __FILE__;

	function __construct()
	{
		add_action('init', array($this, 'init'), 1);
		add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));

		// settings
		$this->init_settings();

		// include common (public frontend) scripts
		add_action('wp_enqueue_scripts', array($this, 'common_scripts'));

		if (is_admin()) {
			$this->require_admin();
		}

		if (get_option('ticketteer-api-endpoint') && !empty(get_option('ticketteer-api-endpoint'))) {
			$this->api_endpoint = get_option('ticketteer-api-endpoint');
		}
	}

	/**
	 * Initializes the plugin
	 *
	 * @since 1.0.0
	 *
	 */
	function init()
	{
		$this->create_event_type_taxonomy();
		$this->create_default_event_types();
		$this->create_post_type_events();
		$this->create_post_type_dates();
		$this->create_post_type_artists();
		$this->create_post_type_venues();
	}

	/**
	 * initializes custom settings for event creator
	 *
	 * @since 1.0.0
	 *
	 */
	function init_settings()
	{

		register_setting('event-creator-settings', 'ticketteer-key');
		register_setting('event-creator-settings', 'ticketteer-api-endpoint');
		register_setting('event-creator-settings', 'ticketteer-pub-key');
		register_setting('event-creator-settings', 'default_start_time');
		register_setting('event-creator-settings', 'default_venue_id');
		register_setting('event-creator-settings', 'default_book_until_min');
		register_setting('event-creator-settings', 'default_book_until_perc');
		register_setting('event-creator-settings', 'default_buy_tickets_text');
		register_setting('event-creator-settings', 'default_sold_out_text');
		register_setting('event-creator-settings', 'default_rest_seats_text');
		register_setting('event-creator-settings', 'price_groups_text');
		$ticketteer_key = get_option('ticketteer-key');
		if (isset($ticketteer_key)) {
			global $ticketteer_enabled;
			$ticketteer_enabled = true;
			require_once(dirname(__FILE__) . '/lib/ticketteer/requests.php');
		}
	}


	/**
	 * include common scripts for public frontend
	 *
	 * @since 1.0.0
	 *
	 */
	public function common_scripts()
	{
		wp_register_style(
			'event_creator_common_css',
			plugins_url('/assets/css/event-creator-common.css', $this->filename),
			false,
			'1.0.0'
		);
		wp_enqueue_style('event_creator_common_css');
	}

	/**
	 * load_plugin_textdomain Tells wordpress to look inside /languages for additional
	 * 	translation files.
	 *
	 * @since 1.0.0
	 *
	 */
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain($this->plugin_slug, false, dirname(plugin_basename($this->filename)) . '/languages');
	}

	private function require_admin()
	{
		require_once(dirname(__FILE__) . '/lib/meta_box.php');
		require_once(dirname(__FILE__) . '/lib/artists/meta_box.php');
		require_once(dirname(__FILE__) . '/lib/venues/meta_box.php');
		require_once(dirname(__FILE__) . '/lib/ajax.php');
	}

	/**
	 * load_admin_partial
	 *
	 * @param  string $template the name of the template file to be loaded (without .php extension)
	 * @param  array  $data     [description]
	 *
	 * @return boolean if template could be required or not
	 *
	 * @since 1.0.0
	 *
	 */
	public function load_admin_partial($template, $data = array())
	{
		$dir = trailingslashit(plugin_dir_path(__FILE__) . 'lib/partials');
		if (file_exists($dir . $template . '.php')) {
			include($dir . $template . '.php');
			return true;
		}
		return false;
	}

	/**
	 * Return singleton of this class
	 *
	 * @return object Singleton of EventCreator
	 *
	 * @since 1.0.0
	 *
	 */
	public static function get_instance()
	{
		if (!isset(self::$instance) && !(self::$instance instanceof EventCreator)) {
			self::$instance = new EventCreator();
		}
		return self::$instance;
	}
}

// Load the addons class.
$event_creator = EventCreator::get_instance();
