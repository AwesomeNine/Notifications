# Notifications

[![Awesome9](https://img.shields.io/badge/Awesome-9-brightgreen)](https://awesome9.co)
[![Latest Stable Version](https://poser.pugx.org/awesome9/notifications/v/stable)](https://packagist.org/packages/awesome9/notifications)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/awesome9/notifications.svg)](https://packagist.org/packages/awesome9/notifications)
[![Total Downloads](https://poser.pugx.org/awesome9/notifications/downloads)](https://packagist.org/packages/awesome9/notifications)
[![License](https://poser.pugx.org/awesome9/notifications/license)](https://packagist.org/packages/awesome9/notifications)

<p align="center">
	<img src="https://img.icons8.com/nolan/256/appointment-reminders.png"/>
</p>

## 📃 About Notifications

This package provides ease of managing temporary and permanent notification within WordPress.

## 💾 Installation

``` bash
composer require awesome9/notifications
```

## 🕹 Usage

First, you need to spin out configuration for your notification center.

```php
Awesome9\Notifications\Center::get()
	->set_storage( 'awesome9_plugin_notifications' );  // Option name to be save persistent notifications in DB.
```

Now, let's add and remove some data to be output in admin.

```php
// This notification shows once its a temporary notification.
Awesome9\Notifications\Center::get()
	->add( 'Your message goes here.', array(
		'id'      => 'awesome9_some_id'
		'type'    => \Awesome9\Notifications\Notification::ERROR,
		'screen'  => 'post',
		'classes' => 'style-me-custom'
	) );

// Now let's add a persistent one which is dismissible by user.
Awesome9\Notifications\Center::get()
	->add( 'Your message goes here.', array(
		'id'         => 'awesome9_some_id_2'
		'type'       => \Awesome9\Notifications\Notification::ERROR,
		'persistent' => 'some_unique_Id',
		'screen'     => 'post',
		'classes'    => 'style-me-custom'
	) );

// Let's remove a notification.
Awesome9\Notifications\Center::get()
	->remove( 'awesome9_some_id' )
```

### Available options to pass in add function

| Option                           | Description
| -------------------------------- | -----------------------------------------------------------------------
| ```(string) id```                | Unique ID for the notification
| ```(string) type```              | Notification type to use i.e error, success, info, warning
| ```(bool, string) persistent```  | If you want a persistent notification pass a unique id
| ```(string) screen```            | Screen id to display on. Default: ```any```
| ```(string) classes```           | Any css classes you want to add on notification

### Helper functions

You can use the procedural approach as well:

```php
Awesome9\Notifications\add( $message, $options = array() );

Awesome9\Notifications\remove( $notification_id );
```

All the parameters remains the same as for the `JSON` class.

## 📖 Changelog

[See the changelog file](./CHANGELOG.md)
