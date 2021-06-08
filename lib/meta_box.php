<?php

namespace Ticketteer\EventCreator;

class MetaBox
{

  public $base;

  public static $instance;

  public function __construct()
  {
    $this->base = EventCreator::get_instance();
    add_action('add_meta_boxes', array($this, 'create_meta_boxes'), 0);
    add_action('admin_enqueue_scripts', array($this, 'init_scripts'));

    add_action('admin_head', array($this, 'menu_icon'));
    add_action('admin_menu', array($this, 'admin_menu'), 13);

    // tabs
    add_action('event_creator_tab_general', array($this, 'general_tab'));
    add_action('event_creator_tab_settings', array($this, 'settings_tab'));
    add_action('event_creator_tab_dates', array($this, 'dates_tab'));

    // save content
    add_action('save_post', array($this, 'save_meta_box'), 10, 2);
    add_action('wp_trash_post', array($this, 'trash_associated_dates'));
    add_action('untrash_post', array($this, 'untrash_associated_dates'));
    add_action('before_delete_post', array($this, 'delete_associated_dates'));
  }

  public function create_meta_boxes()
  {

    add_meta_box(
      'event-creator',
      __('Event Creator', $this->base->plugin_slug, 'event-creator'),
      array($this, 'meta_box'),
      'events',
      'normal',
      'high'
    );
  }

  /**
   * loads initializer scripts and addon scripts required by
   * this plugin
   *
   * @since 1.0.0
   *
   */
  public function init_scripts()
  {
    wp_register_script(
      $this->base->plugin_slug . '_admin_js',
      plugins_url('/assets/js/admin.js', $this->base->filename),
      array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
      0
    );
    wp_enqueue_script($this->base->plugin_slug . '_admin_js');

    wp_register_style(
      'event_creator_css',
      plugins_url('/assets/css/event-creator.css', $this->base->filename),
      false,
      '1.0.0'
    );
    wp_enqueue_style('event_creator_css');

    wp_register_style(
      $this->base->plugin_slug . '_admin_css',
      plugins_url('/assets/css/admin.css', $this->base->filename),
      false,
      '1.0.0'
    );
    wp_enqueue_style($this->base->plugin_slug . '_admin_css');
  }

  public function meta_box($post)
  {

    if ($post->post_type != 'events') {
      return;
    }

    $this->base->load_admin_partial('meta_box', array(
      'post'          => $post,
      'textdomain'    => $this->base->plugin_slug,
      'tabs'          => $this->get_event_creator_tab_nav(),
    ));
  }

  public function save_meta_box($post_id, $post)
  {

    // Bail out if running an autosave, ajax, cron or revision.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if ($post->post_type != 'events') return;

    // Bail out if we fail a security check.
    if (!isset($_POST['_events'])) {
      return;
    }

    foreach ($_POST['_events'] as $key => $value) {
      update_post_meta($post_id, $key, $value);
    }
    error_log("call the fu action");

    do_action('event_creator_event_saved', $post);
  }

  /**
   * return the rendered tab template partial with given name
   *
   * @param  object $post The current post object
   * @param  string $tab_name The name of the template to load (must exist in partials/[$tab_name].php)
   *
   * @return string The rendered tab template
   *
   * @since 1.0.0
   */
  private function load_tab($post, $tab_name)
  {
    $this->base->load_admin_partial($tab_name, array(
      'post'          => $post,
      'textdomain'    => $this->base->plugin_slug,
    ));
  }

  /**
   * return the rendered settings_tab partial
   *
   * @param  object $post The current post object
   *
   * @return String The rendered settings tag html
   *
   * @since 1.0.0
   */
  public function settings_tab($post)
  {
    $this->load_tab($post, 'settings_tab');
  }

  /**
   * return the rendered dates_tab partial
   *
   * @param  object $post The current post object
   *
   * @return String The rendered dates tag html
   *
   * @since 1.0.0
   */
  public function dates_tab($post)
  {
    $this->load_tab($post, 'dates_tab');
  }

  /**
   * return the rendered general_tab partial
   *
   * @param  object $post The current post object
   *
   * @return String The rendered general tag html
   *
   * @since 1.0.0
   */
  public function general_tab($post)
  {
    $this->load_tab($post, 'general_tab');
  }

  /**
   * Returns array of tabs to be displayed in the event_dates metabox.
   *
   * @return array Array of tabs
   *
   * @since 1.0.0
   */
  public function get_event_creator_tab_nav()
  {
    $tabs = array(
      'general'     => __('General', $this->base->plugin_slug, 'event-creator'),
      'dates'     => __('Dates', $this->base->plugin_slug, 'event-creator'),
      'settings'   => __('Settings', $this->base->plugin_slug, 'event-creator'),
    );
    $tabs = apply_filters('event_creator_nav_tabs', $tabs);
    // $tabs['settings'] = __( 'Settings', $this->base->plugin_slug );

    return $tabs;
  }

  /**
   * Create Settings menu entry
   *
   * @since 1.0.0
   *
   */
  public function admin_menu()
  {
    global $ticketteer_enabled;
    $cs = $ticketteer_enabled ? '' : ' style="color:#ee5e18"';
    add_submenu_page(
      'edit.php?post_type=events',
      __('Settings', $this->base->plugin_slug, 'event-creator'),
      '<span' . $cs . '> ' . __('Settings', $this->base->plugin_slug, 'event-creator') . '</span>',
      apply_filters('event_creator_menu_cap', 'manage_options'),
      $this->base->plugin_slug . '-settings',
      array($this, 'settings_page')
    );
  }

  public function settings_page()
  {
    $this->base->load_admin_partial('settings', array(
      'textdomain'    => $this->base->plugin_slug,
    ));
  }

  /**
   * menu_icon
   *
   * @return String html stylesheet to format the event-creator icon in the
   *                     wordpress menu
   *
   * @since 1.0.0
   *
   */
  public function menu_icon()
  {
?>
    <style type="text/css">
      #menu-posts-events .wp-menu-image img {
        width: 16px;
        height: 16px;
      }
    </style>
<?php
  }

  /**
   * Return singleton of this class
   *
   * @return object Singleton of MetaBox
   *
   * @since 1.0.0
   *
   */
  public static function get_instance()
  {
    if (!isset(self::$instance) && !(self::$instance instanceof MetaBox)) {
      self::$instance = new MetaBox();
    }
    return self::$instance;
  }

  public function untrash_associated_dates($post_id)
  {
    $post = get_post($post_id);
    if ($post->post_type != 'events') return;
    $args = array(
      'post_type' => 'dates',
      'post_status' => get_post_stati(),
      'meta_key' => 'event_id',
      'meta_value' => $post_id,
    );
    $dates = get_posts($args);
    if (sizeof($dates) > 0) {
      foreach ($dates as $date) {
        wp_untrash_post($date->ID);
      }
    }
  }

  public function trash_associated_dates($post_id)
  {
    $post = get_post($post_id);
    if ($post->post_type != 'events') return;
    $args = array(
      'post_type' => 'dates',
      'meta_key' => 'event_id',
      'meta_value' => $post_id,
    );
    $dates = get_posts($args);
    if (sizeof($dates) > 0) {
      foreach ($dates as $date) {
        wp_trash_post($date->ID);
      }
    }
  }

  public function delete_associated_dates($post_id)
  {
    $post = get_post($post_id);
    if ($post->post_type != 'events') return;
    $args = array(
      'post_type' => 'dates',
      'meta_key' => 'event_id',
      'meta_value' => $post_id,
    );
    $dates = get_posts($args);
    if (sizeof($dates) > 0) {
      foreach ($dates as $date) {
        wp_delete_post($date->ID, true); // bypass trash
      }
    }
  }
}

// Load the addons class.
$meta_box = MetaBox::get_instance();
