jQuery(document).ready( function ($) {  
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
		
		$('#cc-pu-customize-preview-'+template+' '+eltarget).css('background-size','');
		if(target == 'no') {
			$('#cc-pu-customize-preview-'+template+' '+eltarget).css(elAttr ,'url()');	
					
		}
		
		$('#cc-pu-customize-form-'+template+' .'+group).slideUp();
		$('#cc-pu-customize-form-'+template+' #'+target).slideDown();
		$('#cc-pu-customize-form-'+template+' #'+target).find('.cc-pu-customize-style').trigger('change'); 
		
	}); 
	 
}); 