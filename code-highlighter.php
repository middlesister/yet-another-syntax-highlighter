<?php
/*
Plugin Name: Code Highlighter
Plugin URI: http://github.com/middlesister/code-highlighter
Description: Add syntax highlighting using highlight.js 
Version: 0.1
Author: Karin Taliga
Author URI: http://www.invistruct.com
License: GPL2

    Copyright 2012  Karin Taliga  (email: invistruct@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if ( !class_exists( 'Code_Highlighter' ) ) :

class Code_Highlighter {

	var $plugin_url = '';
	var $defaultsettings      = array();  // Contains the default settings
	var $settings             = array();  // Contains the user's settings
	var $themes               = array();  // Array of themes
	var $options_page_name 	= 'ivst-code-highlighter';	
	var $option_group 	= 'ivst-code-highlighter';		// as used byt settings-sections
	var $option_name 	= 'ivst_code_highlighter_settings';		// name of database option
	
	var $options_page_settingssections = array( 'general' );

	public function __construct(){
		
		//define the $plugin_url
		$this->plugin_url();
		
		// add [code] shortcode
		add_shortcode( 'code', array( &$this, 'shortcode' ) );

		
		// Include required files
		//include_once('inc/class.WordPress_Radio_Taxonomy.php');
		
		// add classes to body class for custom css capabilities
		add_filter( 'body_class', array( &$this, 'body_class' ) );
		
		//load plugin text domain for translations
		add_action( 'plugins_loaded', array( &$this,'load_text_domain' ) );

		// Outputting SyntaxHighlighter's JS and CSS
		add_action( 'wp_enqueue_scripts',			array( &$this, 'enqueue_script_and_styles' ),	15 );
		add_action( 'admin_print_scripts-edit.php',		array( &$this, 'enqueue_script_and_styles' ),	15 ); 
		add_action( 'wp_head',						array( &$this, 'add_custom_css' ),	15 ); 
		
		//add_action( 'wp_footer',		array( &$this, 'maybe_output_scripts' ),		15 );
		//add_action( 'admin_footer',		array( &$this, 'maybe_output_scripts' ),		15 );
		
		
		//register settings
		add_action( 'admin_init', array( &$this,'admin_init' ) );
		
		//add plugin options page
		add_action( 'admin_menu', array( &$this,'add_options_page' ) );
		
		//add settings link to plugins page
		add_filter( 'plugin_action_links', array( &$this,'add_action_link' ), 10, 2 );
		
		// register the script - placing in footer
		wp_register_script( 'code-highlighter-highlightjs', plugins_url('code-highlighter/inc/highlight.pack.js'), array(), '1.0', true );
		wp_register_script( 'code-highlighter-js', plugins_url('code-highlighter/inc/code-highlighter.js'), array( 'code-highlighter-highlightjs') , '1.0', true );
				
		
		// register the styles
		wp_register_style(  'code-highlighter-theme-default',			plugins_url('code-highlighter/styles/default.css'),				array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-arta',				plugins_url('code-highlighter/styles/arta.css'),				array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-ascetic',			plugins_url('code-highlighter/styles/ascetic.css'),				array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-brown_paper',		plugins_url('code-highlighter/styles/brown_paper.css'),			array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-dark',				plugins_url('code-highlighter/styles/dark.css'),				array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-far',				plugins_url('code-highlighter/styles/far.css'),					array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-github',			plugins_url('code-highlighter/styles/github.css'),				array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-googlecode',		plugins_url('code-highlighter/styles/googlecode.css'),			array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-idea',				plugins_url('code-highlighter/styles/idea.css'),				array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-ir_black',			plugins_url('code-highlighter/styles/ir_black.css'),			array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-magula',			plugins_url('code-highlighter/styles/magula.css'),				array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-monokai',			plugins_url('code-highlighter/styles/monokai.css'),				array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-pojoaque',			plugins_url('code-highlighter/styles/pojoaque.css'),			array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-school_book',		plugins_url('code-highlighter/styles/school_book.css'),			array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-solarized_dark',	plugins_url('code-highlighter/styles/solarized_dark.css'),		array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-solarized_light',	plugins_url('code-highlighter/styles/solarized_light.css'),		array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-sunburst',			plugins_url('code-highlighter/styles/sunburst.css'),			array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-vs',				plugins_url('code-highlighter/styles/vs.css'),					array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-xcode',				plugins_url('code-highlighter/styles/xcode.css'),				array(), '1.0' );
		wp_register_style(  'code-highlighter-theme-zenburn',			plugins_url('code-highlighter/styles/zenburn.css'),				array(), '1.0' );
		
		
		// Create array of default settings (you can use the filter to modify these)
		$this->defaultsettings = (array) apply_filters( 'code_highlighter_defaultsettings', array(
			'theme'          => 'default',
			'tab_replace'    => '    ',
			'additional_css' => "pre.code-highlight {padding: 0px;}\npre.code-highlight code {border: 1px solid #ccc; padding: 5px;}"
		) );
		
		// Create the settings array by merging the user's settings and the defaults
		$usersettings = (array) get_option( 'ivst_code_highlighter_settings' );
		$this->settings = wp_parse_args( $usersettings, $this->defaultsettings );
		
		// Create list of themes and their human readable names
		// Plugins can add to this list: http://www.viper007bond.com/wordpress-plugins/syntaxhighlighter/adding-a-new-theme/
		$this->themes = (array) apply_filters( 'code_highlighter_themes', array(
			'default'			=> __( 'Default',			'ivst-code-highlighter' ),
			'arta'				=> __( 'Arta',				'ivst-code-highlighter' ),
			'ascetic'			=> __( 'Ascetic',			'ivst-code-highlighter' ),
			'brown_paper'		=> __( 'Brown paper',		'ivst-code-highlighter' ),
			'far'				=> __( 'Far',				'ivst-code-highlighter' ),
			'dark'				=> __( 'Dark',				'ivst-code-highlighter' ),
			'github'			=> __( 'Github',			'ivst-code-highlighter' ),
			'googlecode'		=> __( 'Google Code',		'ivst-code-highlighter' ),
			'idea'				=> __( 'Idea',				'ivst-code-highlighter' ),
			'ir_black'			=> __( 'IR Black',			'ivst-code-highlighter' ),
			'magula'			=> __( 'Magula',			'ivst-code-highlighter' ),
			'monokai'			=> __( 'Monokai',			'ivst-code-highlighter' ),
			'pojoaque'			=> __( 'Pojoaque',			'ivst-code-highlighter' ),
			'school_book'		=> __( 'School Book',		'ivst-code-highlighter' ),
			'solarized_dark'	=> __( 'Solarized Dark',	'ivst-code-highlighter' ),
			'solarized_light'	=> __( 'Solarized Light',	'ivst-code-highlighter' ),
			'sunburst'			=> __( 'Sunburst',			'ivst-code-highlighter' ),
			'vs'				=> __( 'VS',				'ivst-code-highlighter' ),
			'xcode'				=> __( 'XCode',				'ivst-code-highlighter' ),
			'zenburn'			=> __( 'Zenburn',			'ivst-code-highlighter' ),
			'none'				=> __( '[None]',			'ivst-code-highlighter' ),
		) );
	}


	/**
	 * Helper function: define the plugin url
	 *
	 * @return string The plugin url
	 */
	function plugin_url() { 
		if ( $this->plugin_url ) return $this->plugin_url;
		return $this->plugin_url = plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
	}

	/**
	 * callback for register_activation_hook
	 *
	 * @return void
	 */
	function activate_plugin() {
		// check if there already are options in the database, add defaults if not
/* 		$code_highlighter_options = get_option( 'code_highlighter_options' );
		if ( false === $code_highlighter_options ) {
			$code_highlighter_options = $this->defaultsettings;
		}
		update_option( 'code_highlighter_options', $code_highlighter_options ); */
	}
	
	
	/**
	 * Callback for register_uninstall_hook
	 * 
	 * Cleanup database options and such
	 *
	 * @return void
	 */
	function uninstall_plugin() {
		
	}
	
	
	/**
	 * Load translations if they exist
	 *
	 * @return void
	 */
	function load_text_domain() {
		load_plugin_textdomain( 'ivst-code-highlighter', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	
	
	/**
	 * Add shortcode, same as original wp_highlight.js plugin for compatibility
	 *
	 * @return string Html content
	 **/
	function shortcode( $atts, $content ) {
		$language = $atts['lang'];
	    return "<pre class=\"code-highlight\"><code class=\"$language\">" . ltrim($content, '\n') . '</code></pre>';
	}
	
	
	/**
	 * Add body classes
	 *
	 * @param array Body classes
	 * @return array The body classes
	 **/
	function body_class( $c ) {
		$c[] = 'code-highlighter';
		$c[] = 'code-highlighter-theme-' . $this->settings['theme'] ;
	    return $c;
	}
	
	
	/**
	 * Enqueue and print styles and scripts
	 *
	 * @return void
	 **/
	function enqueue_script_and_styles() {
		wp_enqueue_script( 'code-highlighter-highlightjs' );
		wp_enqueue_script( 'code-highlighter-js' );
		
		wp_enqueue_style( 'code-highlighter-theme-' . $this->settings['theme'] );
	}
	
	
	/**
	 * Output custom css in head
	 *
	 * @return void
	 **/
	function add_custom_css() {
		if( $this->settings['additional_css'] ) {
			?>
			<style type='text/css'><?php echo $this->settings['additional_css']; ?></style>
			<?php
		}
	}


	/**
	 * Registers settings, setting sections and settings fields
	 *
	 * @return void
	 */
	function admin_init(){
		// params: option group (used by settings-field), option name (as stored in databse), callback
		register_setting( 'ivst-code-highlighter' , 'ivst_code_highlighter_settings', array( &$this,'validate_options' ) );
		
		// params: name/id, title, callback, page
		add_settings_section('code_highlighter_settings_general', '', array( &$this,'general_settings_section_text' ), 'ivst-code-highlighter' );
		
		// params: name/id, title, callback, page, section
		add_settings_field('code_highlighter_settings_colorscheme', __( 'Color Scheme', 'ivst-code-highlighter' ), array( &$this,'code_highlighter_settings_colorscheme' ), 'ivst-code-highlighter', 'code_highlighter_settings_general');
		//add_settings_field('code_highlighter_settings_tabreplace', __( 'Tab replace', 'ivst-code-highlighter' ), array( &$this,'code_highlighter_settings_tabreplace' ), 'ivst-code-highlighter', 'code_highlighter_settings_general');
		add_settings_field('code_highlighter_settings_additionalcss', __( 'Additional Css', 'ivst-code-highlighter' ), array( &$this,'code_highlighter_settings_additionalcss' ), 'ivst-code-highlighter', 'code_highlighter_settings_general');
	}


	/**
	 * Add settings page to options menu, hooked to admin_menu 
	 *
	 * @return void
	 */
	function add_options_page() {
		// params: 
		add_options_page( __( 'Code Highlighter Options', 'ivst-code-highlighter' ), __( 'Code Highlighter', 'ivst-code-highlighter' ), 'manage_options', 'ivst-code-highlighter', array( &$this,'code_highlighter_options_page' ) );	
	}
	
	
	/**
	 * The html markup for the Code Highlighter Options Page
	 *
	 * @return void
	 **/
	function code_highlighter_options_page() {
?>
<div class="wrap">
<?php screen_icon(); ?><h2><?php _e( 'Code Highlighter', 'ivst-code-highlighter' ) ?></h2>


<form action="options.php" method="post">
<?php 
// add the nonces
settings_fields( 'ivst-code-highlighter' );
// add our actual settings forms
do_settings_sections( 'ivst-code-highlighter' );
submit_button();
?>
</form>
</div>
<?php
	}
	
	/**
	 * Text output before the general settings section
	 *
	 * @return void
	 **/
	function general_settings_section_text() {

	}
	
	/**
	 * Render the html for colorscheme settings
	 *
	 * @return void
	 **/
	function code_highlighter_settings_colorscheme() {
		$code_highlighter_options = get_option( 'ivst_code_highlighter_settings' );
		?>
		<select name="ivst_code_highlighter_settings[theme]" id="code-highlighter-theme" class="postform">
		<?php
		foreach ( $this->themes as $theme => $name ) {
			echo '	<option value="' . esc_attr( $theme ) . '"' . selected( $code_highlighter_options['theme'], $theme, false ) . '>' . esc_html( $name ) . "&nbsp;</option>\n";
		}
		?>
		</select>
		<?php
	}
	
	
	/**
	 * Render the html for the tabreplace setting
	 *
	 * @return void
	 **/
	function code_highlighter_settings_tabreplace() {
		$code_highlighter_options = get_option( 'ivst_code_highlighter_settings' );
		?>
		<input name="ivst_code_highlighter_settings[tab_replace]" type="text" id="code-highlighter-tab_replace" value="<?php echo esc_attr( $this->settings['tab_replace'] ); ?>" class="small-text" />
		<?php
	}
	
	
	/**
	 * Render the html for additional css setting
	 *
	 * @return void
	 **/
	function code_highlighter_settings_additionalcss() {
		?>
		<textarea name="ivst_code_highlighter_settings[additional_css]" type="text" id="code-highlighter-additional_css" class="textinput" rows="10" style="width:50%"><?php echo esc_attr( $this->settings['additional_css'] ); ?></textarea>
		<?php
	}


	/**
	 * Validate options before entering database
	 *
	 * @param array $input The raw options from the form
	 * @return array $options Sanitized options ready for the database
	 */
	function validate_options( $input ) {
		
		$output['theme'] = ( ! empty($input['theme']) && isset($this->themes[$input['theme']]) ) ? strtolower($input['theme']) : $this->defaultsettings['theme'];
		//$output['tab_replace'] = (int) ( !empty($settings['tabsize']) )   ? $settings['tabsize']   : $this->defaultsettings['tabsize']
		$output['additional_css'] = wp_filter_kses( $input['additional_css'] );
		
		return $output;
		//return apply_filters( 'code_highlighter_options_validate', $output );
	}
	
	
	/**
	 * Add a link to the settings page to the plugins list
	 *
	 * @staticvar string $this_plugin holds the directory & filename for the plugin
	 * @param array  $links array of links for the plugins, adapted when the current plugin is found.
	 * @param string $file  the filename for the current plugin, which the filter loops through.
	 * @return array $links
	 */
	function add_action_link( $links, $file ) {
		static $this_plugin;
		if ( empty( $this_plugin ) ) $this_plugin = 'code-highlighter/code-highlighter.php';
		if ( $file == $this_plugin ) {
			$settings_link = '<a href="' . admin_url( 'options-general.php?page=ivst-code-highlighter' ) . '">' . __( 'Settings', 'ivst-code-highlighter' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

} // end class
endif;


// Register actvation hook
register_activation_hook( __FILE__, array( 'Code_Highlighter', 'activate_plugin' ) );

// Cleanup database on uninstall
register_uninstall_hook( __FILE__, array( 'Code_Highlighter', 'uninstall_plugin' ) );


/**
* Launch the whole plugin
*/
add_action( 'init', 'CodeHighlighter', 5 );
function CodeHighlighter() {
	global $Code_Highlighter;
	$Code_Highlighter = new Code_Highlighter(); 
}
?>