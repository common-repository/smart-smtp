<?php
/**
 * SmartSMTP EmailLogsMigration class.
 *
 * @package  namespace SmartSMTP\Migration
 *
 * @since 1.0.0
 */

namespace SmartSMTP\Migration;

use SmartSMTP\Migration\Migration;

/**
 * EmailLogsMigration class for Wpeverest stmp.
 *
 * @since 1.0.0
 */
class MailLogsMigration extends Migration {

	/**
	 * Contructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		parent::__construct();
		$this->setup();
	}

	/**
	 * Email logs table name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $table_name = 'smart_smtp_mail_logs';

	/**
	 * Function to get the table name.
	 *
	 * @since 1.0.0
	 */
	public function get_table_name() {
		if ( ! isset( $this->connection ) ) {
			return '';
		}

		return $this->connection->prefix . $this->table_name;
	}

	/**
	 * Set up the table to store the email logs.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function setup() {
		global $wpdb;

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$collation = $this->get_collation();

		$sql = "CREATE TABLE IF NOT EXISTS  {$wpdb->prefix}smart_smtp_mail_logs (
			id BIGINT(20) NOT NULL AUTO_INCREMENT,
			site_id BIGINT(20),
			`to` VARCHAR(255) NOT NULL,
			`from` VARCHAR(255) NOT NULL,
			`subject` VARCHAR(500),
			body LONGTEXT,
			header LONGTEXT,
			attachments LONGTEXT,
			`status` VARCHAR(20),
			response LONGTEXT,
			extra TEXT,
			retries int(10),
			resent_count int(10),
			source VARCHAR(255),
			ip_address VARCHAR(40),
			error_message LONGTEXT,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			PRIMARY KEY  (id)
		) {$collation};";
		maybe_create_table(  $wpdb->prefix . 'smart_smtp_mail_logs', $sql );
		return true;
	}
}
