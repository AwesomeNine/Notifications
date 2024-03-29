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
 * Add notification
 *
 * @since 1.0.0
 *
 * @param  string $message Message string.
 * @param  array  $options Set of options.
 * @return Center
 */
function add( $message, $options = [] ) {
	return Center::get()->add( $message, $options );
}

/**
 * Remove the notification by ID
 *
 * @since 1.0.0
 *
 * @param  string $notification_id The ID of the notification to search for.
 * @return Notification Instance of delete notification.
 */
function remove( $notification_id ) {
	return Center::get()->remove( $notification_id );
}
