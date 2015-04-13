<?php
 $absolute_path = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
 $wp_load = $absolute_path[0] . 'wp-load.php';
 require_once($wp_load);

  /**
  Do stuff like connect to WP database and grab user set values
  */

	header('Content-type: text/javascript');
  header('Cache-control: must-revalidate');
	
	$plugin = CcPopUp::get_instance();
	
	if ( ! class_exists( 'CcPopUpTemplate' ) )
    require_once( CC_PU_PLUGIN_DIR . 'public/includes/chch-pop-up-template.php' );
	 
	$js ='';
	 
	$pop_ups = $plugin->get_pop_ups();
		
	if(!empty($pop_ups)) {
		
		if(file_exists(CC_PU_PLUGIN_DIR . 'public/assets/js/jquery-cookie/jquery.cookie.js')) {
			$cookie = file_get_contents(CC_PU_PLUGIN_URL . 'public/assets/js/jquery-cookie/jquery.cookie.js');
			$js .= $cookie;  
		}
		
		if(file_exists(CC_PU_PLUGIN_DIR . 'public/assets/js/public.js')) {
			$public = file_get_contents(CC_PU_PLUGIN_URL . 'public/assets/js/public.js');
			$js .= $public;
		}
		 
		  
		foreach($pop_ups as $id) {
			
			$template_id = get_post_meta( $id, '_chch_pop_up_template', true);
			$template_base = get_post_meta( $id, '_chch_pop_up_base', true);
			
			$template = new CcPopUpTemplate($template_id,$template_base,$id); 
			$js .= $template->build_js(); 
			  
		}
		 
		 
		echo $js;
		
	}
?> 