import { fileURLToPath, URL } from "node:url"

import { defineConfig } from "vite"
import vue from "@vitejs/plugin-vue"
import importMetaEnv from "@import-meta-env/unplugin"

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [
        vue(),
        importMetaEnv.vite({
            example: ".env.example.public",
        }),
    ],
    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./src", import.meta.url)),
        },
    },
    envPrefix: "TGA_",
})
