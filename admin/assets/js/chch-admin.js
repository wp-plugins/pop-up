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
		 
		
		$('#_chch_pop_up_template').val(template); 
		$('#_chch_pop_up_template_base').val(base); 
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
				
				$('#_chch_pop_up_template').val(template); 
				$('#_chch_pop_up_template_base').val(base);
				
				previewWrapper.find('.cc-pu-option-active .cc-pu-customize-style').trigger('change');   
	
				previewWrapper.show();  
            }
        }); 
	});
	 
	/////LIVE PREVIEW SCRIPTS
		  $( ".accordion-section-title" ).on('click', function(e){
		  
		  	var el = $(this);
			var target = el.next('.accordion-section-content');
	  	 	if(!$(this).hasClass('open')){
				$( ".accordion-section-title").removeClass('open'); 
				el.addClass('open');
				target.slideDown('fast');	
			}	
			else
			{
				el.removeClass('open');
				target.slideUp('fast');	
			}  
		}
	  );
	 
	 $( '.cc-pu-colorpicker' ).wpColorPicker({
	 	change: _.throttle(function() {
			var el = $(this);
			var template = el.attr('data-template');
			var elType = el.attr('type');
			var target = el.attr('data-customize-target');
			var styleAttr = el.attr('data-attr');
			var elValue = el.val(); 
			$('#cc-pu-customize-preview-'+template+' '+target).css(styleAttr,elValue);
		})
	 });
	 
	$('.cc-pu-customize-style').on('change', function(e){
		var el = $(this);
		
		var elId = el.attr('id');
		var elType = el.attr('type');
		var template = el.attr('data-template');
		var target = el.attr('data-customize-target');
		var styleAttr = el.attr('data-attr');
		var elValue = el.val(); 
		var elUnit = el.attr('data-unit');
		
		if(typeof elUnit === "undefined"){
			elUnit = '';
		}   
		
		if(styleAttr == 'background-image'){  
			$('#cc-pu-customize-preview-'+template+' '+target).css('background-image','url('+elValue+')');
			
			var n = elId.search("_image"); 
			if(n > 0) {
				$('#cc-pu-customize-preview-'+template+' '+target).css('background-size','cover');	
			}
		}
		else
		{ 
			$('#cc-pu-customize-preview-'+template+' '+target).css(styleAttr,elValue+elUnit);
		}
	  		  
	});
	
	$('.cc-pu-customize-content').on('keyup', function(e){
		var el = $(this); 
		var template = el.attr('data-template');
		var target = el.attr('data-customize-target');
		var elAttr = el.attr('data-attr');
		var elValue = el.val();  
		
		if(typeof elAttr === "undefined"){
			$('#cc-pu-customize-preview-'+template+' '+target).text(elValue); 
		}
		else {   
			$('#cc-pu-customize-preview-'+template+' '+target).attr(elAttr,elValue); 
		}
	});
	 
	
	
	
	$('.revealer').on('change', function(){
		var el = $(this);
		var target = el.attr('data-customize-target');
		
		if(el.hasClass('active')){
			$('#'+target).slideUp('fast');
			el.removeClass('active');
		} 
		else
		{
			$('#'+target).slideDown('fast');
			el.addClass('active');
		}
	});
	
	$('.revealer-group').on('change', function(){
		var el = $(this);
		var template = el.attr('data-template');
		var eltarget = el.attr('data-customize-target');
		var elAttr = el.attr('data-attr');
		
		var group = el.attr('data-group');
		var thisOption = el.find(":selected");
		var target = thisOption.val(); 
		
		$('#cc-pu-customize-preview-'+template+' '+eltarget).css('background-size','auto');
		if(target == 'no') {
			$('#cc-pu-customize-preview-'+template+' '+eltarget).css(elAttr ,'url()');	
					
		}
		
		$('#cc-pu-customize-form-'+template+' .'+group).slideUp();
		$('#cc-pu-customize-form-'+template+' #'+target).slideDown();
		$('#cc-pu-customize-form-'+template+' #'+target).find('.cc-pu-customize-style').trigger('change'); 
		
	}); 
	
	///// WP MEDIA UPLOAD JS
	var custom_uploader;
 
 
    $('.cc-pu-image-upload').click(function(e) {
 
        e.preventDefault();
		var target = $(this).attr('data-target');
 
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
 
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
			console.log(attachment);
            $('#'+target).val(attachment.url);
			$('#'+target).trigger('change');
        });
 
        //Open the uploader dialog
        custom_uploader.open();
 
    });
	 
}); 