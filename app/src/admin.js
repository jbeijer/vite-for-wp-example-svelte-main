console.log('Vite Svelte Debug: admin.js script started');
import AdminApp from './AdminApp.svelte';

console.log('Vite Svelte Debug: Attempting to mount AdminApp...');
const targetElement = document.getElementById('vite-svelte-admin-app');
console.log('Vite Svelte Debug: Target element found:', targetElement);

const app = new AdminApp({
  target: targetElement,
});

console.log('Vite Svelte Debug: AdminApp mount attempted.');

export default app;
