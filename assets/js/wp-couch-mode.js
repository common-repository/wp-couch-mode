/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


// A $( document ).ready() block.
jQuery( document ).ready(function() {
	var loadingImg = couchmodejs.loading_img;
    jQuery('<div class="wpcm-overlay"><div class="wpcm-loading"><img src="'+loadingImg+'"/></div></div>').appendTo('body');
   
    jQuery(".wpcm-wrapper-link").click(function(){
			var postId = jQuery(this).data("get-id");
			jQuery(".wpcm-overlay").fadeIn();
            var demo = jQuery(this).attr('data-get-id');
                var data = {
				action: 'wpcm_read_mode_popup',
                post_id: postId
		};
		
	   // the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
	 	jQuery.post(couchmodejs.ajaxUrl, data, function(response) {
				jQuery('.wpcm-overlay-wrapper').hide();
				jQuery('.wpcm-overlay').html(response);	
				jQuery(".wpcm-overlay-wrapper").fadeIn(5000);				
	 	});
	 	return false;
    });
	jQuery("#wpcm-resize-couch").live('click' ,function(){
		if(jQuery('#wpcm-couch-main').hasClass('wpcm-wrapper-full')){
			jQuery( "#wpcm-couch-main" ).animate({
						top: "6%",width: "90%",height: "80%"
			}, 500, function() {
				jQuery('#wpcm-couch-main').removeClass('wpcm-wrapper-full');
			});
		
		}else{
			var screnn =jQuery( window ).width();
			screnn= screnn-60; 
			jQuery( "#wpcm-couch-main" ).animate({
					top: 0,width: screnn+'px',height: "100%"
			}, 500, function() {
				jQuery('#wpcm-couch-main').addClass('wpcm-wrapper-full');
			});
		}
	});
	
    jQuery("#wpcm-close-couch").live('click',function(){ 
        jQuery(".wpcm-overlay").fadeOut();
		var loadingImg = couchmodejs.loading_img;
		jQuery('.wpcm-overlay').html('<div class="wpcm-overlay"><div class="wpcm-loading"><img src="'+loadingImg+'"/></div></div>');		
    });
		
    jQuery('#wpcm-incfont').live('click',function(){
		curSize= parseInt(jQuery('.wpcm-overlay-wrapper p').css('font-size')) + 2;
        if(curSize<=20)
            jQuery('.wpcm-overlay-wrapper p').css('font-size', curSize);
    });  
	
    jQuery('#wpcm-decfont').live('click',function(){ 
		curSize= parseInt(jQuery('.wpcm-overlay-wrapper p').css('font-size')) - 2;
        if(curSize>=12)
            jQuery('.wpcm-overlay-wrapper p').css('font-size', curSize);
    }); 
});