
jQuery(document).ready(function($) { 

    $( ".cc-puf-newsletter-form" ).submit(function( event ) {
		
		email = $(this).find('.cc-pu-form-control').val();
		nounce = $(this).find('#_ajax_nonce').val();
		popup = $(this).find('#_ajax_nonce').attr('data-popup');
		thanks = $(this).find('.cc-pu-thank-you'); 
		errorMessage = $(this).find('.cc-pu-error-message'); 
		autoClose = $(this).find('.cc-pu-btn').attr('data-auto-close'); 
		closeTimeOut = $(this).find('.cc-pu-btn').attr('data-auto-close-time') * 1000; 
		closeButton = $(this).closest('.modal-inner').find('.cc-pu-close');
		
		
		$.ajax({
            url: chch_pu_ajax_object.ajaxUrl,
            async: true,
            type: "POST",
            data: {
                action: "ajax_newsletter_subscribe",
                email: email,
				nounce: nounce,
				popup: popup,
				
            },
            success: function(data) {  
			
				var response = JSON.parse(data); 
				
				if(response.status === 'ok') {
					thanks.fadeIn();
					
					if(autoClose === 'yes'){
						setTimeout(function(){  
							closeButton.trigger('click');		
						}, closeTimeOut); 
					}
					
				} else {
					errorMessage.fadeIn();	
				}
				
            }
        });
		event.preventDefault();
	});
	
	$( ".cc-puf-close" ).click(function( e ) {
		e.preventDefault();
		
		chchPopUpID = $(this).attr('data-modalID'); 
		controlViews = $(this).attr('data-views-control');  
		controlExpires = $(this).attr('data-expires-control');  
		
		if(controlViews === 'yes' && controlExpires != 'refresh'){ 
			if(!$.cookie('shown_modal_'+chchPopUpID)){  
				switch(controlExpires){
					case 'session':
						$.cookie('shown_modal_'+chchPopUpID, 'true',{ path: '/' });	
					break; 			
				}
			}
		}
		
		$("#modal-"+chchPopUpID).hide("fast");
		
	});
	
	
});