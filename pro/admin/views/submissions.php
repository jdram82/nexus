<?php
/**
 * Submissions View
 *
 * @package Nexus_Pro
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'nexus_form_submissions';

// Get all submissions
$submissions = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 100" );
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Form Submissions', 'nexus' ); ?></h1>

	<?php if ( ! empty( $submissions ) ) : ?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'ID', 'nexus' ); ?></th>
					<th><?php esc_html_e( 'Form', 'nexus' ); ?></th>
					<th><?php esc_html_e( 'Date', 'nexus' ); ?></th>
					<th><?php esc_html_e( 'IP Address', 'nexus' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'nexus' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $submissions as $submission ) : ?>
					<tr>
						<td><?php echo esc_html( $submission->id ); ?></td>
						<td><?php echo esc_html( get_the_title( $submission->form_id ) ); ?></td>
						<td><?php echo esc_html( $submission->created_at ); ?></td>
						<td><?php echo esc_html( $submission->ip_address ); ?></td>
						<td>
							<button type="button" class="button view-submission" data-id="<?php echo esc_attr( $submission->id ); ?>" data-form-id="<?php echo esc_attr( $submission->form_id ); ?>">
								<?php esc_html_e( 'View', 'nexus' ); ?>
							</button>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else : ?>
		<p><?php esc_html_e( 'No submissions found.', 'nexus' ); ?></p>
	<?php endif; ?>
</div>

<!-- Submission Modal -->
<div id="submission-modal" style="display: none;">
	<div class="submission-modal-content">
		<span class="close-modal">&times;</span>
		<h2 id="modal-title"></h2>
		<div id="modal-data"></div>
	</div>
</div>

<style>
#submission-modal {
	position: fixed;
	z-index: 100000;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0,0,0,0.5);
}

.submission-modal-content {
	background-color: #fff;
	margin: 5% auto;
	padding: 20px;
	border: 1px solid #888;
	width: 80%;
	max-width: 600px;
	max-height: 80vh;
	overflow-y: auto;
	position: relative;
}

.close-modal {
	color: #aaa;
	float: right;
	font-size: 28px;
	font-weight: bold;
	cursor: pointer;
}

.close-modal:hover {
	color: #000;
}

#modal-data table {
	width: 100%;
	margin-top: 20px;
}

#modal-data th,
#modal-data td {
	padding: 10px;
	text-align: left;
	border-bottom: 1px solid #eee;
}

#modal-data th {
	font-weight: 600;
	width: 30%;
}
</style>

<script>
jQuery(document).ready(function($) {
	var submissions = <?php echo wp_json_encode( $submissions ); ?>;
	
	$('.view-submission').on('click', function() {
		var id = $(this).data('id');
		var submission = submissions.find(s => s.id == id);
		
		if (submission) {
			var data = JSON.parse(submission.data);
			var formTitle = $(this).closest('tr').find('td:eq(1)').text();
			
			$('#modal-title').text('Submission #' + id + ' - ' + formTitle);
			
			var html = '<table>';
			html += '<tr><th><?php esc_html_e( 'Field', 'nexus' ); ?></th><th><?php esc_html_e( 'Value', 'nexus' ); ?></th></tr>';
			
			for (var key in data) {
				var label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
				var value = Array.isArray(data[key]) ? data[key].join(', ') : data[key];
				html += '<tr><th>' + label + '</th><td>' + value + '</td></tr>';
			}
			
			html += '<tr><th><?php esc_html_e( 'IP Address', 'nexus' ); ?></th><td>' + submission.ip_address + '</td></tr>';
			html += '<tr><th><?php esc_html_e( 'User Agent', 'nexus' ); ?></th><td>' + submission.user_agent + '</td></tr>';
			html += '<tr><th><?php esc_html_e( 'Submitted', 'nexus' ); ?></th><td>' + submission.created_at + '</td></tr>';
			html += '</table>';
			
			$('#modal-data').html(html);
			$('#submission-modal').show();
		}
	});
	
	$('.close-modal, #submission-modal').on('click', function(e) {
		if (e.target === this) {
			$('#submission-modal').hide();
		}
	});
});
</script>
