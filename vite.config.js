import { v4wp } from "@kucrut/vite-for-wp";
import { svelte } from "@sveltejs/vite-plugin-svelte";

// https://vitejs.dev/config/
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
		v4wp({ input: ["app/src/main.js", "app/src/admin.js"], outDir: "app/dist" }),
	],
	server: {
		origin: "http://localhost:5173", // Sets the origin for generated asset URLs
		// Explicitly set CORS headers
		headers: {
			"Access-Control-Allow-Origin": "*", // Allow requests from any origin
		},
		// Optional: Define specific port if needed, though Vite usually handles this
		// port: 5174,
		// strictPort: true,
	},
};
