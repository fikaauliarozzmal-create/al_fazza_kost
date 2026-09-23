import os from 'node:os';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

function getLanIp() {
    const interfaces = os.networkInterfaces();

    const preferredInterfaces = [
        'wlo2',
        'wlan0',
        'eth0',
        'enp0s3',
    ];

    for (const name of preferredInterfaces) {
        const addresses = interfaces[name] || [];

        const ipv4 = addresses.find(
            (address) =>
                address.family === 'IPv4' &&
                !address.internal
        );

        if (ipv4) {
            return ipv4.address;
        }
    }

    for (const addresses of Object.values(interfaces)) {
        const ipv4 = (addresses || []).find(
            (address) =>
                address.family === 'IPv4' &&
                !address.internal
        );

        if (ipv4) {
            return ipv4.address;
        }
    }

    return '127.0.0.1';
}

const lanIp = getLanIp();

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/style.css',
                'resources/js/app.js',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],

    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,

        origin: `http://${lanIp}:5173`,

        hmr: {
            host: lanIp,
            port: 5173,
        },

        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
