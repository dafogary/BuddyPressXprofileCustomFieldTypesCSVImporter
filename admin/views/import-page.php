<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** @var array $groups */

$msg = isset( $_GET['bpxpi_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['bpxpi_msg'] ) ) : '';
$last_results = get_transient( 'bpxpi_last_results_' . get_current_user_id() );
?>
<div class="wrap">
	<h1><?php esc_html_e( 'XProfile Import/Export', 'bp-xprofile-importer' ); ?></h1>

	<?php if ( $msg === 'no_file' ) : ?>
		<div class="notice notice-error"><p><?php esc_html_e( 'No file uploaded.', 'bp-xprofile-importer' ); ?></p></div>
	<?php elseif ( $msg === 'upload_error' ) : ?>
		<div class="notice notice-error"><p><?php esc_html_e( 'Upload error.', 'bp-xprofile-importer' ); ?></p></div>
	<?php elseif ( $msg === 'import_done' ) : ?>
		<div class="notice notice-success"><p><?php esc_html_e( 'Import completed. See results below.', 'bp-xprofile-importer' ); ?></p></div>
	<?php endif; ?>

	<form method="post" enctype="multipart/form-data" id="bpxpi-import-form">
		<?php wp_nonce_field( 'bpxpi_import_action', 'bpxpi_import_nonce' ); ?>
		<input type="hidden" name="bpxpi_import_submit" value="1">

		<table class="form-table">
			<tr>
				<th scope="row"><label for="bpxpi_file"><?php esc_html_e( 'Spreadsheet file', 'bp-xprofile-importer' ); ?></label></th>
				<td>
					<input type="file" name="bpxpi_file" id="bpxpi_file" accept=".csv,.xls,.xlsx,.ods" required>
					<p class="description"><?php esc_html_e( 'CSV/XLS/XLSX/ODS supported. Header row required. Example headers: Field Name, Type, Description, Is_Required, Options, Order, Group, Parent_Field, Show_If_Value', 'bp-xprofile-importer' ); ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="bpxpi_group"><?php esc_html_e( 'Default Field Group', 'bp-xprofile-importer' ); ?></label></th>
				<td>
					<select name="bpxpi_group" id="bpxpi_group">
						<option value="0"><?php esc_html_e( '-- Select Group -- (or set via Group column)', 'bp-xprofile-importer' ); ?></option>
						<?php if ( ! empty( $groups ) ) : ?>
							<?php foreach ( $groups as $g ) : ?>
								<option value="<?php echo esc_attr( $g->id ); ?>"><?php echo esc_html( $g->name ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
					<p class="description"><?php esc_html_e( 'If a row has a Group column, that will be used; otherwise default is used.', 'bp-xprofile-importer' ); ?></p>
				</td>
			</tr>
		</table>

		<?php submit_button( __( 'Import Fields', 'bp-xprofile-importer' ), 'primary', 'bpxpi_import_submit' ); ?>
	</form>

	<hr>

	<h2><?php esc_html_e( 'Export Fields', 'bp-xprofile-importer' ); ?></h2>
	<form method="post">
		<?php wp_nonce_field( 'bpxpi_export_action', 'bpxpi_export_nonce' ); ?>
		<input type="hidden" name="bpxpi_export_submit" value="1">
		<select name="bpxpi_export_group" id="bpxpi_export_group">
			<?php if ( ! empty( $groups ) ) : ?>
				<?php foreach ( $groups as $g ) : ?>
					<option value="<?php echo esc_attr( $g->id ); ?>"><?php echo esc_html( $g->name ); ?></option>
				<?php endforeach; ?>
			<?php else: ?>
				<option value="0"><?php esc_html_e( 'No groups found', 'bp-xprofile-importer' ); ?></option>
			<?php endif; ?>
		</select>
		<?php submit_button( __( 'Export Selected Group', 'bp-xprofile-importer' ), 'secondary', 'bpxpi_export_submit' ); ?>
	</form>

	<?php if ( $last_results ) : ?>
		<h2><?php esc_html_e( 'Last Import Results', 'bp-xprofile-importer' ); ?></h2>
		<table class="widefat fixed striped">
			<thead>
				<tr><th><?php esc_html_e( 'Status', 'bp-xprofile-importer' ); ?></th><th><?php esc_html_e( 'Field', 'bp-xprofile-importer' ); ?></th><th><?php esc_html_e( 'Info', 'bp-xprofile-importer' ); ?></th></tr>
			</thead>
			<tbody>
			<?php foreach ( $last_results as $res ) : ?>
				<tr>
					<td><?php echo esc_html( isset( $res['status'] ) ? $res['status'] : '' ); ?></td>
					<td><?php echo esc_html( isset( $res['name'] ) ? $res['name'] : (isset($res['row']['field name']) ? $res['row']['field name'] : '') ); ?></td>
					<td><?php echo esc_html( isset( $res['error'] ) ? $res['error'] : ( isset( $res['reason'] ) ? $res['reason'] : '' ) ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
