jQuery(document).ready(function(){
  var sp_delete_hubspot_transients = {
    init: function() {
			jQuery('#sp-dht-btn').on('click', this.handleDelete );
		},
    handleDelete: function(){

      // HIDE NOTICE
      jQuery('#sp-hs-notice').remove();

			// SHOW LOADER
			var loader = jQuery('#sp-dht-btn');
			loader.text('Processing....');
      loader.attr('disabled', 'disabled');

			var ajaxUrl = `${ajaxurl}?action=sp_delete_hubspot_transients`;

      // APPENDS NOTICE
      var sp_hs_notice = function( type, msg ){
        var html = `<div id="sp-hs-notice" class="notice notice-${type}"><p><strong>${msg}.</strong></p></div>`;
        jQuery('.sp-hs-heading').after(html);
      };

			jQuery.ajax({
				type:'post',
				url	: ajaxUrl,
				success: function(response) {
          loader.removeAttr('disabled')
					loader.text('Clear Cache');
					response = JSON.parse(response);
          sp_hs_notice( response.notice_class, response.notice ); // SHOW NOTICE
				},
				error: function(error) {
          console.log(error);
				}
			}); // ajax

    } // handleDelete

  };

  sp_delete_hubspot_transients.init();

});
