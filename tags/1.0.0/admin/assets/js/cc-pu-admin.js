jQuery(document).ready( function ($) { 
	 
	$('#post').prepend(
	 '<h2 class="nav-tab-wrapper" id="cc-pu-tabs"><a class="nav-tab nav-tab-active" href="#" title="Templates" data-target="cc-pu-tab-1">Templates</a><a class="nav-tab" href="#" title="Settings" data-target="cc-pu-tab-2">Settings</a></h2>'
	 );
	 
	$('#cc-pu-tabs a').on('click', function(e){
		e.preventDefault();
		var target = $(this).attr('data-target');
		
		if(!$(this).hasClass('nav-tab-active'))
		{
			$('.cc-pu-tab').hide();
			$('#cc-pu-tabs a').removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active');
			
			$('.'+target).show();
		}
	});
	
	$('.cc-pu-template-acivate').on('click', function(e){
		e.preventDefault();
		var template = $(this).attr('data-template');
		var base = $(this).attr('data-base');
		
		$('#poststuff .theme-browser .theme.active').removeClass('active');
		var theme = $(this).closest('.theme');
		theme.addClass('active'); 
		 
		
		$('#_cc_pop_up_template').val(template); 
		$('#_cc_pop_up_template_base').val(base); 
		$('#publish').trigger('click');
	});
	
	$('.cc-pu-customize-close').on('click', function(e){
		e.preventDefault();
		var template = $(this).attr('data-template');
		
		$('#cc-pu-customize-form-'+template).hide();  
	});
	 
	$('.cc-pu-template-edit').on('click', function(e){
		e.preventDefault();
		var thisEl = $(this);
		template = thisEl.attr('data-template');
		base = thisEl.attr('data-base');
		id = thisEl.attr('data-postid');
		nounce = thisEl.attr('data-nounce'); 
		
		$.ajax({
            url: ajax_object.ajaxUrl,
            async: true,
            type: "POST",
            data: {
                action: "cc_pu_load_preview_module",
                template: template,
				base: base,
				nounce: nounce,
				id:id
				
            },
            success: function(data) { 
			 	theme = thisEl.closest('.theme');
				previewWrapper = $('#cc-pu-customize-form-'+template); 
                $('#cc-pu-customize-preview-'+template).append(data);
				
				$('.theme').removeClass('active');
				theme.addClass('active');  
				
				$('#_cc_pop_up_template').val(template); 
				$('#_cc_pop_up_template_base').val(base);
				
				previewWrapper.find('.cc-pu-option-active .cc-pu-customize-style').trigger('change');   
	
				previewWrapper.show();  
            }
        }); 
	});
	 
}); 