<?php
/*
  Plugin Name: WP Couch Mode
  Plugin URI: http://wordpress.org/plugins/wp-couch-mode/
  Description: WP Couch Mode lets you get a Clean or Simple Reading mode for post in your Wordpress website.
  Version: 1.3
  Author: Ritesh Vatwani
  Author URI: http://profiles.wordpress.org/ritesh1991
  License: GPLv2 or later
 */

 /**************************Backend End (Options)******************************* */

 
function wpcm_couch_mode_activate() {

	if(get_option( 'wpcm_readm_mode_link' ) == NULL){
		add_option( 'wpcm_readm_mode_link', 'after_content', '', 'yes' );
	}
	if(get_option( 'wpcm_read_mode_text' ) == NULL){
		add_option( 'wpcm_read_mode_text', 'Read Mode', '', 'yes' );
	}
	if(get_option( 'wpcm_resize_popup' ) == NULL){
		add_option( 'wpcm_resize_popup', '1', '', 'yes' );
	}
}
// Register Plugin activation Hook
register_activation_hook( __FILE__, 'wpcm_couch_mode_activate' );
 
// create plugin settings menu
add_action('admin_menu', 'wpcm_couch_mode_create_menu');

function wpcm_couch_mode_create_menu() {

    //create new top-level menu
    add_submenu_page('options-general.php', 'Read Mode Plugin Settings', 'Read Mode Settings', 'administrator', __FILE__, 'wpcm_couch_mode_settings_page');

    //call register settings function
    add_action('admin_init', 'wpcm_couch_mode_settings_register');
}

function wpcm_couch_mode_settings_register() {
    //register our settings
    register_setting('wpcm-couch-mode-settings-group', 'wpcm_readm_mode_link');
    register_setting('wpcm-couch-mode-settings-group', 'wpcm_read_mode_text');
    register_setting('wpcm-couch-mode-settings-group', 'wpcm_resize_popup');
}

function wpcm_couch_mode_settings_page() {
    ?>
    <div class="wrap">
        <h2>WP Couch Mode Settings</h2>

        <form method="post" action="options.php">
            <?php settings_fields('wpcm-couch-mode-settings-group'); ?>
            <?php do_settings_sections('wpcm-couch-mode-settings-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Read Mode Link</th>
                    <td>
                        <input type="radio" name="wpcm_readm_mode_link" value="before_content" <?php
                        if (get_option('wpcm_readm_mode_link') == 'before_content') {
                            echo 'checked=checked';
                        }
                        ?> /> Before Content &nbsp;
                        <input type="radio" name="wpcm_readm_mode_link" value="after_content" <?php
                        if (get_option('wpcm_readm_mode_link') == 'after_content') {
                            echo 'checked=checked';
                        }
                        ?> /> After Content &nbsp;
						
						<input type="radio" name="wpcm_readm_mode_link" value="custom" <?php
                        if (get_option('wpcm_readm_mode_link') == 'custom') {
                            echo 'checked=checked';
                        }
                        ?> /> Custom Shortcode
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Read Mode Link Text</th>
                    <td><input type="text" name="wpcm_read_mode_text" value="<?php echo get_option('wpcm_read_mode_text'); ?>" /></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Resize Popup</th>
                    <td>
					<input type="radio" name="wpcm_resize_popup" value="0" <?php
                        if (get_option('wpcm_resize_popup') == '0') {
                            echo 'checked=checked';
                        }
                        ?> /> No &nbsp;
						
						<input type="radio" name="wpcm_resize_popup" value="1" <?php
                        if (get_option('wpcm_resize_popup') == '1') {
                            echo 'checked=checked';
                        }
                        ?> /> Yes
			    </tr>
            </table>

            <?php submit_button(); ?>

        </form>
		<br>
		<h2>Read Mode Shortcode</h2>
		<hr>
		<div> 1) To use shortcode in a PHP file (outside the post editor). <br>
<pre> echo do_shortcode('[read_mode]'); </pre> <br>
	2) To use shortcode inside the post editor.
<pre> [read_mode] </pre> <br>
		</div>
    </div>
<?php } 

/**************************Front End ******************************* */

// Register Scripts
function wpcm_couch_mode_read_scripts() {
    wp_enqueue_style('plugin-css', plugin_dir_url(__FILE__) . 'assets/css/wp-couch-mode-style.css', array(), '1.0.0', false);
	wp_enqueue_script('couch-custom-js', plugin_dir_url(__FILE__) . 'assets/js/wp-couch-mode.js', array(), '1.0.0', true);
	wp_enqueue_script('couch-custom-js'); 
	wp_localize_script('couch-custom-js', 'couchmodejs', array('ajaxUrl' => admin_url( 'admin-ajax.php' ),'loading_img' => plugins_url('/wp-couch-mode/assets/images/loading.gif')));
   
}

// Hook into the 'wp_enqueue_scripts' action
add_action('wp_enqueue_scripts', 'wpcm_couch_mode_read_scripts');

/* add content after each post */
if (get_option('wpcm_readm_mode_link') != 'custom') {
	function wpcm_insert_read_mode_link($content) {
		
		$link = '<div class="wpcm-subscribe">';
		if(get_option( 'wpcm_read_mode_text') == NULL){
			$link .= '<a href="javascript:void(0);"  class="wpcm-wrapper-link" data-get-id="' . get_the_ID() . '">Read mode</a>';
		}else{
			$link .= '<a href="javascript:void(0);"  class="wpcm-wrapper-link" data-get-id="' . get_the_ID() . '">'.get_option( 'wpcm_read_mode_text').'</a>';
		}
		$link .= '</div>';
		
		if (get_option('wpcm_readm_mode_link') == 'before_content') {
			$custom_content .= $link;
			$custom_content .= $content;
			return $custom_content;
		}else if(get_option('wpcm_readm_mode_link') == 'after_content'){
			$content .= $link;
			return $content;
		}else{
			return $content;
		}
		
	}
	add_filter('the_content', 'wpcm_insert_read_mode_link');
}


// Add Short Code [read_mode]
if (get_option('wpcm_readm_mode_link') == 'custom') {
	function wpcm_read_mode_shortcode( $atts ){
		$read_more_text = get_option( 'wpcm_read_mode_text');
		if($read_more_text == NULL)
			$read_more_text = 'Read Mode';
			
		$read_link.= "<div class='wpcm-subscribe'>";
		$read_link.= '<a href="javascript:void(0);"  class="wpcm-wrapper-link" data-get-id="' . get_the_ID() . '">'.$read_more_text.'</a>';
		$read_link.= "</div>";
		return $read_link;
	}
	add_shortcode( 'read_mode', 'wpcm_read_mode_shortcode' );
}
add_action('wp_ajax_nopriv_wpcm_read_mode_popup', 'wpcm_read_mode_popup');
add_action('wp_ajax_wpcm_read_mode_popup', 'wpcm_read_mode_popup');

function wpcm_read_mode_popup() {
	
    $post_id = $_POST["post_id"];
    $page_data = get_page($post_id);

    $html = '';
    $html .='<div id="wpcm-couch-main" class="wpcm-overlay-wrapper">';
    if(get_option('wpcm_resize_popup') == 1 ){
		$html .='<div class="wpcm-close-overlay"><span id="wpcm-resize-couch" class="wpcm-icon-resize-expand wpcm-couch-icon"></span><span id="wpcm-close-couch" class="wpcm-icon-cancel wpcm-couch-icon"></span></div>';
	}else{
		$html .='<div class="wpcm-close-overlay"><span id="wpcm-close-couch" class="wpcm-icon-cancel wpcm-couch-icon"></span></div>';
	}
	$html .= '<a href="javascript:void(0)" class="wpcm-overlay-large wpcm-couch-icon">&nbsp;</a>';
    $html .= '<a href="javascript:window.print()" title="Print" class="wpcm-overlay-print wpcm-icon-printer wpcm-couch-icon">&nbsp;</a>';
    $html .= '<a href="javascript:void(0)" id="wpcm-incfont" title="Increase font size" class="wpcm-icon-font wpcm-in-fontsize wpcm-couch-icon">&nbsp;</a>';
    $html .= '<a href="javascript:void(0)" id="wpcm-decfont" title="Decrease font size" class="wpcm-icon-font wpcm-de-fontsize wpcm-couch-icon">&nbsp;</a>';
    $html .= '<div class="wpcm-wrapper-content">';

    $html .= '<h2>' . get_the_title($post_id) . '</h2>';
	if ( has_post_thumbnail($post_id) ) {
		$html .= '<div class="wpcm_post_thumnail">';
		$html .= get_the_post_thumbnail($post_id);
		$html .= '</div>';
    }
	$html .= wpautop($page_data->post_content);

    $html .= "</div></div>";

    echo $html;
    die();
}

?>