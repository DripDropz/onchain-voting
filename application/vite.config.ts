import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import topLevelAwait from "vite-plugin-top-level-await";
import wasm from "vite-plugin-wasm";
import { resolve } from "path";
import { createReadStream, existsSync } from "fs";

const env = loadEnv(
    'all',
    process.cwd()
);

export default defineConfig({
    build: {
        target: "esnext",
        rollupOptions: {
            external: [
                "lucid-cardano",
                // "momentum-modal"
            ]
        }
    },
    optimizeDeps: {
        exclude: [
            "lucid-cardano",
            // "momentum-modal"
        ]
    },
    server: {
        host: '0.0.0.0',
        hmr: {
            host: env.VITE_DOCKER_GATEWAY_HOST ?? 'localhost',
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
        topLevelAwait(),
        {
            name: "cardano-wasm-dev-server",
            configureServer(server) {
                const wasmSources: Record<string, string> = {
                    "cardano_multiplatform_lib_bg.wasm": resolve(
                        process.cwd(),
                        "node_modules/lucid-cardano/esm/src/core/libs/cardano_multiplatform_lib/cardano_multiplatform_lib_bg.wasm"
                    ),
                    "cardano_message_signing_bg.wasm": resolve(
                        process.cwd(),
                        "node_modules/lucid-cardano/esm/src/core/libs/cardano_message_signing/cardano_message_signing_bg.wasm"
                    ),
                };

                server.middlewares.use((req, res, next) => {
                    const url = req.url ?? "";
                    const filename = url.split("/").pop() ?? "";
                    const sourcePath = wasmSources[filename];

                    if (sourcePath && existsSync(sourcePath)) {
                        res.setHeader("Content-Type", "application/wasm");
                        createReadStream(sourcePath).pipe(res);
                        return;
                    }

                    next();
                });
            },
        },
    ],
    worker: {
        format: "es",
        plugins: () => [wasm(), topLevelAwait()],
    },
    resolve: {
        alias: {
            ziggy: "/vendor/tightenco/ziggy/src/js",
            "ziggy-vue": "/vendor/tightenco/ziggy/dist/vue",
            "@": "/resources/js",
            "@lucid-cardano": "/node_modules/lucid-cardano/web/mod.js",
            "@laravel-vapor":"/node_modules/laravel-vapor/dist/laravel-vapor.js"
        },
    },
});
