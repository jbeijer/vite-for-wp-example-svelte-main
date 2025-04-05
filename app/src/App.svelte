<script>
	import svelteLogo from "./assets/svelte.svg";
	import viteLogo from "./assets/vite.svg";
	import Counter from "./lib/Counter.svelte";

	// Access the localized display text, provide fallback
	// Access the localized display text from the window object, provide fallback
	const displayContent =
		typeof window !== "undefined" &&
		typeof window.viteSvelteFrontendData !== "undefined" &&
		window.viteSvelteFrontendData?.displayText
			? window.viteSvelteFrontendData.displayText
			: "Default Text"; // Fallback text
</script>

<main>
	<div>
		<a href="https://vitejs.dev" target="_blank" rel="noreferrer">
			<img src={viteLogo} class="logo" alt="Vite Logo" />
		</a>
		<a href="https://svelte.dev" target="_blank" rel="noreferrer">
			<img src={svelteLogo} class="logo svelte" alt="Svelte Logo" />
		</a>
	</div>
	<h1>Vite + Svelte</h1>

	<div class="card">
		<Counter />
	</div>
	<p>{displayContent}</p>

	<p>
		Check out <a href="https://kit.svelte.dev/" target="_blank" rel="noreferrer"
			>SvelteKit</a
		>, the official Svelte app framework powered by Vite!
	</p>

	<p class="read-the-docs">Click on the Vite and Svelte logos to learn more</p>
</main>

<style>
	.card {
		padding: 1rem;
	}

	.logo {
		height: 6em;
		padding: 1.5em;
		will-change: filter;
		transition: filter 300ms;
	}

	.logo:hover {
		filter: drop-shadow(0 0 2em #646cffaa);
	}

	.logo.svelte:hover {
		filter: drop-shadow(0 0 2em #ff3e00aa);
	}

	.read-the-docs {
		color: #888;
	}
</style>
