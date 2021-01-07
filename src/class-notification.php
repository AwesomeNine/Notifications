<?php
/**
 * The Single Notification
 *
 * @since   1.0.0
 * @package Awesome9\Notifications
 * @author  Awesome9 <me@awesome9.co>
 */

namespace Awesome9\Notifications;

/**
 * Notification class
 */
class Notification {

	/**
	 * Notification type.
	 *
	 * @var string
	 */
	const ERROR = 'error';

	/**
	 * Notification type.
	 *
	 * @var string
	 */
	const SUCCESS = 'success';

	/**
	 * Notification type.
	 *
	 * @var string
	 */
	const INFO = 'info';

	/**
	 * Notification type.
	 *
	 * @var string
	 */
	const WARNING = 'warning';

	/**
	 * Screen check.
	 *
	 * @var string
	 */
	const SCREEN_ANY = 'any';


	/**
	 * Contains optional arguments:
	 *
	 * - id:         The ID of the notification
	 * - type:       The notification type, i.e. 'updated' or 'error'
	 * - persistent: Option name to save dismissal information in.
	 * - screen:     Only display on plugin page or on every page.
	 * - classes:    If you need any extra class to style.
	 *
	 * @var array Options of this Notification.
	 */
	private $options = array();

	/**
	 * Internal flag for whether notifications has been displayed.
	 *
	 * @var bool
	 */
	private $displayed = false;

	/**
	 * Notification class constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $message Message string.
	 * @param  array  $options Set of options.
	 */
	public function __construct( $message, $options = array() ) {
		$this->message = $message;
		$this->options = wp_parse_args(
			$options,
			array(
				'id'         => '',
				'classes'    => '',
				'persistent' => false,
				'type'       => self::SUCCESS,
				'screen'     => self::SCREEN_ANY,
			)
		);
	}

	/**
	 * Adds string (view) behaviour to the Notification.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}

	/**
	 * Return the object properties as an array.
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function to_array() {
		return array(
			'message' => $this->message,
			'options' => $this->options,
		);
	}

	/**
	 * Return data from options.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $id ID to get option.
	 * @return mixed
	 */
	public function option( $id ) {
		return $this->options[ $id ];
	}

	/**
	 * Dismiss persisten notification.
	 *
	 * @since  1.0.0
	 */
	public function dismiss() {
		$this->displayed             = true;
		$this->options['persistent'] = '';
	}

	/**
	 * Renders the notification as a string.
	 *
	 * @since  1.0.0
	 *
	 * @return string The rendered notification.
	 */
	public function render() {
		$attributes = array();

		// Default notification classes.
		$classes = array(
			'notice',
			'notice-' . $this->option( 'type' ),
		);

		if ( ! empty( $this->option( 'classes' ) ) ) {
			$classes[] = $this->option( 'classes' );
		}

		if ( ! empty( $this->option( 'id' ) ) ) {
			$attributes[] = 'id="' . $this->option( 'id' ) . '"';
		}

		// Maintain WordPress visualisation of alerts when they are not persistent.
		if ( $this->is_persistent() ) {
			$classes[]    = 'is-dismissible';
			$attributes[] = 'data-key="' . $this->option( 'persistent' ) . '"';
			$attributes[] = 'data-security="' . wp_create_nonce( $this->option( 'id' ) ) . '"';
		}

		$attributes[] = 'class="' . join( ' ', $classes ) . '"';

		// Build the output DIV.
		return '<div' . join( ' ', $attributes ) . '>' . wpautop( $this->message ) . '</div>' . PHP_EOL;
	}

	/**
	 * Can display on current screen.
	 *
	 * @since  1.0.0
	 *
	 * @return bool
	 */
	public function can_display() {
		// Early Bail!!
		if ( $this->displayed || ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();
		if ( self::SCREEN_ANY === $this->option( 'screen' ) || false !== stristr( $screen->id, $this->option( 'screen' ) ) ) {
			$this->displayed = true;
		}

		return $this->displayed;
	}

	/**
	 * Is this Notification persistent.
	 *
	 * @since  1.0.0
	 *
	 * @return bool True if persistent, False if fire and forget.
	 */
	public function is_persistent() {
		return ! empty( $this->option( 'persistent' ) );
	}

	/**
	 * Is this notification displayed.
	 *
	 * @since  1.0.0
	 *
	 * @return bool
	 */
	public function is_displayed() {
		return $this->displayed;
	}
}
