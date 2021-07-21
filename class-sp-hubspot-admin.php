<?php

class SP_HUBSPOT_ADMIN{

  var $settings;

  function __construct(){

    $this->read_settings();

    add_action( 'admin_menu', array( $this, 'admin_menu' ) );

    /* ENQUEUE STYLES */
    add_action( 'wp_enqueue_scripts', function(){
      wp_enqueue_style('sp-hubspot-css', SP_HUBSPOT_DIR_URL.'/assets/css/main.css', array(), time() );
    });

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

}

global $sp_hubspot_admin;
$sp_hubspot_admin = new SP_HUBSPOT_ADMIN;
