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
            // Tambahkan port bawaan Vite secara eksplisit agar ngrok tidak tersesat
            port: 5173,
            hmr: {
                // JIKA diakses lewat ngrok, paksa host HMR menggunakan domain ngrok itu sendiri
                host: process.env.NGROK_SUBDOMAIN
                    ? process.env.NGROK_SUBDOMAIN
                    : (env.VITE_HMR_HOST || 'localhost'),
                // Jika lewat ngrok, HMR wajib menggunakan port HTTPS (443)
                clientPort: process.env.NGROK_SUBDOMAIN ? 443 : 5173,
                protocol: process.env.NGROK_SUBDOMAIN ? 'wss' : 'ws',
            },
        },
    }
});
