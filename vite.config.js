import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwind from '@tailwindcss/vite'
import fs from 'fs'
import path from 'path'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            buildDirectory: 'build',
            refresh: true,
        }),
        tailwind(),
    ],
    server: {
        https: {
            key: fs.readFileSync(path.resolve(process.env.HOME, '.config/valet/Certificates/laravel-prism.test.key')),
            cert: fs.readFileSync(path.resolve(process.env.HOME, '.config/valet/Certificates/laravel-prism.test.crt')),
        },
        host: 'laravel-prism.test',
        port: 5173,
    },
})
