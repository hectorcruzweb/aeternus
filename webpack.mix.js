const mix = require("laravel-mix");
const path = require("path");
let tailwindcss = require("tailwindcss");
require("dotenv").config();

// Webpack alias
mix.webpackConfig({
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
mix.browserSync({
    proxy: "http://app.aeternus:80", // Change to your local dev URL
    files: [
        "public/css/**/*.css",
        "public/js/**/*.js",
        "resources/views/**/*.php",
        "resources/js/**/*.vue",
    ],
    open: false,
});

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
