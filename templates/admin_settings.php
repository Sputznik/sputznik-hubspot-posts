<?php

	if( isset( $_POST['api_key'] ) ){
		$settings = $this->get_settings();
		$settings['api_key'] = $_POST['api_key'];
		$this->write_settings( $settings );
  }

	$settings = $this->get_settings();
?>
<div class="wrap">
	<h1 class="sp-hs-heading">Sputznik Hubspot Settings <button id="sp-dht-btn" class="button">Clear Cache</button></h1>
  <form method="POST">
    <div>
      <p><strong><label>HUBSPOT API KEY</label></strong></p>
  		<input type="text" name="api_key" value="<?php echo isset( $settings['api_key'] ) ? $settings['api_key'] : ""; ?>" />
    </div>
    <p class='submit'><input type="submit" name="submit" class="button button-primary" value="Save Changes"><p>
  </form>
</div>
