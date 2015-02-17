<?php
/**
 * CC Pop-Up
 *
 * @package   CcPopUpAdmin
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2014 
 */

if ( ! class_exists( 'CcPopUpPeview' ) )
    require_once( dirname( __FILE__ ) . '/includes/chch-pop-up-preview.php' );

if ( ! class_exists( 'CcPopUpTemplate' ) )
    require_once( CC_PU_PLUGIN_DIR . 'public/includes/chch-pop-up-template.php' );
/**
 * @package CcPopUpAdmin
 * @author 	Chop-Chop.org <shop@chop-chop.org>
 */
 

class CcPopUpAdmin { 
	
	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	
	function __construct() {
		$this->plugin = CcPopUp::get_instance(); 
		$this->plugin_slug = $this->plugin->get_plugin_slug();
		
		// Register Post Type
		add_action( 'init', array( $this, 'register_cc_pu_post_type' ) );
		
		// Register Post Type
		add_filter( 'post_updated_messages',  array( $this, 'cc_pu_post_type_messages') );
		
		// Register Post Type Meta Boxes and fields
		add_action( 'init', array( $this, 'cc_pu_initialize_cmb_meta_boxes'), 9999 );
		add_filter( 'cmb_meta_boxes', array( $this, 'cc_pu_posttype_metaboxes') );
		add_action( 'add_meta_boxes_chch-pop-up', array( $this, 'cc_pu_metabox' ));
		add_action( 'cmb_render_pages_select', array( $this, 'cc_pu_render_pages_select'), 10, 5  ); 
		
		// Templates view
		add_action('edit_form_after_title',array( $this, 'form_title' ));
		
		// Save Post Data
		add_action( 'save_post', array( $this, 'save_pop_up_meta'), 10, 3 ); 
		
		add_action( 'admin_init', array( $this,'cc_pu_tinymce_keyup_event') );  
		
		// Customize the columns in the popup list.
		add_filter('manage_chch-pop-up_posts_columns',array( $this, 'custom_pop_up_columns') ); 

		// Returns the content for the custom columns.
		add_action('manage_chch-pop-up_posts_custom_column',array( $this, 'manage_custom_pop_up_columns' ),10, 2); 
		  
		add_action( 'admin_print_scripts', array( $this, 'enqueue_cc_pu_admin_scripts' ));
		 
		add_action( 'admin_head', array( $this, 'admin_head_scripts') ); 
		
		add_action( 'wp_ajax_cc_pu_load_preview_module', array( $this, 'cc_pu_load_preview_module'  )); 
	} 
	
	
	/**
	 * Register tineMce event
	 *
	 * @since     1.0.0
	 * 
	 */
	function cc_pu_tinymce_keyup_event() { 
		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
			if ( get_bloginfo('version') < 3.9 ) { 
				add_filter( 'mce_external_plugins', array( $this, 'cc_pu_tinymce_event_old') );
			} else
			{
				add_filter( 'mce_external_plugins', array( $this, 'cc_pu_tinymce_event') );	 
			} 
		}
	}
  	
	
	/**
	 * Add keyup to tineMce for WP version > 3.9
	 *
	 * @since     1.0.0
	 * 
	 */
	function cc_pu_tinymce_event($plugin_array) { 
	 	$plugin_array['keyup_event'] = CC_PU_PLUGIN_URL .'admin/assets/js/chch-tinymce.js'; 
		return $plugin_array;
	}
	
	
	/**
	 * Add keyup to tineMce for WP version < 3.9
	 *
	 * @since     1.0.0
	 * 
	 */
	function cc_pu_tinymce_event_old($plugin_array) { 
	 	$plugin_array['keyup_event'] = CC_PU_PLUGIN_URL .'admin/assets/js/chch-tinymce-old.js'; 
		return $plugin_array;
	}
	
	
	/**
	 * Return a pages_select field for CMB
	 *
	 * @since     1.0.0
	 * 
	 */
	function custom_pop_up_columns($defaults) {
		$defaults['cc_pu_status'] = __('Active',$this->plugin_slug);
		$defaults['cc_pu_clicks'] = __('Clicks',$this->plugin_slug);
		$defaults['cc_pu_template'] = __('Template',$this->plugin_slug);
		return $defaults;
	}
	
 	
	/**
	 * Create columns in Pop-ups list
	 *
	 * @since     1.0.0  
	 */
	function manage_custom_pop_up_columns($column, $post_id) {
		global $post;
		if ($column === 'cc_pu_status') {
			echo ucfirst(get_post_meta($post_id,'_chch_pop_up_status', true));
		}
		
		if ($column === 'cc_pu_clicks') {
			echo '<a href="https://shop.chop-chop.org/pop-up/" target="_blank">AVAILABLE IN PRO</a>';
		}
		
		if ($column === 'cc_pu_template') {
			echo ucfirst(get_post_meta($post_id,'_chch_pop_up_template', true));
		}
	}
	
	
	/**
	 * Return a pages_select field for CMB
	 *
	 * @since     1.0.0
	 * 
	 */
	function cc_pu_render_pages_select( $field_args, $escaped_value, $object_id, $object_type, $field_type_object ) {
		$all_pages = $this->get_all_pages();
		 ?>
		<select class="cmb_select" name="<?php echo $field_args['_name']; ?>[]" id="<?php echo $field_args['_id']; ?>" multiple="multiple">	
		<?php
			foreach($all_pages as $value => $title):
				$selected = '';
				if(!empty($escaped_value)){
					if(in_array( $value,$escaped_value)) {
						$selected = 'selected';	
					} 
				}
			 	echo '<option value="'.$value.'" '.$selected .'>'.$title.'</option>	';
			endforeach
		 ?>
			</select>	 
		<?php  
		 
	} 
	
	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	/**
	 * Register Custom Post Type
	 *
	 * @since    0.1.0
	 */
	public function register_cc_pu_post_type() {
		
		$domain = $this->plugin_slug;
		
		$labels = array(
			'name'                => _x( 'Pop-Up CC', 'Post Type General Name', $domain),
			'singular_name'       => _x( 'Pop-Up', 'Post Type Singular Name', $domain),
			'menu_name'           => __( 'Pop-Up CC', $domain),
			'parent_item_colon'   => __( 'Parent Item:', $domain),
			'all_items'           => __( 'Pop-Ups CC', $domain),
			'view_item'           => __( 'View Item', $domain),
			'add_new_item'        => __( 'Add New Pop-Up', $domain),
			'add_new'             => __( 'Add New Pop-Up', $domain),
			'edit_item'           => __( 'Edit Pop-Up', $domain),
			'update_item'         => __( 'Update Pop-Up', $domain),
			'search_items'        => __( 'Search Pop-Up', $domain),
			'not_found'           => __( 'Not found', $domain),
			'not_found_in_trash'  => __( 'No Pop-Up found in Trash', $domain),
		);
 

		$args = array(
			'label'               => __( 'Pop-Up', $domain),
			'description'         => __( '', $domain),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => false,
			'menu_position'       => 65, 
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false
		);
		register_post_type( 'chch-pop-up', $args );
	}
	
	
	
	/**
	 * Pop-Ups update messages. 
	 *
	 * @param array $messages Existing post update messages.
	 *
	 * @return array Amended post update messages with new CPT update messages.
	 */
	function cc_pu_post_type_messages( $messages ) {
		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );
	
		$messages['chch-pop-up'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Pop-Up updated.', $this->plugin_slug ),
			2  => __( 'Custom field updated.', $this->plugin_slug),
			3  => __( 'Custom field deleted.',$this->plugin_slug),
			4  => __( 'Pop-Up updated.', $this->plugin_slug ), 
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Pop-Up restored to revision from %s', $this->plugin_slug ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Pop-Up published.', $this->plugin_slug ),
			7  => __( 'Pop-Up saved.', $this->plugin_slug ),
			8  => __( 'Pop-Up submitted.', $this->plugin_slug ),
			9  => sprintf(
				__( 'Pop-Up scheduled for: <strong>%1$s</strong>.', $this->plugin_slug ), 
				date_i18n( __( 'M j, Y @ G:i', $this->plugin_slug ), strtotime( $post->post_date ) )
			),
			10 => __( 'Pop-Up draft updated.', $this->plugin_slug )
		);
	
		if ( $post_type_object->publicly_queryable ) {
			$permalink = get_permalink( $post->ID );
	
			$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View Pop-Up',  $this->plugin_slug ) );
			$messages[ $post_type ][1] .= $view_link;
			$messages[ $post_type ][6] .= $view_link;
			$messages[ $post_type ][9] .= $view_link;
	
			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
			$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview Pop-Up',  $this->plugin_slug ) );
			$messages[ $post_type ][8]  .= $preview_link;
			$messages[ $post_type ][10] .= $preview_link;
		}
	
		return $messages;
	}
	
	/**
	 * Initialize Custom Metaboxes Class
	 *
	 * @since  0.1.0 
	 */
	function cc_pu_initialize_cmb_meta_boxes() {
 		if ( ! class_exists( 'cmb_Meta_Box' ) )
			require_once( dirname( __FILE__ ) . '/includes/Custom-Metaboxes-and-Fields-for-WordPress-master/init.php' ); 
	}
	
	/**
	 * Register custom metaboxes
	 *
	 * @since  0.1.0 
	 */
	function cc_pu_posttype_metaboxes( array $meta_boxes ) {
		
		$domain = $this->plugin_slug; 
		$prefix = '_chch_pop_up_';
	
		/**
		 * GENERAL OPTIONS
		 */
		$meta_boxes['cc-pu-metabox-general'] = array(
			'id'         => 'cc-pu-metabox-general',
			'title'      => __( 'GENERAL', $domain ),
			'pages'      => array( 'chch-pop-up', $domain ), 
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true,  
			'fields'     => array( 
				
				array(
					'name'    => __( 'Pop-up Status', $domain ),
					'desc'    => __( 'Enable or disable the plugin.', $domain  ),
					'id'      => $prefix . 'status',
					'type'    => 'radio_inline',
					'std'	=> 'yes',
					'options' => array(
						'yes' => __( 'Turned ON', $domain ),
						'no'   => __( 'Turned OFF', $domain ), 
					),
				),
				array(
					'name' => __( 'Show on mobile devices?', $domain  ),
					'desc'    => __( 'The pop-up will be visible on mobile devices.', $domain  ),
					'id'   => $prefix . 'show_on_mobile',
					'type' => 'checkbox',
				),
				array(
					'name' => __( 'Show only on mobile devices?', $domain  ),
					'desc'    => __( 'The pop-up will be visible on mobile devices only.', $domain  ),
					'id'   => $prefix . 'show_only_on_mobile',
					'type' => 'checkbox',
				),
				array(
					'name'       => __( 'Appear after', $domain ),
					'desc'    => __( 'seconds', $domain  ),
					'id'         => $prefix . 'timer',
					'type'       => 'text_small', 
					'default' => '0'
				), 
				array(
					'name' => __( 'Show once per session', $domain  ),
					'desc'    => __( '', $domain  ),
					'id'   => $prefix . 'show_only_once',
					'type' => 'checkbox', 
				), 
				array(
					'name' => __( 'Auto close the pop-up after the sign-up', $domain  ),
					'desc'    => __( '', $domain  ),
					'id'   => $prefix . 'auto_closed',
					'type' => 'checkbox', 
				),   
			),

		); 	
	
		
		/**
		 * DISPLAY CONTROL
		 */
		$meta_boxes['cc-pu-metabox-control'] = array(
			'id'         => 'cc-pu-metabox-control',
			'title'      => __( 'Display Control', $domain ),
			'pages'      => array( 'chch-pop-up', ), 
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true,  
			'fields'     => array( 
				array(
					'name' => __( 'By Role:', $domain  ),
					'desc'    => __( 'Decide who will see the pop-up.', $domain  ),
					'id'   => $prefix . 'role',
					'type' => 'radio',
					'options' => array(
						'all' => __( 'All', $domain  ),
						'unlogged' => __( 'Show to unlogged users', $domain  ),
						'logged' => __( 'Show to logged in users', $domain  ),
					),
					'default' => 'all'
				),
				array(
					'name' => __( 'Disable on:', $domain  ),
					'desc'    => __( 'Decide on which pages the pop-up will not be visible.', $domain  ),
					'id'   => $prefix . 'page',
					'type' => 'pages_select',  
				), 
			),
		); 
		
		/**
		 * Newsletter
		 */
		$meta_boxes['cc-pu-metabox-newsletter'] = array(
			'id'         => 'cc-pu-metabox-newsletter',
			'title'      => __( 'Newsletter', $domain ),
			'pages'      => array( 'chch-pop-up', ), 
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true,  
			'fields'     => array( 
				array(
					'name'    => __( 'Newsletter Status:', $domain ),
					'desc'    => __( 'Enable or disable newsletter subscribe form on the front-end.', $domain  ),
					'id'      => $prefix . 'newsletter',
					'type'    => 'radio_inline',
					'default'	=> 'yes',
					'options' => array(
						'yes' => __( 'Active', $domain ),
						'no'   => __( 'Inactive', $domain ), 
					),
				),
				array(
					'name' => __( 'E-mail Address:', $domain  ),
					'desc'    => __( 'Subscription notifications will be sent to this email. If there is no email provided, admin email will be used.', $domain  ),
					'id'   => $prefix . 'email',
					'type' => 'text_medium',
				), 
			),
		); 
		
		return $meta_boxes;
	}
	
	
	/**
	 * Register custom metaboxes
	 *
	 * @since  0.1.0 
	 */
	public function cc_pu_metabox( $post ) {
		remove_meta_box( 'slugdiv', 'cc-pop-up', 'normal' );
		$post_boxes = array(
			'cc-pu-metabox-general',
			'cc-pu-metabox-content',
			'cc-pu-metabox-control',
			'cc-pu-metabox-newsletter',
		);	
		
		foreach($post_boxes as $post_box)
		{
			add_filter( 'postbox_classes_chch-pop-up_'.$post_box,array( $this, 'add_metabox_classes') );
		}
	}
	
	/**
	 * Add metabox class for tabs
	 *
	 * @since  0.1.0 
	 */
	function add_metabox_classes( $classes ) {
 		array_push( $classes, "cc-pu-tab-2 cc-pu-tab" );
		return $classes; 
	}
	
	/**
	 * Return list of templates
	 *
	 * @since     1.0.0
	 *
	 * @return    array - template list
	 */
	public function get_templates() {
		if ( ! class_exists( 'PluginMetaData' ) )
			require_once( CC_PU_PLUGIN_DIR . 'admin/includes/PluginMetaData.php' ); 
		$pmd = new PluginMetaData;
		$pmd->scan(CC_PU_PLUGIN_DIR . 'public/templates');
		return $pmd->plugin;
	}
	
	/**
	 * Add Templates View
	 *
	 * @since  0.1.0 
	 */
	public function form_title( $post ) { 
		  
		$screen = get_current_screen();
		if ( 'post' == $screen->base && 'chch-pop-up' === $screen->post_type) {
			
			include(CC_PU_PLUGIN_DIR . '/admin/views/templates.php' );
		}
	}
	
	
	/**
	 * Save Post Type Meta
	 *
	 * @since  0.1.0 
	 */
	function save_pop_up_meta( $post_id, $post, $update ) { 
		if (
			!isset($_POST['chch_pu_save_nonce']) 
			|| ! wp_verify_nonce($_POST['chch_pu_save_nonce'],'chch_pu_save_nonce_'.$post_id) 
		) 
		{
			return;
		}
		
		if(defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) return;
		
		$slug = 'chch-pop-up';
		
		  
		if ( $slug != $post->post_type ) {
			return;
		}
		
		$template =  $_REQUEST['_chch_pop_up_template'];
		update_post_meta( $post_id, '_chch_pop_up_template', sanitize_text_field( $_REQUEST['_chch_pop_up_template']) );
		update_post_meta( $post_id, '_chch_pop_up_base', sanitize_text_field( $_REQUEST['_chch_pop_up_template_base']) );
		
		if(!empty($template))
		{
			$template_data = array();
			
			if ( isset( $_REQUEST['_'.$template.'_size_custom'] ) ) { 
				$template_data['size'] = array(
					'custom' => 1
				);   
			} else {
				$template_data['size'] = array(
					'custom' => 0
				); 
			}
			
			$template_data['size']['width'] = sanitize_text_field($_REQUEST['_'.$template.'_size_width']); 
			$template_data['size']['height'] = sanitize_text_field($_REQUEST['_'.$template.'_size_height']);
			 
			$template_data['background']= array(
				'color' => sanitize_text_field($_REQUEST['_'.$template.'_background_color']), 
				'type' => sanitize_text_field($_REQUEST['_'.$template.'_background_type']), 
				'image' => sanitize_text_field($_REQUEST['_'.$template.'_background_image']), 
				'pattern' => sanitize_text_field($_REQUEST['_'.$template.'_background_pattern']), 
				'repeat' => sanitize_text_field($_REQUEST['_'.$template.'_background_repeat']), 
				 
			);
			  
			$template_data['input']= array(   
				'text' => sanitize_text_field($_REQUEST['_'.$template.'_input_text']),
			);
			
			$template_data['button']= array( 
				'text' => sanitize_text_field($_REQUEST['_'.$template.'_button_text']),
			);
			
			$p_array = array('</p>','<p>');
		 
			$template_data['contents']= array(
				'header' => wp_kses_post(str_replace($p_array, '', $_REQUEST['_'.$template.'_contents_header'])),  
				'subheader' => wp_kses_post(str_replace($p_array, '', $_REQUEST['_'.$template.'_contents_subheader'])),   
				'content' => wp_kses_post($_REQUEST['_'.$template.'_contents_content']),  
				'privacy_message' => wp_kses_post(str_replace($p_array, '', $_REQUEST['_'.$template.'_contents_privacy_message'])),  
				'privacy_link' => sanitize_text_field($_REQUEST['_'.$template.'_contents_privacy_link']),   
			); 
			 
			update_post_meta($post_id, '_'.$template.'_template_data', $template_data);	
		}
	}
	
	
	/**
	 * Get all pages for CMB select pages field
	 *
	 * @since  0.1.0 
	 */
	private function get_all_pages() {
		
		$args = array(
		   'public'   => true,
		   '_builtin' => true
		);
		
		$post_types = get_post_types( $args );
		
        $args = array(
			'post_type' => $post_types,
			'posts_per_page' => -1, 
			'orderby' => 'title',
			'order' => 'ASC'
		);
		
		$post_list = get_posts($args);
		
		$all_posts = array();
		
		if($post_list):
			foreach($post_list as $post):
				$all_posts[$post->ID] = get_the_title($post->ID);
			endforeach;
		endif;
		
        return $all_posts; 
	}
	
	
	/**
	 * Include google fonts
	 *
	 * @since  0.1.0 
	 */
	public function admin_head_scripts() {
	 	$screen = get_current_screen();
		if ( 'post' == $screen->base && 'chch-pop-up' === $screen->post_type) { 
			
			$js ="<link href='http://fonts.googleapis.com/css?family=Playfair+Display:400,700,900|Lora:400,700|Open+Sans:400,300,700|Oswald:700,300|Roboto:400,700,300|Signika:400,700,300' rel='stylesheet' type='text/css'>";
			echo $js;
		}
	 } 
	 
	/**
	 * Register and enqueue admin-specific style sheet.
	 
	 */
	public function enqueue_cc_pu_admin_scripts() {

		$screen = get_current_screen();
		if ( 'post' == $screen->base && 'chch-pop-up' === $screen->post_type) { 
			wp_enqueue_style('wp-color-picker' ); 
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-slider'); 
			
			wp_enqueue_media();
			
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), CcPopUp::VERSION );
			
			wp_enqueue_script( $this->plugin_slug .'-admin-scripts', plugins_url( 'assets/js/chch-admin.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), CcPopUp::VERSION );  
			wp_localize_script( $this->plugin_slug .'-admin-scripts', 'ajax_object', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' )) );
			
			wp_enqueue_style( $this->plugin_slug .'-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css', null, CcPopUp::VERSION,'all' );
			
			if(file_exists(CC_PU_PLUGIN_DIR . 'public/templates/css/defaults.css'))
			{
				wp_enqueue_style($this->plugin_slug .'_template_defaults', CC_PU_PLUGIN_URL . 'public/templates/css/defaults.css', null, CcPopUp::VERSION, 'all');  
			}
			
			if(file_exists(CC_PU_PLUGIN_DIR . 'public/templates/css/fonts.css'))
			{
				wp_enqueue_style($this->plugin_slug .'_template_fonts', CC_PU_PLUGIN_URL . 'public/templates/css/fonts.css', null, CcPopUp::VERSION, 'all');  
			}
			
			$templates = $this->get_templates();
			
			if(count($templates))
			{	
				if(file_exists(CC_PU_PLUGIN_DIR . 'public/templates/m-5/css/base.css'))
				{
					wp_enqueue_style('base_m-5', CC_PU_PLUGIN_URL . 'public/templates/m-5/css/base.css', null, CcPopUp::VERSION, 'all');  
				}
				
				foreach($templates as $template)
				{
					$template_id = $template['id'];
					$base = $template['base'];
					
					
					if(file_exists(CC_PU_PLUGIN_DIR . 'public/templates/'.$base.'/'.$template_id.'/css/style.css'))
					{
					wp_enqueue_style('style_'.$template_id, CC_PU_PLUGIN_URL . 'public/templates/'.$base.'/'.$template_id.'/css/style.css', null, CcPopUp::VERSION, 'all');
					}
				}
			}
		}  

	}
	
	
	/**
	 * Load preview by ajax
	 *
	 */
	function cc_pu_load_preview_module() {
 
		$template = $_POST['template'];
		$template_base = $_POST['base'];
		$popup = $_POST['id'];
		
		$template = new CcPopUpTemplate($template,$template_base,$popup);
		$template->build_css();
		$template->get_template();	
		die();
	}
}
