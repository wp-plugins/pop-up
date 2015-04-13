<?php
/**
 * Pop-Up CC
 *
 * @package   CcPopUpTemplate
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2014 
 */

/**
 * @package CcPopUpTemplate
 * @author  Chop-Chop.org <shop@chop-chop.org>
 */
class CcPopUpTemplate { 

	private $template, $template_base, $post_id = 0;

	function __construct($template, $template_base, $post_id = 0) {
		$this->plugin = CcPopUp::get_instance(); 
		
		$this->template = $template;
		$this->template_base = $template_base;
		$this->post_id = $post_id; 
		 
	} 
	
	function get_template_options(){
		if(!$options = get_post_meta($this->post_id, '_'.$this->template.'_template_data',true)){
			if(file_exists(CC_PU_PLUGIN_DIR . 'public/templates/'.$this->template_base.'/'.$this->template.'/defaults.php'))
			{
				$options = (include(CC_PU_PLUGIN_DIR . 'public/templates/'.$this->template_base.'/'.$this->template.'/defaults.php'));
			}
		}
		 
		return $options;
	} 
	
	function get_template_option($base, $option){
		
		$all_options = $this->get_template_options();
		
		if(isset($all_options[$base][$option])){
			
			return $all_options[$base][$option];
			
		} elseif(file_exists(CC_PU_PLUGIN_DIR . 'public/templates/'.$this->template_base.'/'.$this->template.'/defaults.php')) {
			
			$default_options = (include(CC_PU_PLUGIN_DIR . 'public/templates/'.$this->template_base.'/'.$this->template.'/defaults.php'));
			
			if(isset($default_options[$base][$option])){ 
				return $default_options[$base][$option];
			}
		}
		 
		return '';
	} 
	
	
	function get_template(){ 
		$template_options = $this->get_template_options();
		$id = $this->post_id;
		include(CC_PU_PLUGIN_DIR . 'public/templates/'.$this->template_base.'/'.$this->template.'/index.php' );  
	}
	
	function build_css(){ 
		$options = $this->get_template_options();
		$template = $this->template_base;
		
		$prefix = '#modal-'.$this->post_id.' ';
		$css = '';
		$size_options = $options['size'];
		if($size_options['custom'])
		{
			$css .= $prefix.'.'.$template.'.pop-up-cc  {
				width: '.$size_options['width'].'px;
				height: '.$size_options['height'].'px;
			}';
		}
		 
		 
		$background_options = $options['background'];
		$css .= $prefix.'.'.$template.' .modal-inner { 
			
			background-color: '.$background_options['color'].';';
			
			switch($background_options['type']){ 
				case 'image':
					$css .= 'background-image: url('.$background_options['image'].');';
					$css .= 'background-size: cover;';
				break;
				
				case 'pattern':
					$css .= 'background-image: url('.$background_options['pattern'].');';
					$css .= 'background-repeat:'.$background_options['repeat'].';';
				break; 
			} 	
		$css .= '}';
		 
	
		return $css;  
	}
	
	function build_js()
	{
		$id = $this->post_id;
		
		$timer = get_post_meta($id, '_chch_pop_up_timer',true);
		$timer *= 1000;
		
		$mobile_header = 'if($(window).width() > 1024){';
		$mobile_footer = '}';
		
		if(get_post_meta($id, '_chch_pop_up_show_on_mobile',true))
		{
			$mobile_header = '';
			$mobile_footer = '';	
		}
		
		if(get_post_meta($id, '_chch_pop_up_show_only_on_mobile',true))
		{
			$mobile_header = 'if($(window).width() < 1025){'; 
			$mobile_footer = '}';
		}
		 
		$script = 'jQuery(function($) {';
		
		if(get_post_meta($id, '_chch_pop_up_show_once_per',true)) {
			$script .= 'if(!$.cookie("shown_modal_'.$id.'")){ ';
		}
		
		$script .= $mobile_header;
		
		$script .= 'setTimeout(function(){
			 $("#modal-'.$id.'").show("fast");
			 windowPos = $(window).scrollTop();
			 windowHeight = $(window).height();
			 popupHeight = $( "#modal-'.$id.' .modal-inner" ).outerHeight();
			 popupPosition = windowPos + ((windowHeight - popupHeight)/2);
			 $( "#modal-'.$id.' .pop-up-cc").css("top",Math.abs(popupPosition)); 
		}, '.$timer.');  ';
		$script .= $mobile_footer;
		
		if(get_post_meta($id, '_chch_pop_up_show_once_per',true)) {
			$script .= '}';
		}
		$script .= '});'; 
		
		return $script;		
	}
	
	
}