import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'Modules/Setting/resources/assets/js/settings.js',
                'Modules/User/resources/assets/js/users.js',
                'Modules/Authorization/resources/assets/js/authorization.js',
                'Modules/Blog/resources/assets/js/blog.js',
                'Modules/Page/resources/assets/js/page.js',
                'Modules/Brand/resources/assets/js/brand.js',
                'Modules/Product/resources/assets/js/product.js',
                'Modules/Order/resources/assets/js/statuses.js',
                'Modules/Order/resources/assets/js/orders.js',
                'Modules/Order/resources/assets/js/order-show.js',
                'Modules/Order/resources/assets/js/order-edit.js',
                'Modules/Order/resources/assets/js/order-show.js',
                'Modules/Order/resources/assets/js/order-edit.js',
                'Modules/Post/resources/assets/js/post.js',
                'Modules/Post/resources/assets/js/package.js',
                'Modules/Post/resources/assets/js/type.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
