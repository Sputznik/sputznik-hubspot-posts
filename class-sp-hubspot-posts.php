<?php

class SP_HUBSPOT_POSTS{

  var $settings;
  var $shortcode_str;

  function __construct(){

    $this->read_settings();

    $this->shortcode_str = "sp_hubspot_posts";

    add_action( 'admin_menu', array( $this, 'admin_menu' ) );


    /* ENQUEUE STYLES */
    add_action( 'wp_enqueue_scripts', function(){
      wp_enqueue_style('sp-hubspot-css', SP_HUBSPOT_DIR_URL.'/assets/css/main.css', array(), time() );
    });

    /* SHORTCODE */
    add_shortcode( $this->shortcode_str, array( $this,'get_hubspot_posts' ) );
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

  function get_hubspot_posts( $atts ) {

    $curl = curl_init();

    $hubspot_api_key = $this->get_settings()['api_key'];

    $shortcode_atts = shortcode_atts( array(
      'limit' => 3,
      'state' => 'published'
    ), $atts, $this->shortcode_str );

    // SHOW ERROR IF API KEY IS NOT SET
    if( empty( $hubspot_api_key ) ){
      return "Add Hubspot Api Key";
    }

    curl_setopt_array( $curl, array(
      CURLOPT_URL => "https://api.hubapi.com/cms/v3/blogs/posts?hapikey=".$hubspot_api_key."&limit=".$shortcode_atts['limit']."&state=".$shortcode_atts['state'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "accept: application/json"
      ),
    ));

    $response = json_decode( curl_exec( $curl ) );
    $err = curl_error( $curl );

    curl_close( $curl );

    if ( $err ) {
      return  "cURL Error #:" . $err;
    }
    else {
      ob_start();
      include('templates/hubspot-3grid.php');
      return ob_get_clean();
    }

  }

  function settings_page(){
    include( "templates/admin_settings.php" );
  }

}

new SP_HUBSPOT_POSTS;
