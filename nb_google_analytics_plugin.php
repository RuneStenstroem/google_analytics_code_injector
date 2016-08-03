<?php
/*
Plugin Name: Google Analytics Plugin by NewBits
Plugin URI: http://www.runestenstroem.dk/2016/08/01/google-analytics-plugin-wordpress/
Description: This Plugin adds Google Analytics UA tracking code to your WordPress Website
Version: 1.0
Author: Rune Stenstrøm
Author URI: http://www.runestenstroem.dk
Text Domain: nb_gaci
*/

class NB_Gaci {
    public function __construct()
    {
    	add_action('plugins_loaded', array($this, 'nb_gaci_load_translation'));

    	add_action('admin_menu', array($this,'nb_gaci_add_settings_menu'));

    	add_action('admin_init', array($this,'nb_gaci_init_setting_page'));

    	add_action('wp_head', array($this,'add_google_analytics'));
     }

    //Default Values
    function nb_gaci_get_defaults(){
		$defaults = array(
			'google_code'   =>  'Google ID'
		);

	return $defaults;
	}

	function nb_gaci_get_settings_options() {
		$defaults = $this->nb_gaci_get_defaults();
		return array_merge( $defaults, (array)get_option( 'nb_gaci_options', array() ) );
	}

	// Initialise - load in translations
	function nb_gaci_load_translation () {
		load_plugin_textdomain('nb_gaci', false, dirname( plugin_basename( __FILE__ ) ) . '/lang');
	}

	// Adds the menu item in the settigns section
	function nb_gaci_add_settings_menu(){
		add_submenu_page(
			'options-general.php', 
			__('Google Analytics', 
			'nb_gaci' ), 
			__('Google Analytics', 
			'nb_gaci'), 'administrator', 
			'nb_gaci',
			array($this,'nb_gaci_settings_page')
			);
		}

	function nb_gaci_settings_page() {
		?>
		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">
			<?php screen_icon('screen'); ?>
			<h2><?php __("Google Analytics Plugin by NewBits", "nb_gaci" ) ?> </h2>
			<form method="post" action="options.php">
				<?php settings_fields( 'nb_gaci_options' ); ?>
				<?php do_settings_sections( 'nb_gaci_options' ); ?>         
				<?php submit_button(); ?>
			</form>

		</div><!-- /.wrap -->
		<?php
	}

	function nb_gaci_init_setting_page() {
		add_settings_section(
		    'general_settings_section',         
		    __('General Options','nb_gaci'),                  
		    array($this,'nb_gaci_add_section'), 
		    'nb_gaci_options'
	    );

		add_settings_field( 
		    'custom_post_type',
		    __('Tracking ID','nb_gaci'),
		    array($this,'nb_gaci_add_tracking_code_setting'),
		    'nb_gaci_options',
		    'general_settings_section',        
		    array(
		    	__('You find your tracking ID on your Google Analytics account page','nb_gaci')
		    	)
	    );
		register_setting(
			'nb_gaci_options',
			'nb_gaci_options',
			array($this,'nb_gaci_sanitize_options')
		);
	}

	function nb_gaci_add_section() {
		_e('Insert your Google Analytics Tracking ID in the text field below','nb_gaci');
	} 

	function nb_gaci_add_tracking_code_setting($args) {
		$options = $this->nb_gaci_get_settings_options();
		$html = '<input id="google_code" style="width: 35%;" type="text" name="nb_gaci_options[google_code]" value="'. $options['google_code'] .'"/>';
		$html .= '<label for="custom_post_type"> '  . $args[0] . '</label>'; 
		echo $html;
	}

	function nb_gaci_sanitize_options($input){
		if(preg_match('/^UA-[0-9]+-[0-9]+$/', $input['google_code'])){
			return $input;
		} else {
			return $this->nb_gaci_get_defaults();
		}
	}
	function add_google_analytics() { 
	$options = $this->nb_gaci_get_settings_options();
	if(preg_match('/^UA-[0-9]+-[0-9]+$/', $options['google_code'])){
		?>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

			ga('create', '<?php echo $options['google_code'] ?>', 'auto');
			ga('send', 'pageview');

		</script>
	<?php }
	}
}

$nb_gaci = new NB_Gaci();


