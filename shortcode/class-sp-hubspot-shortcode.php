<?php

/* BASE CLASS FOR SHORTCODE */
class SP_HUBSPOT_SHORTCODE extends SP_HUBSPOT_BASE{

	var $shortcode;
	var $template;
	var $hubspot_api_key;

	function __construct(){

		// SET THE HUBSPOT API KEY
		$this->set_hubspot_api();

		add_shortcode( $this->shortcode, array( $this, 'main_shortcode' ), 100 );
	}

	function get_atts( $atts ){
		$defaults_atts = apply_filters( $this->shortcode.'_atts', $this->get_default_atts() );
		return shortcode_atts( $defaults_atts, $atts, $this->shortcode );
	}

	function get_default_atts(){
		return array();
	}

	function get_hubspot_posts( $atts ) {

		$curl = curl_init();

		$query_params = "&limit=".$atts['limit']."&state=".$atts['state']."&sort=".$atts['sort'];

		// FILTER BLOG POSTS BY TAGS
		if( $atts['tag__in'] ){
			$query_params .= "&tagId__in=".$atts['tag__in'];
		}

		curl_setopt_array( $curl, array(
			CURLOPT_URL => "https://api.hubapi.com/cms/v3/blogs/posts?hapikey=".$this->hubspot_api_key.$query_params,
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

		if ( ! $err && isset(	$response->results )	) {
			ob_start();
			include(SP_HUBSPOT_DIR_PATH.'templates/hubspot-3grid.php');
			return ob_get_clean();
		} else {
			return '';
		}

	}

	function unique_atts(){
		return array();
	}

	function get_cache_key( $atts ){
		$atts = $this->get_atts( $atts );

		$cache_key = $this->shortcode;

		$unique_atts = $this->unique_atts();

		foreach( $unique_atts as $unique_att ){
			$cache_key .= $atts[$unique_att];
		}

		return $cache_key;
	}

	function get_cache( $atts ){
		$cache_key = $this->get_cache_key( $atts );

		// try to get value from Wordpress cache
		return get_transient( $cache_key );
	}

	function set_cache( $data, $atts ){
		$cache_key = $this->get_cache_key( $atts );

		// store value in cache for minutes
		set_transient( $cache_key, $data, ( $atts['cache'] * MINUTE_IN_SECONDS ) );

	}

	function get_hubspot_api(){ return $this->hubspot_api_key; }

	function set_hubspot_api(){
		global $sp_hubspot_admin;
		$this->hubspot_api_key = $sp_hubspot_admin->get_settings()['api_key'];
	}

	function main_shortcode( $atts ){

		$atts = $this->get_atts( $atts );

		$data = false;

		// SHOW ERROR IF API KEY IS NOT SET
		if( empty( $this->hubspot_api_key ) ){
			return "Add Hubspot Api Key";
		}

		/* CHECK IF THE DATA EXISTS IN CACHE */
		if( isset( $atts['cache'] ) && $atts['cache'] && is_numeric( $atts['cache'] ) ){
			$data = $this->get_cache( $atts );
		}

		// if no value in the cache
		if ( $data === false ) {

			$data = $this->get_hubspot_posts( $atts );

			if( isset( $atts['cache'] ) && $atts['cache'] && $data ){
				$this->set_cache( $data, $atts );
			}

		}

		return $data;

	}

}
