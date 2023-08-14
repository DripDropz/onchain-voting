import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import topLevelAwait from "vite-plugin-top-level-await";
import wasm from "vite-plugin-wasm";

export default defineConfig({
    build: {
        target: "esnext",
        rollupOptions: {
            external: [
                "lucid-cardano"
            ]
        }
    },
    optimizeDeps: {
        exclude: [
            "lucid-cardano"
        ]
    },
    server: {
        host: '0.0.0.0',
        hmr: {
            host: process.env.DOCKER_GATEWAY_HOST ?? '0.0.0.0',
        }
    },
    plugins: [
        laravel({
            input: "resources/js/app.ts",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        wasm(),
        topLevelAwait()
    ],
    worker: {
        format: "es",
        plugins: [wasm(), topLevelAwait()],
    },
    resolve: {
        alias: {
            "@inertiajs/inertia-vue":
                "/node_modules/@inertiajs/inertia-vue/src/index.js",
            ziggy: "/vendor/tightenco/ziggy/src/js",
            "ziggy-vue": "/vendor/tightenco/ziggy/dist/vue",
            "@": "/resources/js",
            "@lucid-cardano": "/node_modules/lucid-cardano/web/mod.js",
            // "@lucid-cardano": "/node_modules/lucid-cardano/types/src/mod.d.ts",
        },
    },
});
