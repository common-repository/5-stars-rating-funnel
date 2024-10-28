<?php
/**
 * @var $is_premium
 */
$feedbacks = RRTNGG_Feedbacks_Model::get();
$funnels   = array();

$date_format = get_option( 'date_format' );

?>

<table id="reviews-list" class="wp-list-table widefat fixed striped pages">
	<thead>
	<tr>
		<td class="manage-column column-cb check-column">
			<label class="screen-reader-text" for="cb-select-all-1"><?php esc_html_e( 'Select All' ); ?></label>
			<input id="cb-select-all-1" type="checkbox" />
		</td>
		<th class="column-primary"><?php esc_html_e( 'Email', '5-stars-rating-funnel' ); ?></th>
		<th><?php esc_html_e( 'Name', '5-stars-rating-funnel' ); ?></th>
		<th><?php esc_html_e( 'Funnel', '5-stars-rating-funnel' ); ?></th>
		<th><?php esc_html_e( 'Message', '5-stars-rating-funnel' ); ?></th>
		<th></th>
	</tr>
	</thead>
	<?php
	if ( ! empty( $feedbacks ) ) {
		?>
		<tbody id="the-list">
		<?php

		foreach ( $feedbacks as $feedback ) {

			$lead = RRTNGG_Leads_Model::get_by_id( $feedback['lead_id'] );
			// if ( empty( $lead ) ) {
			// continue;
			// }

			$funnel_id = ! empty( $lead['funnel_id'] ) ? $lead['funnel_id'] : $feedback['funnel_id'];
			$funnel    = ! empty( $lead['funnel_id'] ) ? get_post( $lead['funnel_id'] ) : get_post( $feedback['funnel_id'] );

			if ( empty( $funnel ) || 'publish' !== $funnel->post_status ) {
				$funnels[ $funnel_id ] = __( 'Funnel deleted or not published', '5-stars-rating-funnel' );
			} else {
				$title                 = $funnel->post_title;
				$funnels[ $funnel_id ] = $title . " (ID: {$funnel_id})";
			}

			$full_name_array = array();
			$email           = '';
			if ( ! empty( $feedback['first_name'] ) ) {
				$full_name_array[] = $feedback['first_name'];
			} elseif ( ! empty( $lead['first_name'] ) ) {
				$full_name_array[] = $lead['first_name'];
			}
			if ( ! empty( $feedback['last_name'] ) ) {
				$full_name_array[] = $feedback['last_name'];
			} elseif ( ! empty( $lead['last_name'] ) ) {
				$full_name_array[] = $lead['last_name'];
			}
			if ( ! empty( $feedback['email'] ) ) {
				$email = $feedback['email'];
			} elseif ( ! empty( $lead['email'] ) ) {
				$email = $lead['email'];
			}
			?>
			<tr>
				<th class="check-column">
					<input id="cb-select-<?php echo esc_attr( $feedback['ID'] ); ?>" type="checkbox" value="<?php echo esc_html( $feedback['ID'] ); ?>">
				</th>

				<td class="column-primary has-row-actions">
					<strong><?php echo esc_html( $email ); ?></strong>

					<button type="button" class="toggle-row"><span class="screen-reader-text"><?php esc_html_e( 'Show more details', '5-stars-rating-funnel' ); ?></span></button>
				</td>

				<td data-colname="<?php esc_html_e( 'Name', '5-stars-rating-funnel' ); ?>"><?php echo esc_html( implode( ' ', $full_name_array ) ); ?></td>
				<td data-colname="<?php esc_html_e( 'Funnel', '5-stars-rating-funnel' ); ?>">
													<?php
													if ( ! empty( $lead['funnel_id'] ) && isset( $funnels[ $lead['funnel_id'] ] ) ) {
														echo esc_html( $funnels[ $lead['funnel_id'] ] );
													} elseif ( ! empty( $feedback['funnel_id'] ) && isset( $funnels[ $feedback['funnel_id'] ] ) ) {
														echo esc_html( $funnels[ $feedback['funnel_id'] ] );
													} else {
														echo esc_html__( 'Funnel not available', '5-stars-rating-funnel' );
													}
													?>
	</td>
				<td data-colname="<?php esc_html_e( 'Message', '5-stars-rating-funnel' ); ?>">
					<?php echo wp_kses( wpautop( $feedback['message'] ), RRTNGG_Manager::get_allowed_tags() ); ?>
				</td>
				<td>
					<button class="rrtng_delete_feedback button button-small" data-feedback-id="<?php echo esc_attr( $feedback['ID'] ); ?>" data-confirm="<?php _e( 'Are you sure you want to delete this feedback. This action could not be undone!', '5-stars-rating-funnel' ); ?>">
						<?php esc_html_e( 'Delete', '5-stars-rating-funnel' ); ?>
					</button>
				</td>
			</tr>
			<?php
		}
		?>
		</tbody>
		<?php
	} else {
		?>
		<tbody id="the-list"></tbody>
		<?php
	}
	?>
	<tfoot>
	<tr>
		<td class="manage-column column-cb check-column">
			<label class="screen-reader-text" for="cb-select-all-1"><?php esc_html_e( 'Select All' ); ?></label>
			<input id="cb-select-all-1" type="checkbox" />
		</td>
		<th class="column-primary"><?php esc_html_e( 'Email', '5-stars-rating-funnel' ); ?></th>
		<th><?php esc_html_e( 'Name', '5-stars-rating-funnel' ); ?></th>
		<th><?php esc_html_e( 'Funnel', '5-stars-rating-funnel' ); ?></th>
		<th><?php esc_html_e( 'Message', '5-stars-rating-funnel' ); ?></th>
		<th></th>
	</tr>
	</tfoot>
</table>
