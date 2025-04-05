<script context="module" lang="ts">
  // Declare the global variable provided by WordPress localization
  // This makes TypeScript aware of the object injected by wp_localize_script
  declare global {
    interface Window {
      viteSvelteAdminData: {
        savedText: string;
        nonce: string;
        ajaxUrl: string;
      };
    }
  }
</script>

<script lang="ts">
  import { onMount } from 'svelte';

  let displayText = '';
  let statusMessage = ''; // To provide feedback
  let isLoading = true; // Indicate loading state

  // These will be populated by localized data
  let nonce = '';
  let ajaxUrl = '';

  onMount(() => {
    // Access the global variable from the window object
    if (typeof window.viteSvelteAdminData !== 'undefined') {
      displayText = window.viteSvelteAdminData.savedText || '';
      nonce = window.viteSvelteAdminData.nonce;
      ajaxUrl = window.viteSvelteAdminData.ajaxUrl;
      isLoading = false;
    } else {
      console.error('viteSvelteAdminData is not defined.');
      statusMessage = 'Error: Could not load initial data.';
      isLoading = false;
    }
  });

  async function saveText() {
    if (!nonce || !ajaxUrl) {
      statusMessage = 'Error: Configuration data missing.';
      console.error('Nonce or AJAX URL is missing.');
      return;
    }

    statusMessage = 'Saving...'; // Provide immediate feedback

    const body = new URLSearchParams();
    body.append('action', 'vite_svelte_save_display_text');
    body.append('nonce', nonce);
    body.append('displayText', displayText);

    try {
      const response = await fetch(ajaxUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: body.toString(), // Convert URLSearchParams to string
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();

      if (result.success) {
        statusMessage = 'Text saved successfully!';
        // Optionally clear the message after a few seconds
        setTimeout(() => statusMessage = '', 3000);
      } else {
        statusMessage = `Error saving text: ${result.data?.message || 'Unknown error'}`;
        console.error('Save failed:', result.data);
      }
    } catch (error) {
      statusMessage = `Error saving text: ${error.message}`;
      console.error('Fetch error:', error);
    }
  }
</script>

<div class="svelte-admin-app-container">
  <h1>Svelte Admin App</h1>
  <p>This content is rendered by Svelte! test</p>

  <div>
    <label for="displayText">Text to display on frontend:</label>
    <input type="text" id="displayText" bind:value={displayText} disabled={isLoading} />
    <button on:click={saveText} disabled={isLoading}>Save Text</button>
    {#if statusMessage}
      <p class="status-message">{statusMessage}</p>
    {/if}
  </div>
</div>

<style>
  .svelte-admin-app-container {
    /* Reset margin/padding to avoid interfering with WP admin layout */
    margin: 0;
    padding: 20px; /* Added padding */
  }

  .svelte-admin-app-container h1 {
    color: blue;
    /* Reset default browser margins */
    margin-top: 0;
    margin-bottom: 0; /* Adjust if needed for spacing */
  }

  .svelte-admin-app-container p {
    /* Reset default browser margins */
    margin-top: 0;
    margin-bottom: 0; /* Adjust if needed for spacing */
  }

  .status-message {
      margin-top: 10px;
      font-style: italic;
  }
</style>
