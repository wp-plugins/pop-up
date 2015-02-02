
jQuery(document).ready(function($) { 

    $( ".cc-pu-newsletter-form" ).submit(function( event ) {
		
		email = $(this).find('.cc-pu-form-control').val();
		nounce = $(this).find('#_ajax_nonce').val();
		popup = $(this).find('#_ajax_nonce').attr('data-popup');
		thanks = $(this).find('.cc-pu-thank-you'); 
		errorMessage = $(this).find('.cc-pu-error-message'); 
		
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
                console.log(errorMessage);
				var response = JSON.parse(data);
				console.log(response);
				if(response.status === 'ok') {
					thanks.fadeIn();
					
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
		
		if(!$.cookie('shown_modal_'+chchPopUpID)){
			$.cookie('shown_modal_'+chchPopUpID, 'true',{ path: '/' });	
		}
		
		$("#modal-"+chchPopUpID).hide();
		
	});
	
	
});