<?php
 $absolute_path = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
 $wp_load = $absolute_path[0] . 'wp-load.php';
 require_once($wp_load);

  /**
  Do stuff like connect to WP database and grab user set values
  */

	header('Content-type: text/css');
  header('Cache-control: must-revalidate');
	
	$plugin = CcPopUp::get_instance();
	
	if ( ! class_exists( 'CcPopUpTemplate' ) )
    require_once( CC_PU_PLUGIN_DIR . 'public/includes/chch-pop-up-template.php' );
	 
	$css ='';
	 
	$pop_ups = $plugin->get_pop_ups();
		
	if(!empty($pop_ups)) {
		 
		if(file_exists(CC_PU_PLUGIN_DIR . 'public/templates/css/fonts.css')) {
			$fonts = file_get_contents(CC_PU_PLUGIN_URL . 'public/templates/css/fonts.css');
			$css .= $fonts;  
		}
		
		if(file_exists(CC_PU_PLUGIN_DIR . 'public/templates/css/defaults.css')) {
			$defaults = file_get_contents(CC_PU_PLUGIN_URL . 'public/templates/css/defaults.css');
			$css .= $defaults;
		}
		 
		if(file_exists(CC_PU_PLUGIN_DIR . 'public/templates/m-5/css/base.css')){
			$base = file_get_contents(CC_PU_PLUGIN_URL . 'public/templates/m-5/css/base.css');
			$css .= $base;    
		} 
		  
		foreach($pop_ups as $id) {
			
			$template_id = get_post_meta( $id, '_chch_pop_up_template', true);
			$template_base = get_post_meta( $id, '_chch_pop_up_base', true);
			
			if(file_exists(CC_PU_PLUGIN_DIR . 'public/templates/'.$template_base.'/'.$template_id.'/css/style.css')){
				$template_style = file_get_contents(CC_PU_PLUGIN_URL . 'public/templates/'.$template_base.'/'.$template_id.'/css/style.css');
				$css .= $template_style;   
			} 
			
			$template = new CcPopUpTemplate($template_id,$template_base,$id); 
			$css .= $template->build_css(); 
			  
		}
		
		// Remove comments
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
		
		// Remove space after colons
		$css = str_replace(': ', ':', $css);
		
		// Remove whitespace
		$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
		 
		echo $css;
		
	}
?> 
