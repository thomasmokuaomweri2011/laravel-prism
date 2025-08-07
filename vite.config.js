import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwind from '@tailwindcss/vite'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      buildDirectory: 'build',
      refresh: true,
    }),
    tailwind(),
  ],
})
