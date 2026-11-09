const mix = require("laravel-mix");
const path = require("path");
let tailwindcss = require("tailwindcss");
require("dotenv").config();

// Mix 5 declara css-loader 1.x. Vue CLI/VuePress también instalan otras
// versiones: resolver desde Mix evita tomar accidentalmente la versión raíz.
// Afecta solo esta compilación; no cambia dependencias ni el diseño del login.
const mixCssLoader = require.resolve("css-loader", {
    paths: [path.dirname(require.resolve("laravel-mix/package.json"))],
});

// Webpack alias
mix.webpackConfig({
    resolveLoader: {
        alias: { "css-loader": mixCssLoader },
    },
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js/src"),
            "@assets": path.resolve(__dirname, "resources/assets"),
            "@public": path.resolve(__dirname, "public"),
            "@sass": path.resolve(__dirname, "resources/sass"),
            "@services": path.resolve(__dirname, "resources/js/src/services"),
            "@pages": path.resolve(__dirname, "resources/js/src/views/pages"),
        },
    },
});

// JavaScript
mix.js("resources/js/app.js", "public/js");

// Sass & CSS
mix.sass("resources/sass/app.scss", "public/css").options({
    // Extraer también los estilos Vue: evita mezclar dependencias CSS retiradas
    // del bundle inicial con estilos inline de rutas asíncronas (login/perfil).
    extractVueStyles: true,
    postCss: [require("autoprefixer"), require("postcss-rtl")()],
});

mix.postCss("resources/assets/css/main.css", "public/css", [
    tailwindcss("tailwind.js"),
    require("postcss-rtl")(), // Using PostCSS 7 compatible version
]);

// Copy assets
mix.copy("node_modules/vuesax/dist/vuesax.css", "public/css/vuesax.css");
mix.copy("resources/assets/css/iconfont.css", "public/css/iconfont.css");
mix.copyDirectory("resources/assets/fonts", "public/fonts");
mix.copyDirectory(
    "node_modules/material-icons/iconfont",
    "public/css/material-icons"
);
mix.copy(
    "node_modules/material-icons/iconfont/material-icons.css",
    "public/css/material-icons/material-icons.css"
);
mix.copy(
    "node_modules/prismjs/themes/prism-tomorrow.css",
    "public/css/prism-tomorrow.css"
);
mix.copyDirectory("resources/assets/images", "public/images");

// BrowserSync (optional, uncomment to use)
//mix.browserSync({
//  proxy: "http://app.aeternus:80", // Change to your local dev URL
//files: [
//  "public/css/**/*.css",
//"public/js/**/*.js",
// "resources/views/**/*.php",
//"resources/js/**/*.vue",
//],
// open: false,
//});

// Production vs Development
if (mix.inProduction()) {
    mix.version();
    mix.webpackConfig({
        output: {
            publicPath: "/",
            chunkFilename: "js/chunks/[name].[chunkhash].js",
        },
    });
    mix.setResourceRoot("/");
} else {
    mix.webpackConfig({
        output: {
            chunkFilename: "js/chunks/[name].js",
        },
    });
}

// Incluir estilos de todas las rutas asíncronas en los CSS iniciales ya enlazados
// por application.blade.php. No dejar helpers CSS requeridos desde chunks JS.
mix.override(config => {
    const ExtractTextPlugin = require("extract-text-webpack-plugin");
    config.plugins.forEach(plugin => {
        if (plugin instanceof ExtractTextPlugin) plugin.options.allChunks = true;
    });
});
