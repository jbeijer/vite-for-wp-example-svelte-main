<?php
/**
 * Plugin Name: Vite for WP example: Svelte
 * Description: A plugin to demonstrate Vite for WP integration.
 * Author: Dzikri Aziz
 * Author URI: https://dz.aziz.im
 * License: GPLv2
 * Version: 0.0.1
 */

namespace Kucrut\ViteForWPExample\Svelte;

// Removed autoload from here

/**
 * Initialize the plugin.
 *
 * Loads the necessary files and bootstraps the frontend and admin functionalities.
 * Hooked to 'plugins_loaded' to ensure it runs after all plugins are loaded.
 *
 * @since 0.0.2
 */
function initialize_plugin() {
	error_log('Vite Svelte Debug: Autoloader path: ' . __DIR__ . '/vendor/autoload.php');
	error_log('Vite Svelte Debug: Autoloader exists: ' . (file_exists(__DIR__ . '/vendor/autoload.php') ? 'Yes' : 'No'));

	// Moved autoload inside the function, as the first require
	require_once __DIR__ . '/vendor/autoload.php';
	require_once __DIR__ . '/inc/frontend.php';
	require_once __DIR__ . '/inc/admin.php';
// Test script to check function availability
try {
	if (function_exists('\\Kucrut\\ViteForWP\\Vite\\enqueue_asset')) {
		error_log('Vite Svelte Debug: enqueue_asset function exists');
	} else {
		error_log('Vite Svelte Debug: enqueue_asset function MISSING');
	}
} catch (\Exception $e) {
	error_log('Vite Svelte Debug: Error checking function existence: ' . $e->getMessage());
}

Frontend\bootstrap();
Admin\bootstrap();
}

\add_action( 'plugins_loaded', __NAMESPACE__ . '\\initialize_plugin' );
