<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BPXP_Admin_Page {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	public function register_menu() {
		$hook = add_users_page(
			'XProfile Import/Export',
			'XProfile Import/Export',
			'manage_options',
			'bpxp-importer',
			[ $this, 'render_page' ]
		);

		// load handlers only on our page
		add_action( "load-$hook", [ $this, 'handle_actions' ] );
	}

	public function enqueue_assets( $hook ) {
		if ( empty( $_GET['page'] ) || $_GET['page'] !== 'bpxp-importer' ) {
			return;
		}
		wp_enqueue_style( 'bpxpi-admin-css', plugins_url( '../assets/css/admin.css', __FILE__ ), [], '0.1' );
		wp_enqueue_script( 'bpxpi-admin-js', plugins_url( '../assets/js/admin.js', __FILE__ ), [ 'jquery' ], '0.1', true );
		wp_localize_script( 'bpxpi-admin-js', 'bpxpi', [
			'confirm_import' => __( 'Are you sure you want to import these fields? This will create XProfile fields.', 'bp-xprofile-importer' ),
		] );
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Insufficient permissions', 'bp-xprofile-importer' ) );
		}

		$groups = function_exists( 'xprofile_get_field_groups' ) ? xprofile_get_field_groups() : [];

		include BPXPI_PATH . 'admin/views/import-page.php';
	}

	/**
	 * Handle form submissions on our admin page.
	 */
	public function handle_actions() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// Import handler
		if ( isset( $_POST['bpxpi_import_submit'] ) && check_admin_referer( 'bpxpi_import_action', 'bpxpi_import_nonce' ) ) {
			$this->process_import();
		}

		// Export handler
		if ( isset( $_POST['bpxpi_export_submit'] ) && check_admin_referer( 'bpxpi_export_action', 'bpxpi_export_nonce' ) ) {
			$this->process_export();
		}
	}

	protected function process_import() {
		if ( empty( $_FILES['bpxpi_file'] ) || $_FILES['bpxpi_file']['error'] !== UPLOAD_ERR_OK ) {
			wp_redirect( add_query_arg( 'bpxpi_msg', 'no_file', wp_get_referer() ) );
			exit;
		}

		$file = $_FILES['bpxpi_file'];
		$wp_file = wp_handle_upload( $file, [ 'test_form' => false ] );
		if ( isset( $wp_file['error'] ) ) {
			wp_redirect( add_query_arg( 'bpxpi_msg', 'upload_error', wp_get_referer() ) );
			exit;
		}

		$filepath = $wp_file['file'];

		// Minimal mime check
		$finfo = wp_check_filetype( $filepath );
		$allowed = bpxpi_allowed_mime_types();
		if ( ! in_array( $finfo['type'], array_keys( $allowed ), true ) && ! in_array( $finfo['ext'], $allowed, true ) ) {
			// still try — but warn
			bpxpi_log( 'Uploaded file has unrecognized mime: ' . print_r( $finfo, true ) );
		}

		$default_group = isset( $_POST['bpxpi_group'] ) ? intval( $_POST['bpxpi_group'] ) : 0;

		$importer = new BPXP_Importer( $filepath );
		$importer->parse();
		$results = $importer->import( $default_group );

		// Save results in transient for display (short lived)
		set_transient( 'bpxpi_last_results_' . get_current_user_id(), $results, 60 );

		wp_redirect( add_query_arg( 'bpxpi_msg', 'import_done', wp_get_referer() ) );
		exit;
	}

	protected function process_export() {
		$group_id = isset( $_POST['bpxpi_export_group'] ) ? intval( $_POST['bpxpi_export_group'] ) : 0;
		$exporter = new BPXP_Exporter();
		$exporter->export_group( $group_id );
		// export_group exits after output
	}
}
