<?php
/**
 * Plugin Name: Redvolver Formidable PDF
 * Plugin URI: http://filippozanardo.com/
 * Description: Redvolver Formidable PDF
 * Version: 0.0.1
 * Author: Filippo Zanardo
 * Author URI: http://filippozanardo.com/
 * Requires at least: 4.1
 * Tested up to: 4.9
 * Text Domain: redvolver
 * Domain Path: /languages/
 * License: GPL3+
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once( __DIR__ . '/vendor/autoload.php' );


class RV_Form_PDF {
	/**
	 * The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Main Instance.
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();

		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->setup_constants();
		$this->includes();
		$this->init_hooks();
		//$this->db = \WeDevs\ORM\Eloquent\Database::instance();

	}

	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'redvolver' ), '1' );
	}

	/**
	 * Disable unserializing of the class.
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'redvolver' ), '1' );
	}

	private function setup_constants() {

		if ( ! defined( 'RVFPDF_VERSION' ) ) {
			define( 'RVFPDF_VERSION', '1.0.0' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'RVFPDF_PLUGIN_DIR' ) ) {
			define( 'RVFPDF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
    }

		// Plugin Folder URL.
		if ( ! defined( 'RVFPDF_PLUGIN_URL' ) ) {
			define( 'RVFPDF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		$upload = wp_upload_dir();
    $upload_dir = $upload['basedir'];
    $upload_dir = $upload_dir . '/rvpdf';
    if (! is_dir($upload_dir)) {
       mkdir( $upload_dir, 0700 );
    }

		define( 'RVFPDF_UPLOAD_DIR', $upload_dir );

	}

	private function init_hooks() {

		// Activation - works with symlinks
		register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( $this, 'activate' ) );

		register_uninstall_hook(basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( 'RV_Form_PDF', 'deactivate' ));

		add_action( 'after_setup_theme', array( $this, 'load_textdomain' ) );

		add_action('frm_row_actions', array($this, 'rv_pdf_link'), 10, 2);
		add_action('wp', array($this, 'rv_render_pdf'));

		add_filter( 'frm_notification_attachment', array($this, 'attach_pdf'), 10, 3 );
	}

	public function activate() {

		flush_rewrite_rules();
	}

	public function deactivate() {

	}

	public function includes() {

		include_once( 'includes/class-rv-pdf-generator.php' );
		include_once( 'includes/class-rv-template-loader.php' );

		$this->template_loader = new RV_Template_Loader;
		$this->generator = new RV_PDF_Generator;


	}

	public function load_textdomain() {

	}

	public function rv_pdf_link($actions, $entry) {


		$form_id = $entry->form_id;
		$entry_id = $entry->id;
		$actions['rvpdf'] = '<a href="' . esc_url( home_url() ) . '/?rvpdf=1&formid='.$form_id.'&entryid='.$entry_id.'">' . __( 'PDF', 'redvolver' ) . '</a>';

		return $actions;

	}

	public function rv_render_pdf ( ) {
		if ( empty($_REQUEST['rvpdf']) ) return;


		$args = array(
			'download' => true,
			'return' => false
		);

		$formid = $_REQUEST['formid'];
		$entryid = $_REQUEST['entryid'];
		$this->generator->generate($formid,$entryid,$args);

		exit();
	}

	public function attach_pdf($attachments, $form, $args) {

		$formid = $form->id;
		$entryid = $args['entry']->id;

		$args = array(
			'download' => true,
			'return' => true
		);

		$attach = $this->generator->generate($formid,$entryid,$args);

		$attachments[] = $attach;

		return $attachments;

	}

}


function RVFPDF() {
	return RV_Form_PDF::instance();
}

$GLOBALS['rv_form_pdf'] = RVFPDF();
