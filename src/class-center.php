<?php
/**
 * The notification center.
 *
 * @since   1.0.0
 * @package Awesome9\Notifications
 * @author  Awesome9 <me@awesome9.co>
 */

namespace Awesome9\Notifications;

/**
 * Center class.
 */
class Center {

	/**
	 * Option name to store notifications in.
	 *
	 * @var Storage
	 */
	private $storage = '';

	/**
	 * Retrieve main instance.
	 *
	 * Ensure only one instance is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return Center
	 */
	public static function get() {
		static $instance;

		if ( is_null( $instance ) && ! ( $instance instanceof Center ) ) {
			$instance = new Center();
		}

		return $instance;
	}

	/**
	 * Bind all hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return Center
	 */
	public function hooks() {
		$this->storage->hooks();

		add_action( 'all_admin_notices', [ $this, 'display' ] );
		add_action( 'admin_footer', [ $this, 'print_javascript' ] );

		add_action( 'wp_ajax_wp_helpers_notice_dismissible', [ $this, 'notice_dismissible' ] );
		return $this;
	}

	/**
	 * Set storage.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $name Storage option name.
	 * @return Center
	 */
	public function set_storage( $name ) {
		$this->storage = new Storage( $name );
		return $this;
	}

	/**
	 * Add notification
	 *
	 * @since 1.0.0
	 *
	 * @param  string $message Message string.
	 * @param  array  $options Set of options.
	 * @return Center
	 */
	public function add( $message, $options = [] ) {
		$this->storage->add( $message, $options );
		return $this;
	}

	/**
	 * Remove the notification by ID
	 *
	 * @since 1.0.0
	 *
	 * @param  string $notification_id The ID of the notification to search for.
	 * @return Notification Instance of delete notification.
	 */
	public function remove( $notification_id ) {
		return $this->storage->remove( $notification_id );
	}

	/**
	 * Display the notifications.
	 *
	 * @since  1.0.0
	 */
	public function display() {
		// Never display notifications for network admin.
		if ( $this->is_network_admin() ) {
			return;
		}

		foreach ( $this->storage->get_notifications() as $notification ) {
			if ( $notification->can_display() ) {
				echo $notification; // phpcs:ignore
			}
		}
	}

	/**
	 * Print JS for dismissile.
	 *
	 * @codeCoverageIgnore
	 */
	public function print_javascript() {
		?>
		<script>
			;(function($) {
				$( '.is-dismissible' ).on( 'click', '.notice-dismiss', function() {
					var notice = $( this ).parent()

					$.ajax({
						url: ajaxurl,
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'wp_helpers_notice_dismissible',
							security: notice.data( 'security' ),
							notificationId: notice.attr( 'id' )
						}
					});
				});
			})(jQuery);
		</script>
		<?php
	}

	/**
	 * Dismiss persistent notice.
	 *
	 * @codeCoverageIgnore
	 */
	public function notice_dismissible() {
		$notification_id = filter_input( INPUT_POST, 'notificationId' );
		check_ajax_referer( $notification_id, 'security' );

		$notification = $this->storage->remove( $notification_id );

		/**
		 * Filter: 'awesome9_notification_dismissed' - Allows developer to perform action after dismissed.
		 *
		 * @param Notification[] $notifications
		 */
		do_action( 'awesome9_notification_dismissed', $notification_id, $notification );
	}

	/**
	 * Check if is network admin.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return bool
	 */
	private function is_network_admin() {
		return function_exists( 'is_network_admin' ) && is_network_admin();
	}
}
