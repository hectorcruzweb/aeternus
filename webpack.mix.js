const mix = require("laravel-mix");
let tailwindcss = require("tailwindcss");
require("dotenv").config();
mix.webpackConfig({
   /*devServer: {
        proxy: {
            "*": "http://app.aeternus:80"
        }
    }
        */
});
mix.browserSync({
    proxy:'http://app.aeternus:80'
});
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js("resources/js/app.js", "public/js")
    .webpackConfig({
        resolve: {
            alias: {
                "@": path.resolve(__dirname, "resources/js/src"),
                "@assets": path.resolve(__dirname, "resources/assets"),
                "@public": path.resolve(__dirname, "public"),
                "@sass": path.resolve(__dirname, "resources/sass"),
                "@services": path.resolve(
                    __dirname,
                    "resources/js/src/services"
                ),
                "@pages": path.resolve(
                    __dirname,
                    "resources/js/src/views/pages"
                )
            }
        }
    })
    .copy(["resources/assets/css/w3.css"], "public/css/w3.css")
    .sass("resources/sass/app.scss", "public/css")
    .options({
        postCss: [require("autoprefixer"), require("postcss-rtl")]
    })
    .postCss("resources/assets/css/main.css", "public/css", [
        tailwindcss("tailwind.js"),
        require("postcss-rtl")()
    ])
    .copy("node_modules/vuesax/dist/vuesax.css", "public/css/vuesax.css") // Vuesax framework css
    .copy("resources/assets/css/iconfont.css", "public/css/iconfont.css") // Feather Icon Font css
    .copyDirectory("resources/assets/fonts", "public/fonts") // Feather Icon fonts
    .copyDirectory(
        "node_modules/material-icons/iconfont",
        "public/css/material-icons"
    ) // Material Icon fonts
    .copyDirectory(
        "node_modules/material-icons/iconfont/material-icons.css",
        "public/css/material-icons/material-icons.css"
    ) // Material Icon fonts css
    .copy(
        "node_modules/prismjs/themes/prism-tomorrow.css",
        "public/css/prism-tomorrow.css"
    ) // Prism Tomorrow theme css
    .copyDirectory("resources/assets/images", "public/images"); // Copy all images from resources to public folder

// Change below options according to your requirement
if (mix.inProduction()) {
    mix.version();
    mix.webpackConfig({
        output: {
            publicPath: "/",
            chunkFilename: "js/chunks/[name].[chunkhash].js"
        }
    });
    mix.setResourceRoot("/");
} else {
    mix.webpackConfig({
        output: {
            chunkFilename: "js/chunks/[name].js"
        }
    });
}
