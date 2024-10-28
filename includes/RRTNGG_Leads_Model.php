<?php


class RRTNGG_Leads_Model {
	private static $table_name = 'rratingg_leads';

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
funnel_id bigint(20) unsigned NOT NULL default '0',
email varchar(120) NOT NULL default '',
title varchar(30) NOT NULL default '',
first_name varchar(120) NOT NULL default '',
last_name varchar(120) NOT NULL default '',
order_id varchar(30) NOT NULL default '',
hash varchar(40) NOT NULL default '',
status varchar(120) NOT NULL default '',
next_mail varchar(120) NOT NULL default '',
next_mail_status varchar(120) NOT NULL default '',
next_mail_at varchar(11) NOT NULL default '',
meta longtext,
link mediumtext NOT NULL,
created_at datetime NOT NULL default '0000-00-00 00:00:00',
created_at_gmt datetime NOT NULL default '0000-00-00 00:00:00',
updated_at datetime NOT NULL default '0000-00-00 00:00:00',
updated_at_gmt datetime NOT NULL default '0000-00-00 00:00:00',
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

	public static function get( $select = '*', $where = '' ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		if ( ! empty( $where ) ) {
			$where = ' WHERE ' . $where;
		}

		$sql = "SELECT {$select} FROM {$table_name} {$where} ORDER BY ID DESC";

		return $wpdb->get_results( $sql, ARRAY_A );
	}

	public static function count( $where = '' ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		if ( ! empty( $where ) ) {
			$where = ' WHERE ' . $where;
		}

		$sql = "SELECT COUNT(*) FROM {$table_name} {$where}";

		return $wpdb->get_var( $sql );
	}

	public static function get_by_id( $id, $select = '*' ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;
		$sql        = $wpdb->prepare( "SELECT {$select} FROM {$table_name} WHERE ID = '%s'", $id );

		return $wpdb->get_row( $sql, ARRAY_A );
	}

	public static function get_by_hash( $hash ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;
		$sql        = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE hash = '%s'", $hash );

		return $wpdb->get_row( $sql, ARRAY_A );
	}

	public static function set_global_lead_by_hash() {
		global $rratingg_lead;

		if ( ! empty( $_GET['rratingg_id'] ) ) {
			$rratingg_lead = self::get_by_hash( sanitize_text_field( $_GET['rratingg_id'] ) );

			if ( ! empty( $rratingg_lead ) && empty( $rratingg_lead['order_id'] ) ) {
				$rratingg_lead['order_id'] = RRTNGG_Manager::generate_order_id( $rratingg_lead['ID'] );

				self::create( $rratingg_lead );
			}
		} else {
			$rratingg_lead = self::get_dummy_user();
		}

		if ( ! empty( $rratingg_lead['funnel_id'] ) ) {
			$funnel = RRTNGG_Funnel_CPT::get( $rratingg_lead['funnel_id'], empty( $rratingg_lead['is_dummy'] ), ! empty( $rratingg_lead['is_dummy'] ) );

			if ( empty( $funnel ) ) {
				$rratingg_lead = null;
			}
		}
	}

	public static function get_dummy_user( $lead_status = 'invite_sent', $funnel_id = null ) {
		if ( empty( $funnel_id ) ) {

			if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
				return null;
			}

			global $post;
			if ( empty( $post ) ) {
				return null;
			}

			$funnel_id = $post->ID;
		}

		$funnel = RRTNGG_Funnel_CPT::get( $funnel_id, false, true );

		if ( empty( $funnel ) ) {
			return null;
		}

		$domain = rrtngg_get_current_domain_name();
		$email  = 'dummy@' . $domain;
		$hash   = sha1( $email . $funnel_id );

		$permalink = get_permalink( $funnel_id );

		if ( false === strpos( $permalink, '?' ) ) {
			$permalink = $permalink . '?rratingg_id=' . $hash;
		} else {
			$permalink = $permalink . '&rratingg_id=' . $hash;
		}

		$lead = array(
			'is_dummy'   => true,
			'ID'         => 999999999,
			'funnel_id'  => $funnel_id,
			'email'      => $email,
			'title'      => __( 'Mr.', '5-stars-rating-funnel' ),
			'first_name' => __( 'John', '5-stars-rating-funnel' ),
			'last_name'  => __( 'Doe', '5-stars-rating-funnel' ),
			'order_id'   => __( '123456-XXX', '5-stars-rating-funnel' ),
			'hash'       => sha1( $email . $funnel_id ),
			'status'     => $lead_status,
			'link'       => $permalink,
		);

		return $lead;
	}
}
