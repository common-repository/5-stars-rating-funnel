<?php


class RRTNGG_Upgrade {
	public static function set_1_2_2_upgrade() {
		global $wpdb;
		$table_name = RRTNGG_Feedbacks_Model::get_table();
		$wpdb->hide_errors();
		$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$table_name}' AND column_name = 'subject' " );

		if ( ! empty( $row ) ) {
			$sql = "ALTER TABLE {$table_name} DROP COLUMN subject";
			$wpdb->query( $sql );
		}

		update_option( 'rrtngg_1_2_2_upgrade', 1 );
	}
	public static function set_1_2_13_upgrade() {
		global $wpdb;
		$table_name = RRTNGG_Leads_Model::get_table();
		$wpdb->hide_errors();
		$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$table_name}' AND column_name = 'sex' " );

		if ( ! empty( $row ) ) {
			$sql = "ALTER TABLE {$table_name} DROP COLUMN sex";
			$wpdb->query( $sql );
		}

		update_option( 'rrtngg_1_2_13_upgrade', 1 );
	}

	public static function set_1_2_32_upgrade() {
		// return;
		$image_url  = plugin_dir_url( RRTNGG_PLUGIN_FILE ) . 'assets/img/feedback-logo-default.jpg';
		$upload_dir = wp_upload_dir();
		$image_data = file_get_contents( $image_url );
		$filename   = basename( $image_url );

		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}

		file_put_contents( $file, $image_data );

		$wp_filetype = wp_check_filetype( $filename, null );

		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attach_id = wp_insert_attachment( $attachment, $file );
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		update_option( 'rrtngg_default_funnel_logo', $attach_id );

		update_option( 'rrtngg_1_2_32_upgrade', 1 );
	}

	public static function set_1_2_58_upgrade() {
		global $wpdb;
		$table_name = RRTNGG_Leads_Model::get_table();
		$wpdb->hide_errors();
		$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$table_name}' AND column_name = 'funnels_id' " );
		
		if ( empty( $row ) ) {
			$sql = "ALTER TABLE `wp_rratingg_feedbacks` 
			ADD COLUMN `funnel_id` INT NULL DEFAULT NULL AFTER `created_at_gmt`;";
			$wpdb->query( $sql );
		}
		update_option( 'rrtngg_1_2_58_upgrade', 1 );
	}

	/**
	 * New Table Used For Storing Rating Values Based On User Specific IP's And Funnel ID's
	 *
	 * @return bool
	 */
	public static function create_rating_storage_table() {
		global $wpdb;
		$table_name = "{$wpdb->prefix}ratings";
	
		// Check if the table exists
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			$custom_query = $wpdb->query("CREATE TABLE {$table_name} (
				id INT AUTO_INCREMENT NOT NULL,
				ip VARCHAR(255),
				timestamp_column TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				funnel_id BIGINT UNSIGNED,
				rating_value INT UNSIGNED NOT NULL,
				PRIMARY KEY (id)
			)");
	
			if ($custom_query) {
				return update_option('ip_funnel_upgrade', 1);
			} else {
				return false;
			}
		} else {
			// Table already exists, alter the ip column if needed
			$wpdb->query("ALTER TABLE {$table_name} MODIFY COLUMN ip VARCHAR(255) NOT NULL");
			// Check if the timestamp_column already exists and create if not
			$column_name = 'timestamp_column';

			$column_exists = $wpdb->get_results($wpdb->prepare(
				"SELECT COLUMN_NAME
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE TABLE_SCHEMA = %s
				AND TABLE_NAME = %s
				AND COLUMN_NAME = %s",
				$wpdb->dbname, $table_name, $column_name
			));

			if (empty($column_exists)) {
				$sql = "ALTER TABLE $table_name ADD COLUMN $column_name TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    		    $wpdb->query($sql);
			}
			return true;
		}
	}
}
