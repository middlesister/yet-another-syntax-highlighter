<?php
/*
Plugin Name: Yet Another Syntax Highlighter
Plugin URI: http://github.com/middlesister/yet-another-syntax-highlighter
Description: Add syntax highlighting using highlight.js 
Version: 0.2
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


if ( !class_exists( 'Yet_Another_Syntax_Highlighter' ) ) :

class Yet_Another_Syntax_Highlighter {

	var $plugin_url = '';
	var $plugin_version = '0.2';
	var $defaultsettings      = array();  // Contains the default settings
	var $settings             = array();  // Contains the current settings
	var $themes               = array();  // Array of themes
	var $options_page_name 	= 'yet-another-syntax-highlighter';					//
	var $option_group 	= 'yet-another-syntax-highlighter';				// as used by settings-sections
	var $option_name 	= 'yet_another_syntax_highlighter_settings';	// name of database option
	
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
		wp_register_script( 'yash-highlightjs', plugins_url('yet-another-syntax-highlighter/inc/highlight.pack.js'), array(), $this->plugin_version, true );
		wp_register_script( 'yash-js', 			plugins_url('yet-another-syntax-highlighter/inc/yash.js'), array( 'yash-highlightjs') , $this->plugin_version, true );
				
		
		// register the styles
		wp_register_style(  'yash-theme-default',			plugins_url('yet-another-syntax-highlighter/styles/default.css'),				array(), $this->plugin_version );
		wp_register_style(  'yash-theme-arta',				plugins_url('yet-another-syntax-highlighter/styles/arta.css'),					array(), $this->plugin_version );
		wp_register_style(  'yash-theme-ascetic',			plugins_url('yet-another-syntax-highlighter/styles/ascetic.css'),				array(), $this->plugin_version );
		wp_register_style(  'yash-theme-brown_paper',		plugins_url('yet-another-syntax-highlighter/styles/brown_paper.css'),			array(), $this->plugin_version );
		wp_register_style(  'yash-theme-dark',				plugins_url('yet-another-syntax-highlighter/styles/dark.css'),					array(), $this->plugin_version );
		wp_register_style(  'yash-theme-far',				plugins_url('yet-another-syntax-highlighter/styles/far.css'),					array(), $this->plugin_version );
		wp_register_style(  'yash-theme-github',			plugins_url('yet-another-syntax-highlighter/styles/github.css'),				array(), $this->plugin_version );
		wp_register_style(  'yash-theme-googlecode',		plugins_url('yet-another-syntax-highlighter/styles/googlecode.css'),			array(), $this->plugin_version );
		wp_register_style(  'yash-theme-idea',				plugins_url('yet-another-syntax-highlighter/styles/idea.css'),					array(), $this->plugin_version );
		wp_register_style(  'yash-theme-ir_black',			plugins_url('yet-another-syntax-highlighter/styles/ir_black.css'),				array(), $this->plugin_version );
		wp_register_style(  'yash-theme-magula',			plugins_url('yet-another-syntax-highlighter/styles/magula.css'),				array(), $this->plugin_version );
		wp_register_style(  'yash-theme-monokai',			plugins_url('yet-another-syntax-highlighter/styles/monokai.css'),				array(), $this->plugin_version );
		wp_register_style(  'yash-theme-pojoaque',			plugins_url('yet-another-syntax-highlighter/styles/pojoaque.css'),				array(), $this->plugin_version );
		wp_register_style(  'yash-theme-school_book',		plugins_url('yet-another-syntax-highlighter/styles/school_book.css'),			array(), $this->plugin_version );
		wp_register_style(  'yash-theme-solarized_dark',	plugins_url('yet-another-syntax-highlighter/styles/solarized_dark.css'),		array(), $this->plugin_version );
		wp_register_style(  'yash-theme-solarized_light',	plugins_url('yet-another-syntax-highlighter/styles/solarized_light.css'),		array(), $this->plugin_version );
		wp_register_style(  'yash-theme-sunburst',			plugins_url('yet-another-syntax-highlighter/styles/sunburst.css'),				array(), $this->plugin_version );
		wp_register_style(  'yash-theme-vs',				plugins_url('yet-another-syntax-highlighter/styles/vs.css'),					array(), $this->plugin_version );
		wp_register_style(  'yash-theme-xcode',				plugins_url('yet-another-syntax-highlighter/styles/xcode.css'),					array(), $this->plugin_version );
		wp_register_style(  'yash-theme-zenburn',			plugins_url('yet-another-syntax-highlighter/styles/zenburn.css'),				array(), $this->plugin_version );
		
		
		// Create array of default settings (you can use the filter to modify these)
		$this->defaultsettings = (array) apply_filters( 'yet_another_syntax_highlighter_defaultsettings', array(
			'theme'          => 'default',
			'tab_replace'    => '    ',
			'additional_css' => ".yash {padding: 0px;}\n.yash pre code {border: 1px solid #ccc; padding: 5px;}"
		) );
		
		// Create the settings array by merging the user's settings and the defaults
		$usersettings = (array) get_option( $this->option_name );
		$this->settings = wp_parse_args( $usersettings, $this->defaultsettings );
		
		// Create list of themes and their human readable names
		// Plugins can add to this list: http://www.viper007bond.com/wordpress-plugins/syntaxhighlighter/adding-a-new-theme/
		$this->themes = (array) apply_filters( 'yet_another_syntax_highlighter_themes', array(
			'default'			=> 'Default',		
			'arta'				=> 'Arta',			
			'ascetic'			=> 'Ascetic',		
			'brown_paper'		=> 'Brown paper',	
			'far'				=> 'Far',			
			'dark'				=> 'Dark',			
			'github'			=> 'Github',		
			'googlecode'		=> 'Google Code',	
			'idea'				=> 'Idea',			
			'ir_black'			=> 'IR Black',		
			'magula'			=> 'Magula',		
			'monokai'			=> 'Monokai',		
			'pojoaque'			=> 'Pojoaque',		
			'school_book'		=> 'School Book',	
			'solarized_dark'	=> 'Solarized Dark',
			'solarized_light'	=> 'Solarized Light',
			'sunburst'			=> 'Sunburst',		
			'vs'				=> 'VS',			
			'xcode'				=> 'XCode',			
			'zenburn'			=> 'Zenburn',		
			'none'				=> '[None]',		
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
		load_plugin_textdomain( 'yash', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	
	
	/**
	 * Add shortcode, same as original wp_highlight.js plugin for compatibility
	 *
	 * @return string Html content
	 **/
	function shortcode( $atts, $content ) {
		$language = $atts['lang'];
	    return "<pre class=\"yash\"><code class=\"$language\">" . ltrim($content, '\n') . '</code></pre>';
	}
	
	
	/**
	 * Add body classes
	 *
	 * @param array Body classes
	 * @return array The body classes
	 **/
	function body_class( $c ) {
		$c[] = 'yash';
		$c[] = 'yash-theme-' . $this->settings['theme'] ;
	    return $c;
	}
	
	
	/**
	 * Enqueue and print styles and scripts
	 *
	 * @return void
	 **/
	function enqueue_script_and_styles() {
		wp_enqueue_script( 'yash-highlightjs' );
		wp_enqueue_script( 'yash-js' );
		
		wp_enqueue_style( 'yash-theme-' . $this->settings['theme'] );
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
		register_setting( $this->option_group , $this->option_name, array( &$this,'validate_options' ) );
		
		// params: name/id, title, callback, page
		add_settings_section(	'yet_another_syntax_highlighter_settings_general', 			'', 							array( &$this,'general_settings_section_text' ), 							$this->options_page_name );
		
		// params: name/id, title, callback, page, section
		add_settings_field(		'yet_another_syntax_highlighter_settings_colorscheme', 		__( 'Color Scheme', 'yash' ), 	array( &$this,'yet_another_syntax_highlighter_settings_colorscheme' ), 		$this->options_page_name, 'yet_another_syntax_highlighter_settings_general');
		//add_settings_field(	'yet_another_syntax_highlighter_settings_tabreplace', 		__( 'Tab replace', 'yash' ), 	array( &$this,'yet_another_syntax_highlighter_settings_tabreplace' ), 		$this->options_page_name, 'yet_another_syntax_highlighter_settings_general');
		add_settings_field(		'yet_another_syntax_highlighter_settings_additionalcss', 	__( 'Additional Css', 'yash' ), array( &$this,'yet_another_syntax_highlighter_settings_additionalcss' ), 	$this->options_page_name, 'yet_another_syntax_highlighter_settings_general');
	}


	/**
	 * Add settings page to options menu, hooked to admin_menu 
	 *
	 * @return void
	 */
	function add_options_page() {
		// params: page title, menu title, capability, menu-slug, function
		add_options_page( __( 'Yet Another Syntax Highlighter', 'yash' ), __( 'Yet Another Syntax Highlighter', 'yash' ), 'manage_options', $this->options_page_name, array( &$this,'yet_another_syntax_highlighter_options_page' ) );	
	}
	
	
	/**
	 * The html markup for the Code Highlighter Options Page
	 *
	 * @return void
	 **/
	function yet_another_syntax_highlighter_options_page() {
?>
<div class="wrap">
<?php screen_icon(); ?><h2><?php _e( 'Yet Another Syntax Highlighter', 'yash' ) ?></h2>


<form action="options.php" method="post">
<?php 
// add the nonces
settings_fields( $this->option_group );
// add our actual settings forms
do_settings_sections( $this->option_group );
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
	function yet_another_syntax_highlighter_settings_colorscheme() {
		?>
		<select name="<?php echo $this->option_name; ?>[theme]" id="yet-another-syntax-highlighter-theme" class="postform">
		<?php
		foreach ( $this->themes as $theme => $name ) {
			echo '	<option value="' . esc_attr( $theme ) . '"' . selected( $this->settings['theme'], $theme, false ) . '>' . esc_html( $name ) . "&nbsp;</option>\n";
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
	function yet_another_syntax_highlighter_settings_tabreplace() {
		?>
		<input name="<?php echo $this->option_name; ?>[tab_replace]" type="text" id="yet-another-syntax-highlighter-tab_replace" class="small-text" value="<?php echo esc_attr( $this->settings['tab_replace'] ); ?>" />
		<?php
	}
	
	
	/**
	 * Render the html for additional css setting
	 *
	 * @return void
	 **/
	function yet_another_syntax_highlighter_settings_additionalcss() {
		?>
		<textarea name="<?php echo $this->option_name; ?>[additional_css]" type="text" id="yet-another-syntax-highlighter-additional_css" class="textinput" rows="10" style="width:50%"><?php echo esc_attr( $this->settings['additional_css'] ); ?></textarea><br />
		<?php
		$sectiontext = __( 'You can add custom css to adapt the output to match your wordpress theme.', 'yash' ) . '</br >';
		$sectiontext .= __( 'Yet Another Syntax Highlighter adds a body class of .yash and .yash-theme-[color scheme] when it is used that you can target.', 'yash' ) . '<br />';
		$sectiontext .= sprintf( __( 'For example: %s', 'yash' ),  '<code>.yash-theme-sunburst pre {border:1px solid black;}</code>' );
		echo $sectiontext;
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
		//return apply_filters( 'yet_another_syntax_highlighter_options_validate', $output );
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
		if ( empty( $this_plugin ) ) $this_plugin = 'yet-another-syntax-highlighter/yet-another-syntax-highlighter.php';
		if ( $file == $this_plugin ) {
			$settings_link = '<a href="' . admin_url( 'options-general.php?page=yet-another-syntax-highlighter' ) . '">' . __( 'Settings', 'yash' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

} // end class
endif;


// Register actvation hook
register_activation_hook( __FILE__, array( 'Yet_Another_Syntax_Highlighter', 'activate_plugin' ) );

// Cleanup database on uninstall
register_uninstall_hook( __FILE__, array( 'Yet_Another_Syntax_Highlighter', 'uninstall_plugin' ) );


/**
* Launch the whole plugin
*/
add_action( 'init', 'yet_another_syntax_highlighter', 5 );
function yet_another_syntax_highlighter() {
	global $Yet_Another_Syntax_Highlighter;
	$Yet_Another_Syntax_Highlighter = new Yet_Another_Syntax_Highlighter(); 
}
?>