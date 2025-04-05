<?php
/**
 * Admin page functionality.
 *
 * @package Kucrut\ViteForWPExample\Svelte
 */

namespace Kucrut\ViteForWPExample\Svelte\Admin;

/**
 * Bootstrap admin functionality.
 *
 * @return void
 */
function bootstrap() : void {
	error_log('Vite Svelte Debug: Admin\\bootstrap function called.');
	\add_action( 'admin_menu', __NAMESPACE__ . '\register_admin_page' );
	// Use the general hook for enqueuing assets.
	\add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_admin_assets' ); // Changed hook
	\add_action( 'wp_ajax_vite_svelte_save_display_text', __NAMESPACE__ . '\vite_svelte_save_display_text_handler' );
}

/**
 * Register the admin menu page.
 *
 * @return void
 */
function register_admin_page() : void {
	\add_menu_page(
		'Vite Svelte Example',
		'Vite Svelte',
		'manage_options',
		'vite-svelte-example-admin',
		__NAMESPACE__ . '\render_admin_page'
	);
}

/**
 * Enqueue admin assets.
 *
 * Hooked to the general admin enqueue action.
 * Includes check for the specific admin page via hook suffix.
 *
 * @param string $hook_suffix The hook suffix for the current admin page.
 * @return void
 */
function enqueue_admin_assets( string $hook_suffix ) : void { // Changed signature
    error_log('Vite Svelte Debug: enqueue_admin_assets called (general hook). Hook suffix: ' . $hook_suffix);
    // For a toplevel page created with add_menu_page, the hook suffix is 'toplevel_page_{menu_slug}'
    $expected_suffix = 'toplevel_page_vite-svelte-example-admin';
    if ( $hook_suffix !== $expected_suffix ) {
        // Log if the suffix doesn't match, then exit the function.
        error_log('Vite Svelte Debug: Hook suffix mismatch. Expected: ' . $expected_suffix . '. Skipping enqueue.');
        return;
    }
    // Log if the suffix matches, then proceed with enqueuing.
    error_log('Vite Svelte Debug: Hook suffix matched. Enqueuing admin.js...');
    error_log('Vite functions available: ' . (function_exists('\\Kucrut\\Vite\\enqueue_asset') ? 'Yes' : 'No')); // Added debug log

    // Prepare data for the frontend script.
    $saved_text = \get_option( 'vite_svelte_display_text', 'Default Text' );
    $nonce      = \wp_create_nonce( 'vite_svelte_admin_nonce' );

    try {
        // Assuming \Vite\enqueue_asset is the correct namespace/function after potential refactoring.
        // If the Vite helper class/namespace changed, update this line accordingly.
        // Based on previous context, it might be \Kucrut\ViteForWP\Vite\enqueue_asset
        // Correct function call based on vendor package structure
        $script_handle = 'vite-svelte-example-admin-script'; // Define handle for reuse
        \Kucrut\Vite\enqueue_asset( dirname( __DIR__ ) . '/app/dist', 'app/src/admin.js', ['handle' => $script_handle] );
        error_log('Vite Svelte Debug: enqueue_asset successfully called for admin.js.');

        // Localize script with saved data and nonce.
        \wp_localize_script(
        	$script_handle,
        	'viteSvelteAdminData',
        	[
        		'savedText' => $saved_text,
        		'nonce'     => $nonce,
        		'ajaxUrl'   => \admin_url( 'admin-ajax.php' ), // Pass ajax url too
        	]
        );
        error_log('Vite Svelte Debug: wp_localize_script called for ' . $script_handle);
    } catch (\Exception $e) {
        error_log('Vite Svelte Debug: Error calling enqueue_asset: ' . $e->getMessage());
    }
}

/**
 * Render the admin page content.
 *
 * @return void
 */
function render_admin_page() : void {
	error_log('Vite Svelte Debug: render_admin_page called, about to output target div'); // Added debug log
	?>
	<div id="vite-svelte-admin-app"></div> <?php // Updated content
}

/**
	* Handles the AJAX request to save the display text.
	*
	* @return void
	*/
function vite_svelte_save_display_text_handler() : void {
	// 1. Verify nonce.
	if ( ! isset( $_POST['nonce'] ) || ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['nonce'] ) ), 'vite_svelte_admin_nonce' ) ) {
		\wp_send_json_error( [ 'message' => 'Nonce verification failed.' ], 403 );
		return;
	}

	// 2. Check user capabilities.
	if ( ! \current_user_can( 'manage_options' ) ) {
		\wp_send_json_error( [ 'message' => 'Insufficient permissions.' ], 403 );
		return;
	}

	// 3. Get and sanitize the input.
	if ( ! isset( $_POST['displayText'] ) ) {
		\wp_send_json_error( [ 'message' => 'Missing display text.' ], 400 );
		return;
	}
	$display_text = \sanitize_text_field( \wp_unslash( $_POST['displayText'] ) );

	// 4. Save the option.
	$updated = \update_option( 'vite_svelte_display_text', $display_text );

	if ( $updated ) {
		\wp_send_json_success( [ 'message' => 'Display text saved successfully.' ] );
	} else {
		// Check if the value was the same as the existing one. update_option returns false if the value is unchanged.
		$current_value = \get_option( 'vite_svelte_display_text' );
		if ( $current_value === $display_text ) {
			\wp_send_json_success( [ 'message' => 'Display text is already set to this value.' ] );
		} else {
			\wp_send_json_error( [ 'message' => 'Failed to save display text.' ], 500 );
		}
	}
}
