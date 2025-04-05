/// <reference types="svelte" />
/// <reference types="vite/client" />
// Add this block to declare SVG files as modules
declare module "*.svg" {
	const content: any;
	export default content;
}
// Declare the global variable injected by WordPress
interface ViteSvelteFrontendData {
	displayText?: string; // Make optional in case it's not always present
	// Add other properties if known
}

declare global {
	interface Window {
		viteSvelteFrontendData?: ViteSvelteFrontendData;
	}
}
