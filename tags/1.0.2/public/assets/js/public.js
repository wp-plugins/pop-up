
jQuery(document).ready(function($) { 

    $( ".cc-pu-newsletter-form" ).submit(function( event ) {
		
		email = $(this).find('.cc-pu-form-control').val();
		nounce = $(this).find('#_ajax_nonce').val();
		popup = $(this).find('#_ajax_nonce').attr('data-popup');
		thanks = $(this).find('.cc-pu-thank-you'); 
		errorMessage = $(this).find('.cc-pu-error-message'); 
		autoClose = $(this).find('.cc-pu-btn').attr('data-auto-close'); 
		closeButton = $(this).closest('.modal-inner').find('.cc-pu-close');
		
		
		$.ajax({
            url: ajax_object.ajaxUrl,
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
						}, 2000); 
					}
					
				} else {
					errorMessage.fadeIn();	
				}
				
            }
        });
		event.preventDefault();
	});
	
	$( ".cc-pu-close" ).click(function( e ) {
		e.preventDefault();
		
		chchPopUpID = $(this).attr('data-modalID'); 
		controlViews = $(this).attr('data-views-control');  
		
		if(controlViews === 'yes'){
			if(!$.cookie('shown_modal_'+chchPopUpID)){
				$.cookie('shown_modal_'+chchPopUpID, 'true',{ path: '/' });	
			}
		}
		
		$("#modal-"+chchPopUpID).hide();
		
	});
	
	
});