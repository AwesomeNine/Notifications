<?php
/**
 * The notification storage.
 *
 * @since   1.0.0
 * @package Awesome9\Notifications
 * @author  Awesome9 <me@awesome9.co>
 */

namespace Awesome9\Notifications;

/**
 * Storage class
 */
class Storage {

	/**
	 * Option name.
	 *
	 * @var string
	 */
	private $option_name = null;

	/**
	 * Internal flag for whether notifications have been retrieved from storage.
	 *
	 * @var bool
	 */
	private $retrieved = false;

	/**
	 * Notifications.
	 *
	 * @var Notification[]
	 */
	private $notifications = array();

	/**
	 * The constructor
	 *
	 * @since 1.0.0
	 *
	 * @param  string $option_name Option name to store notification in.
	 */
	public function __construct( $option_name ) {
		$this->option_name = $option_name;
	}

	/**
	 * Bind all hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return Storage
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'get_from_storage' ) );
		add_action( 'shutdown', array( $this, 'update_storage' ) );
		return $this;
	}

	/**
	 * Add notification
	 *
	 * @since 1.0.0
	 *
	 * @param  string $message Message string.
	 * @param  array  $options Set of options.
	 * @return Storage
	 */
	public function add( $message, $options = array() ) {
		if ( isset( $options['id'] ) && ! is_null( $this->get_by_id( $options['id'] ) ) ) {
			return;
		}

		$this->notifications[] = new Notification(
			$message,
			$options
		);

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
		$notification = $this->get_by_id( $notification_id );
		if ( ! is_null( $notification ) ) {
			$notification->dismiss();
		}

		return $notification;
	}

	/**
	 * Get the notification by ID
	 *
	 * @param  string $notification_id The ID of the notification to search for.
	 * @return null|Notification
	 */
	public function get_by_id( $notification_id ) {
		foreach ( $this->notifications as $notification ) {
			if ( $notification_id === $notification->option( 'id' ) ) {
				return $notification;
			}
		}

		return null;
	}

	/**
	 * Get notifications.
	 *
	 * @since 1.0.0
	 *
	 * @return Notification[] Registered notifications.
	 */
	public function get_notifications() {
		return $this->notifications;
	}

	/**
	 * Retrieve the notifications from storage
	 *
	 * @since 1.0.0
	 *
	 * @return array Notification[] Notifications
	 */
	public function get_from_storage() {
		if ( $this->retrieved ) {
			return;
		}

		$this->retrieved = true;
		$notifications   = get_option( $this->option_name );

		// Check if notifications are stored.
		if ( empty( $notifications ) ) {
			return;
		}

		if ( is_array( $notifications ) ) {
			foreach ( $notifications as $notification ) {
				$this->notifications[] = new Notification(
					$notification['message'],
					$notification['options']
				);
			}
		}
	}

	/**
	 * Save persistent or transactional notifications to storage.
	 *
	 * We need to be able to retrieve these so they can be dismissed at any time during the execution.
	 *
	 * @since 1.0.0
	 */
	public function update_storage() {
		$notifications = array_filter( $this->notifications, array( $this, 'remove_notification' ) );

		// No notifications to store, clear storage.
		if ( empty( $notifications ) ) {
			delete_option( $this->option_name );
			return;
		}

		$notifications = array_map( array( $this, 'notification_to_array' ), $notifications );

		// Save the notifications to the storage.
		update_option( $this->option_name, $notifications );
	}

	/**
	 * Remove notification after it has been displayed.
	 *
	 * @since 1.0.0
	 *
	 * @param  Notification $notification Notification to remove.
	 * @return bool
	 */
	public function remove_notification( Notification $notification ) {
		if ( ! $notification->is_displayed() ) {
			return true;
		}

		if ( $notification->is_persistent() ) {
			return true;
		}

		return false;
	}


	/**
	 * Convert Notification to array representation
	 *
	 * @since 1.0.0
	 *
	 * @param  Notification $notification Notification to convert.
	 * @return array
	 */
	private function notification_to_array( Notification $notification ) {
		return $notification->to_array();
	}
}
