<?php

class SP_HUBSPOT_ADMIN{

  var $settings;

  private $sp_delete_transients = 'sp_delete_hubspot_transients';

  function __construct(){

    $this->read_settings();

    add_action( 'admin_menu', array( $this, 'admin_menu' ) );

    /* ENQUEUE STYLES */
    add_action( 'wp_enqueue_scripts', function(){
      wp_enqueue_style('sp-hubspot-css', SP_HUBSPOT_DIR_URL.'/assets/css/main.css', array(), time() );
    });

    // ADMIN ASSETS
    add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

    add_action( 'wp_ajax_'.$this->sp_delete_transients, array( $this, $this->sp_delete_transients ) );

  }

  function admin_assets( $hook ){
    if( 'settings_page_sp_hubspot_settings' != $hook ) return;
    wp_enqueue_script( $this->sp_delete_transients, SP_HUBSPOT_DIR_URL . '/assets/js/clear-transients.js', array('jquery'), time(), true );
  }

  function admin_menu() {
    add_options_page(
      __( 'Settings', 'sp-hubspot-posts' ),
      __( 'Sputznik Hubspot Settings', 'sp-hubspot-posts' ),
      'manage_options',
      'sp_hubspot_settings',
      array( $this, 'settings_page' )
    );
  }

  function get_settings(){ return $this->settings; }
  function set_settings( $settings ){ $this->settings = $settings; }

  function read_settings(){
    $value = get_option( 'sp_hubspot_settings' );
    if( !$value || !is_array( $value ) ) return array();
    $this->set_settings( $value );
  }

  function write_settings( $settings ){
    update_option( 'sp_hubspot_settings', $settings );
    $this->set_settings( $settings );
  }

  function settings_page(){
    include( "templates/admin_settings.php" );
  }

  function sp_ajax_payload( $class = '', $msg = '' ){
    echo json_encode( array( 'notice' => $msg, 'notice_class' => $class ) );
    wp_die();
  }

  function sp_delete_hubspot_transients(){

    if( !current_user_can('administrator') ){
      $this->sp_ajax_payload( 'error', 'UNAUTHORIZED ACCESS' );
    }

    global $wpdb;

    $sp_transients = $wpdb->get_col(
      $wpdb->prepare( "
      SELECT REPLACE(option_name, '_transient_timeout_', '') AS transient_name
      FROM {$wpdb->options}
      WHERE option_name LIKE '\_transient\_timeout\_sp_hubspot_posts%'
      ")
    );

    // RETURN IF ALREADY CLEARED
    if( !$sp_transients ){ $this->sp_ajax_payload( 'warning', 'Nothing to delete.' ); }

    $options_names = array();

    // LOOP THROUGH ALL THE TRANSIENTS
    foreach( $sp_transients as $transient ){
      $options_names[] = '_transient_timeout_' . $transient;
      $options_names[] = '_transient_' . $transient;
    }

    $options_names = array_map(  array( $wpdb, '_escape' ), $options_names );
    $options_names = "'". implode("','", $options_names) ."'";

    // DELETE TRANSIENTS
    $result = $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name IN ({$options_names})" );

    if ( $result ){ $this->sp_ajax_payload( 'success', 'Cache cleared successfully.' ); }

    wp_die();

  }

}

global $sp_hubspot_admin;
$sp_hubspot_admin = new SP_HUBSPOT_ADMIN;
