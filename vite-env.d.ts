/// <reference types="svelte" />
/// <reference types="vite/client" />
// Add this block to declare SVG files as modules
declare module "*.svg" {
	const content: any;
	export default content;
}
