# WordPress Plugin with Svelte and Vite: Structure and Build Process

## Introduction/Overview

This document outlines the structure and build process for a WordPress plugin that utilizes Svelte for the frontend and Vite for the build tooling. It leverages the `@kucrut/vite-for-wp` package, which acts as a crucial bridge, providing both a Vite plugin for the JavaScript build process and PHP helper functions to integrate the generated assets seamlessly into WordPress.

## Project Structure

The plugin follows a standard structure, separating PHP backend code from the Svelte frontend application:

*   **`/` (Root Directory)**
    *   `plugin.php`: The main WordPress plugin file. It handles plugin activation, deactivation, and includes necessary PHP files, including the Composer autoloader.
    *   `vite.config.js`: Configuration file for Vite, defining the build process, input/output paths, and Vite plugins, including the one provided by `@kucrut/vite-for-wp`.
    *   `package.json`: Defines Node.js dependencies (Svelte, Vite, plugins) and scripts for development and building (`npm run dev`, `npm run build`).
    *   `svelte.config.js`: Configuration specific to the Svelte compiler and integration with Vite.
    *   `composer.json`: Defines PHP dependencies managed by Composer. Crucially, this includes the `@kucrut/vite-for-wp` package, which provides the necessary PHP functions for WordPress integration. Running `composer install` reads this file and installs the required PHP libraries into the `vendor/` directory.
    *   `README.md`: Project documentation.
    *   `.gitignore`, `.editorconfig`: Standard project configuration files.
*   **`app/`**: Contains the frontend Svelte application.
    *   `app/src/`: Source code for the Svelte app.
        *   `main.js`: The entry point for the Svelte application. It imports the root component (`App.svelte`) and mounts it to the DOM.
        *   `App.svelte`: The main Svelte component for the application.
        *   `assets/`: Static assets like images or fonts used by the Svelte app.
        *   `lib/`: Reusable Svelte components.
*   **`inc/`**: Contains the PHP code for the WordPress plugin integration.
    *   `frontend.php`: Handles enqueuing the Vite-built JavaScript and CSS assets on the WordPress frontend using helper functions provided by `@kucrut/vite-for-wp`, and defines the HTML element where the Svelte app will be mounted.
*   **`vendor/`**: (Created by Composer) Contains the installed PHP dependencies, including `@kucrut/vite-for-wp`.

## Frontend Build Process

The frontend build is managed by Vite, configured via `vite.config.js`.

*   **Configuration (`vite.config.js`):**
    *   Specifies `app/src/main.js` as the entry point and `app/dist/` as the output directory.
    *   Uses `@sveltejs/vite-plugin-svelte` to process Svelte files.
    *   Uses the `v4wp` plugin from `@kucrut/vite-for-wp` to handle integration aspects like generating the correct asset paths for WordPress.

    ```js
    // vite.config.js
    import { v4wp } from "@kucrut/vite-for-wp";
    import { svelte } from "@sveltejs/vite-plugin-svelte";

    export default {
        plugins: [
            svelte({
                compilerOptions: {
                    // Use Svelte 4 component API for compatibility
                    compatibility: {
                        componentApi: 4,
                    },
                },
            }),
            // Configures Vite for WordPress integration using the v4wp plugin
            v4wp({
                input: "app/src/main.js", // Frontend entry point
                outDir: "app/dist"        // Output directory for built assets
            }),
        ],
        server: {
            origin: "http://test2.local", // Adjust to your local dev URL
            headers: {
                "Access-Control-Allow-Origin": "*",
            },
        },
    };
    ```

*   **Scripts (`package.json`):**
    *   Provides commands for development (`dev`) and production builds (`build`).

    ```json
    // package.json
    "scripts": {
      "dev": "vite",          // Starts the Vite development server with HMR
      "build": "vite build",    // Creates an optimized production build
      "preview": "vite preview" // Previews the production build locally
    }
    ```

## WordPress Integration

The PHP code in the `inc/` directory, along with the `@kucrut/vite-for-wp` package installed via Composer, bridges the gap between WordPress and the Vite/Svelte frontend.

*   **The Role of `@kucrut/vite-for-wp` and Composer:**
    *   The `@kucrut/vite-for-wp` package serves two primary purposes:
        1.  **Vite Plugin:** Provides the `v4wp` plugin used in `vite.config.js` to configure Vite for WordPress development (e.g., handling asset paths, manifest generation).
        2.  **PHP Helper Functions:** Offers PHP functions (like `Vite\enqueue_asset()`) to easily enqueue the correct assets (from the dev server or build directory) within WordPress hooks.
    *   **Composer (`composer.json`)** is used to manage the PHP dependencies. It specifies the requirement for `@kucrut/vite-for-wp`:

        ```json
        // composer.json (require section)
        "require": {
            "kucrut/vite-for-wp": "^0.9.1"
        }
        ```
    *   Running `composer install` downloads this package (and any other PHP dependencies) into the `vendor/` directory and sets up the autoloader (`vendor/autoload.php`). This makes the package's PHP classes and functions available to the plugin's PHP code.

*   **Plugin Initialization (`plugin.php`):**
    *   Includes Composer's autoloader (`vendor/autoload.php`) which is essential for loading the `kucrut/vite-for-wp` classes and functions.
    *   Includes the frontend integration logic (`inc/frontend.php`).
    *   Calls the `Frontend\bootstrap()` function to set up WordPress hooks.

    ```php
    <?php
    // plugin.php

    namespace Kucrut\ViteForWPExample\Svelte;

    // Ensure Composer dependencies are loaded (makes Vite\enqueue_asset available)
    require_once __DIR__ . '/vendor/autoload.php';

    // Include the frontend integration logic
    require_once __DIR__ . '/inc/frontend.php';

    // Initialize the frontend hooks defined in inc/frontend.php
    Frontend\bootstrap();
    ```

*   **Asset Enqueuing & App Rendering (`inc/frontend.php`):**
    *   The `Frontend\bootstrap()` function registers hooks for `wp_enqueue_scripts` and `wp_footer`.
    *   `Frontend\enqueue_script()` uses `Vite\enqueue_asset()` (made available by Composer and `@kucrut/vite-for-wp`) to intelligently load the correct JavaScript/CSS assets – either from the Vite development server or the production build directory (`app/dist/`).
    *   `Frontend\render_app()` outputs the target `<div>` element in the footer where the Svelte app will mount.

    ```php
    <?php
    // inc/frontend.php

    namespace Kucrut\ViteForWPExample\Svelte\Frontend;

    use Kucrut\Vite; // Namespace provided by the Composer package

    /**
     * Sets up WordPress action hooks.
     */
    function bootstrap(): void {
        // Hook for enqueuing scripts and styles
        add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_script' );
        // Hook for rendering the app container in the footer
        add_action( 'wp_footer', __NAMESPACE__ . '\\render_app' );
    }

    /**
     * Enqueues the Vite-managed script using the vite-for-wp helper function.
     * This function handles loading from the dev server or build output automatically.
     */
    function enqueue_script(): void {
        Vite\enqueue_asset(
            dirname( __DIR__ ) . '/app/dist', // Path to Vite build output directory
            'app/src/main.js',              // Entry point relative to source code root
            [
                'handle' => 'vite-for-wp-svelte', // Optional: script handle
                'in-footer' => true,             // Load script in the footer
            ]
        );
    }

    /**
     * Renders the HTML div where the Svelte app will be mounted.
     */
    function render_app(): void {
        ?>
        <div id="v4wp-app-svelte" class="v4wp-app-svelte"></div>
        <?php
    }
    ```

*   **Svelte App Mounting (`app/src/main.js`):**
    *   The JavaScript entry point imports the main `App.svelte` component.
    *   It creates a new Svelte app instance, targeting the `#v4wp-app-svelte` element rendered by the PHP code (`inc/frontend.php`).

    ```js
    // app/src/main.js
    import App from "./App.svelte"; // Import the root Svelte component
    import "./app.css";           // Import global styles

    // Create a new Svelte app instance
    const app = new App({
        // Target the div rendered by inc/frontend.php
        target: document.getElementById("v4wp-app-svelte"),
    });

    export default app;
    ```

## Development Workflow

1.  Run `npm install` (or `pnpm install`, `yarn install`) to install frontend Node.js dependencies defined in `package.json`.
2.  Run `composer install` to install PHP dependencies (like `@kucrut/vite-for-wp`) defined in `composer.json`. This creates the `vendor/` directory and autoloader.
3.  Run `npm run dev` to start the Vite development server. This server provides Hot Module Replacement (HMR) for frontend assets.
4.  Activate the plugin in WordPress.
5.  Visit a page on your WordPress site where the plugin is active. The `Vite\enqueue_asset()` function will detect the running dev server and load assets from it. Changes made to Svelte components will be reflected instantly via HMR.

## Production Build

1.  Run `npm run build` to generate optimized static assets in the `app/dist/` directory. Vite creates minified JS/CSS files and a manifest file.
2.  Ensure `composer install` has been run.
3.  Ensure the plugin is active in WordPress.
4.  When the Vite dev server is *not* running, `Vite\enqueue_asset()` automatically uses the manifest file in `app/dist/` to load the optimized production assets.
