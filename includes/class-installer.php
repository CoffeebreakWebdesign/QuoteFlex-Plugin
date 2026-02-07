<?php
/**
 * Database installer class.
 *
 * Handles creation and updates of database tables.
 *
 * @package QuoteFlex
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * QuoteFlex Installer Class.
 */
class QuoteFlex_Installer {

	/**
	 * Run the installer.
	 *
	 * Creates all database tables.
	 *
	 * @since 1.0.0
	 */
	public static function install() {
		self::create_tables();
		self::save_db_version();
	}

	/**
	 * Create database tables.
	 *
	 * @since 1.0.0
	 */
	private static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Table 1: Quotes.
		$table_quotes = $wpdb->prefix . 'quoteflex_quotes';
		$sql_quotes   = "CREATE TABLE $table_quotes (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			quote_text TEXT NOT NULL,
			author VARCHAR(255) NOT NULL,
			author_description VARCHAR(500) DEFAULT NULL,
			source VARCHAR(255) DEFAULT NULL,
			source_type ENUM('api', 'manual') DEFAULT 'manual',
			api_source VARCHAR(100) DEFAULT NULL,
			category VARCHAR(255) DEFAULT NULL,
			date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
			date_modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			status ENUM('active', 'inactive') DEFAULT 'active',
			PRIMARY KEY (id),
			KEY idx_status (status),
			KEY idx_author (author),
			KEY idx_source_type (source_type),
			FULLTEXT KEY idx_quote_text (quote_text)
		) $charset_collate ENGINE=InnoDB;";

		dbDelta( $sql_quotes );

		// Table 2: Quote Sets.
		$table_sets = $wpdb->prefix . 'quoteflex_sets';
		$sql_sets   = "CREATE TABLE $table_sets (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			set_name VARCHAR(255) NOT NULL,
			set_slug VARCHAR(255) NOT NULL,
			description TEXT DEFAULT NULL,
			date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
			date_modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY idx_set_slug (set_slug),
			KEY idx_set_name (set_name)
		) $charset_collate ENGINE=InnoDB;";

		dbDelta( $sql_sets );

		// Table 3: Quote-Set Relationships.
		$table_relationships = $wpdb->prefix . 'quoteflex_set_relationships';
		$sql_relationships   = "CREATE TABLE $table_relationships (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			quote_id BIGINT(20) UNSIGNED NOT NULL,
			set_id BIGINT(20) UNSIGNED NOT NULL,
			date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			UNIQUE KEY idx_quote_set (quote_id, set_id),
			KEY idx_quote_id (quote_id),
			KEY idx_set_id (set_id)
		) $charset_collate ENGINE=InnoDB;";

		dbDelta( $sql_relationships );

		// Table 4: Categories.
		$table_categories = $wpdb->prefix . 'quoteflex_categories';
		$sql_categories   = "CREATE TABLE $table_categories (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			category_name VARCHAR(255) NOT NULL,
			category_slug VARCHAR(255) NOT NULL,
			description TEXT DEFAULT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY idx_category_slug (category_slug)
		) $charset_collate ENGINE=InnoDB;";

		dbDelta( $sql_categories );
	}

	/**
	 * Save database version to options.
	 *
	 * @since 1.0.0
	 */
	private static function save_db_version() {
		update_option( 'quoteflex_db_version', QUOTEFLEX_DB_VERSION );
	}

	/**
	 * Check if tables need updating.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public static function needs_update() {
		$saved_version = get_option( 'quoteflex_db_version', '0' );
		return version_compare( $saved_version, QUOTEFLEX_DB_VERSION, '<' );
	}
}
