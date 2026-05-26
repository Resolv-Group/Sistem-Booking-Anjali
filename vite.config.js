import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import { bunny } from "laravel-vite-plugin/fonts";

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '')

    return {
        plugins: [
            laravel({
                input: ["resources/css/app.css", "resources/js/app.js"],
                refresh: true,
                fonts: [
                    bunny("Manrope", {
                        weights: [300, 400, 500, 600, 700, 800],
                    }),
                ],
            }),
        ],
        server: {
            watch: {
                ignored: ["**/storage/framework/views/**"],
            },
            host: "0.0.0.0",
            hmr: {
                host: env.VITE_HMR_HOST || 'localhost',
            },
        },
    }
});
