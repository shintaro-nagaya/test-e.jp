/* eslint-disable no-param-reassign */
const Encore = require('@symfony/webpack-encore');
const path = require("path");
const ImageminWebpWebpackPlugin = require("imagemin-webp-webpack-plugin");

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public_html/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app/index.ts')
    .addEntry('admin', './assets/admin/index.ts')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    // .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()
    .addAliases({
        "~": path.resolve(__dirname, "./assets"),
        "@app": path.resolve(__dirname, "./assets/app"),
        "@admin": path.resolve(__dirname, "./assets/admin"),
        "@images": path.resolve(__dirname, "./assets/images"),
        "@scss": path.resolve(__dirname, "./assets/app/scss"),
        "@js": path.resolve(__dirname, "./assets/app/js")
    })
    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    // .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .copyFiles({
        from: "./assets/images",
        to: !Encore.isProduction() ? "images/[path][name].[ext]" : 'images/[path][name].[hash:8].[ext]',
        pattern: /\.(png|jpg|jpeg|svg)$/
    })
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/plugin-proposal-class-properties');
    // })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableSassLoader()
    .enablePostCssLoader()
    .autoProvidejQuery()

    .configureDevServerOptions((options) => {
      Object.assign(options, {
        hot: true,
        https: true,
        server: {
          type: "https",
          options: {
            pfx: path.join(process.env.HOME, ".symfony5/certs/default.p12"),
          },
        },
        static: {
          directory: path.resolve(__dirname, "preview"),
        },
        proxy: {
          "/": {
            target: "https://localhost:8000/",
            secure: false,
          },
        }
      });
      const lightGreenCode = "\x1b[92m";
      const lightAquaCode = "\x1b[96m";
      const resetColorCode = "\x1b[0m";
      console.log(
          `<i> ${lightGreenCode}[webpack-dev-server] ここにアクセスしてください: ${lightAquaCode}http://${options.host}${resetColorCode}/`
      );
    })
    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()
;
if(Encore.isProduction()) {
    Encore
        .addPlugin(
            new ImageminWebpWebpackPlugin({
                disable: true,//!Encore.isProduction(),
                config: [
                    {
                        test: /\.(jpe?g|png)/,
                        options: {
                            quality: 90,
                        },
                    },
                ],
                detailedLogs: false,
                silent: true,
            })
        );
}
module.exports = Encore.getWebpackConfig();
