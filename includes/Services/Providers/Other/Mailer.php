<?php
/**
 * Other mailer.
 *
 * @since 1.0.0
 * @package Other mailer.
 */

namespace SmartSMTP\Services\Providers\Other;

use SmartSMTP\Model\Provider;
use SmartSMTP\Helper;
use SmartSMTP\Services\Providers\MailerAbstract;

/**
 * Other mailer class.
 *
 * @since 0
 */
class Mailer extends MailerAbstract {

	/**
	 * Provider type name.
	 *
	 * @since 0
	 * @var string
	 */
	protected static $type = 'other';
	/**
	 * Other mailer constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param  [type] $php_mailer The default mailer.
	 */
	public function __construct( $php_mailer ) {
		$this->php_mailer = $php_mailer;
	}
	/**
	 * Other configuration.
	 *
	 * @since 1.0.0
	 */
	public static function get_configuration() {
		$mail_config_inst = new Provider();
		return $mail_config_inst->get_mail_config( self::$type );
	}
	/**
	 * Function to send an email using PHPMailer with SMTP configuration.
	 *
	 * This method configures the PHPMailer instance with SMTP settings retrieved from the configuration,
	 * and sends the email based on the provided `mail_data`. The SMTP configuration includes host, authentication,
	 * username, password, encryption type, port, and sender details.
	 *
	 * @since 1.0.0
	 *
	 * @param array $mail_data An associative array containing email details, which may include:
	 *                          - 'to'          => Recipient email address.
	 *                          - 'subject'     => Email subject.
	 *                          - 'message'     => Email body content.
	 *                          - 'headers'     => Additional headers for the email.
	 *                          - 'attachments' => List of file paths for attachments.
	 *                          This array is passed by reference and can be modified within the method.
	 *
	 * @return bool True on success, false on failure. Returns the result of the `send` method from the PHPMailer instance.
	 */
	public function send( &$mail_data ) {
		$mail_config = $this->get_configuration();
		// Set PHPMailer to use SMTP.
		$this->php_mailer->isSMTP();
		if ( isset( $mail_config['smtp_host'] ) && ! empty( $mail_config['smtp_host'] ) ) {
			$this->php_mailer->Host = sanitize_text_field( $mail_config['smtp_host'] );
		}
		if ( isset( $mail_config['smtp_authentication'] ) ) {
			$this->php_mailer->SMTPAuth = sanitize_text_field( $mail_config['smtp_authentication'] );
			if ( $mail_config['smtp_authentication'] ) {
				if ( isset( $mail_config['smtp_user_name'] ) ) {
					$this->php_mailer->Username = sanitize_text_field( $mail_config['smtp_user_name'] );
				}
				if ( isset( $mail_config['smtp_user_password'] ) ) {

					$this->php_mailer->Password = Helper::crypt_the_string( $mail_config['smtp_user_password'], 'd' );
				}
			}
		}

		if ( isset( $mail_config['smtp_type_of_encryption'] ) ) {

			$this->php_mailer->SMTPSecure = sanitize_text_field( $mail_config['smtp_type_of_encryption'] );
		}

		if ( isset( $mail_config['smtp_port'] ) && ! empty( $mail_config['smtp_port'] ) ) {
			$this->php_mailer->Port = sanitize_text_field( $mail_config['smtp_port'] );
		}
		if ( isset( $mail_config['smtp_from_name'] ) && isset( $mail_config['smtp_from_email_address'] ) ) {
			$this->php_mailer->setFrom( sanitize_email( $mail_config['smtp_from_email_address'] ), sanitize_text_field( $mail_config['smtp_from_name'] ) );
			$mail_data['headers']['from'] = sanitize_email( $mail_config['smtp_from_email_address'] );
		}
		if ( isset( $mail_config['smtp_reply_to_email_address'] ) && ! empty( $mail_config['smtp_reply_to_email_address'] ) ) {
			$this->php_mailer->addReplyTo( sanitize_email( $mail_config['smtp_reply_to_email_address'] ), 'Information' );
		}

		$result = $this->php_mailer->send();

		return $result;
	}

	/**
	 * Func to check the mail is complete or not.
	 *
	 * @since 1.0.0
	 */
	public static function is_mailer_complete() {
		$mail_config = self::get_configuration();
		if (
			empty( $mail_config['smtp_host'] ) ||
			empty( $mail_config['smtp_port'] )
		) {
			return false;
		}

		return true;
	}

}
