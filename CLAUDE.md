# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Build Commands
- `pnpm run dev` - Start Vite development server
- `pnpm run build` - Build production assets with Vite
- `pnpm run preview` - Preview the build locally
- `composer install` - Install PHP dependencies

## Code Style Guidelines

### JavaScript/Svelte
- Use ESM module system (`type: "module"`)
- Follow TypeScript standards with `checkJs: true`
- Use Svelte 4 component API for compatibility
- Use descriptive variable/function names
- Import organization: external libs first, then local modules
- Error handling: Log errors with descriptive prefixes

### PHP
- Use strict types (`declare(strict_types = 1)`)
- Follow WordPress coding standards
- Namespace: `Kucrut\ViteForWPExample\Svelte`
- Include type declarations on function returns
- Use WordPress hook patterns for integrations
- Document functions with PHPDoc comments

### Project Structure
- Keep frontend (app/src/) and PHP (inc/) code separate
- Assets are built to app/dist/ directory
- Use WordPress hook system for proper integration