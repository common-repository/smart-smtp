<?php
/**
 * SmartSMTP Services class.
 *
 * @package  namespace SmartSMTP\Services\BaseMailer
 *
 * @since 1.0.0
 */

namespace SmartSMTP\Services;

use SmartSMTP\SmartSMTP;
use SmartSMTP\Model\Provider;

/**
 * Base Mailer.
 *
 * @since 1.0.0
 */
class BaseMailer {

	/**
	 * Easymail smtp mailer.
	 *
	 * @since 0
	 * @var [type] $php_mailer base php mailer.
	 */
	protected $php_mailer = null;
	/**
	 * BaseMailer constructor.
	 *
	 * @since 0
	 *
	 * @param  [type] $php_mailer The default mailer.
	 */
	public function __construct( $php_mailer ) {
		$this->php_mailer = $php_mailer;
	}
	/**
	 * Send Mail.
	 *
	 * This method sends an email using the specified provider type.
	 *
	 * @since 0.0.1
	 *
	 * @param array $mail_data An array containing email details such as 'to', 'subject', 'message', 'headers', and 'attachments'.
	 *                         This array is passed by reference and can be modified within the method.
	 *
	 * @return mixed The response from the mail provider's send method. It returns false if the provider type is not recognized.
	 */
	public function send( &$mail_data ) {
		$config_inst   = new Provider();
		$provider_type = $config_inst->get_provider_type();
		$res           = false;
		switch ( $provider_type ) {
			case 'brevo':
				$brevo = new \SmartSMTP\Services\Providers\Brevo\Mailer( $this->php_mailer );
				$res   = $brevo->send( $mail_data );
				break;
			case 'other':
				$other = new \SmartSMTP\Services\Providers\Other\Mailer( $this->php_mailer );
				$res   = $other->send( $mail_data );
				break;
			default:
				$other = new \SmartSMTP\Services\Providers\DefaultSmtp\Mailer( $this->php_mailer );
				$res   = $other->send( $mail_data );
		}
		return $res;
	}
}
