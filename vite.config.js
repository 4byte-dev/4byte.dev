import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";
import path from "path";

export default ({ mode }) => {
	process.env = { ...process.env, ...loadEnv(mode, process.cwd()) };

	return defineConfig({
		server: {
			watch: {
				ignored: ["**/vendor/**", "**/node_modules/**"],
			},
		},
		build: {
			rollupOptions: {
				output: {
					manualChunks: {
						"react-vendor": ["react", "react-dom"],
						"inertia-vendor": ["@inertiajs/react"],
						"radix-vendor": ["radix-ui", "@radix-ui/react-accordion"],
					},
				},
			},
		},
		plugins: [
			laravel({
				input: ["resources/js/app.jsx", "resources/css/app.css"],
				ssr: "resources/js/ssr.jsx",
				refresh: true,
			}),
			react(),
			// visualizer()
		],
		resolve: {
			alias: {
				"@": "/resources/js",
				"@Modules": path.resolve(__dirname, "modules"),
				"@Article": path.resolve(__dirname, "modules/Article/resources/js"),
				"@Category": path.resolve(__dirname, "modules/Category/resources/js"),
				"@Course": path.resolve(__dirname, "modules/Course/resources/js"),
				"@Entry": path.resolve(__dirname, "modules/Entry/resources/js"),
				"@Page": path.resolve(__dirname, "modules/Page/resources/js"),
				"@React": path.resolve(__dirname, "modules/React/resources/js"),
				"@Recommend": path.resolve(__dirname, "modules/Recommend/resources/js"),
				"@Search": path.resolve(__dirname, "modules/Search/resources/js"),
				"@Tag": path.resolve(__dirname, "modules/Tag/resources/js"),
				"@User": path.resolve(__dirname, "modules/User/resources/js"),
				"@CodeSpace": path.resolve(__dirname, "modules/CodeSpace/resources/js"),
				"ziggy-js": path.resolve("vendor/tightenco/ziggy"),
			},
		},
		base: process.env.VITE_BASE_URL ? process.env.VITE_BASE_URL + "/build/" : "/build/",
	});
};
