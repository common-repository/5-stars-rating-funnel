<?php


class RRTNGG_Feedbacks_Model {
	private static $table_name = 'rratingg_feedbacks';

	public static function get_table() {
		global $wpdb;

		return $wpdb->prefix . self::$table_name;
	}

	private static function get_schema() {
		global $wpdb;

		$table_name = self::get_table();
		$collate    = ( $wpdb->has_cap( 'collation' ) ) ? $wpdb->get_charset_collate() : '';
		$schema     = "CREATE TABLE {$table_name} (
ID bigint(20) unsigned NOT NULL auto_increment,
lead_id bigint(20) unsigned NOT NULL default '0',
email varchar(120) NOT NULL default '',
first_name varchar(120) NOT NULL default '',
last_name varchar(120) NOT NULL default '',
message longtext,
created_at datetime NOT NULL default '0000-00-00 00:00:00',
created_at_gmt datetime NOT NULL default '0000-00-00 00:00:00',
funnel_id INT NULL DEFAULT NULL,
PRIMARY KEY  (ID)
) $collate;";

		return $schema;
	}

	public static function install() {
		global $wpdb;
		$wpdb->hide_errors();
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( self::get_schema() );
	}

	public static function create( $data ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		if ( ! empty( $data['ID'] ) ) {
			$ID = $data['ID'];
			unset( $data['ID'] );
			$wpdb->update( $table_name, $data, array( 'ID' => $ID ) );
		} else {
			$wpdb->insert( $table_name, $data );
			$ID = $wpdb->insert_id;
		}

		return $ID;
	}

	public static function delete( $ids ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		if ( is_string( $ids ) ) {
			$ids = array( $ids );
		}

		$ids_in = implode( ', ', $ids );
		$sql    = $wpdb->prepare( "DELETE FROM {$table_name} WHERE ID IN (%s)", $ids_in );

		return $wpdb->query( $sql );
	}

	public static function delete_leads( $ids ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		if ( is_string( $ids ) ) {
			$ids = array( $ids );
		}

		$ids_in = implode( ', ', $ids );
		$sql    = $wpdb->prepare( "DELETE FROM {$table_name} WHERE lead_id IN (%s)", $ids_in );

		return $wpdb->query( $sql );
	}

	public static function get( $filter = array() ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		$sql = "SELECT * FROM {$table_name}";

		return $wpdb->get_results( $sql, ARRAY_A );
	}

	public static function get_by_id( $id, $select = '*' ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;
		$sql        = $wpdb->prepare( "SELECT {$select} FROM {$table_name} WHERE ID = '%s'", $id );

		return $wpdb->get_row( $sql, ARRAY_A );
	}

	public static function get_by_lead_id( $lead_id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;
		$sql        = "SELECT * FROM {$table_name} WHERE lead_id = '{$lead_id}'";
		$sql        = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE lead_id = '%s'", $lead_id );

		return $wpdb->get_row( $sql, ARRAY_A );
	}
}
