# [Starter App]()

Starter App is a WordPress starter theme that is designed to be used as a WordPress-based application for clients. Since its intended use is for individual clients vs. the theme marketplace, much functionality is meant to be built directly into the theme instead of delegated to seperate plugins (contrary to WordPress conventions).

**This is simply a starter framework that is constantly in flux and there are no intentions of supporting it!**

## Features

* PHP namespaces for cleaner organization 
* Various helper functions and settings
* Sass for stylesheets with loosely based SMACCS structure
* ES6 support for JavaScript
* [Gulp](http://gulpjs.com/) for compiling assets
* [Webpack](https://webpack.github.io/) for bundling JS within gulp
* [Browsersync](http://www.browsersync.io/) for synchronized browser testing


## Requirements

Make sure all dependencies have been installed before moving on:

* [PHP](http://php.net/manual/en/install.php) >= 5.6.4
* [Node.js](http://nodejs.org/) >= 6.9.x
* [Gulp](https://github.com/gulpjs/gulp/blob/master/docs/getting-started.md) >= 3.9.1
* [Yarn](https://yarnpkg.com/en/docs/install)

## Theme installation

Install the starter theme by using yarn from your custom theme's directory. Make sure that all dependencies are always installed within the assets/build directory:

```shell
# @ themes/your-theme-name/
$ yarn install --modules-folder ./assets/build/node_modules
```
For now, use a text editor to search through all of the theme files to find and replace all instances of `Starter` and `Starter App` with your custom theme's namespace.


## Theme structure

```shell
your-theme-name/   # → Root of your theme
├── assets                # → Front-end assets
│   ├── build/            # → Settings for compiling assets
│   ├── fonts/            # → Theme fonts
│   ├── images/           # → Theme images
│   ├── scripts/          # → Theme JS
│   ├── styles/           # → Theme stylesheets
│   └── vendor/           # → Third party assets         
├── dist/                 # → Built theme assets (never edit)
├── functions.php         # → Theme includes
├── index.php             # → Main template file
├── package.json          # → Node.js dependencies and scripts
├── screenshot.png        # → Theme screenshot for WP admin
├── includes/             # → Theme PHP
│   ├── acf.php           # → ACF helpers, settings and custom functionality
│   ├── assets.php        # → Theme styles, scripts, and fonts
│   ├── classes/          # → PHP classes go here
│   ├── dev.php           # → Dev helpers
│   ├── extras.php        # → Miscellaneous functions
│   ├── integrations.php  # → Third party integrations (might need to delegate them to classes)
│   ├── settings.php      # → Theme settings for use throughout app
│   ├── setup.php         # → Theme setup
│   ├── shortcodes/       # → Shortcodes go here
│   ├── theme-wrapper.php # → Theme wrapper
│   ├── widgets/          # → Widgets go here
│   └── woocommerce.php   # → Misc. woocommerce functions
├── page-templates/       # → Page templates
├── partials/             # → All partials/template parts
├── style.css             # → Theme meta information
├── partials/            # → Theme templates
└── vendor/               # → Composer packages (never edit)
```

## Theme setup

Edit `includes/setup.php` to enable or disable theme features, setup navigation menus, post thumbnail sizes, and sidebars.

## Theme development

Sage uses [Gulp](http://gulpjs.com/) and [Webpack](https://webpack.github.io/) to manage front-end assets.

### Install dependencies

From the command line on your host machine navigate to the theme directory then run `yarn`:

```shell
# @ themes/your-theme-name/
$ yarn install --module-folder ./assets/build/node_modules
```

You now have all the necessary dependencies to run the build process.

### Build setup
*  Within assets/build/gulpfile.js, make sure to specify your scss and js entry points on lines 54 and 56

### Build commands

* `gulp` — Compiles all assets for dev environent (sourcemaps/unuglified), sets up a watch task, and initiates browsersync
* `gulp production` — Compiles all assets for production
* Also, the system will create seperate tasks for each of the files you specify as your entry points, so if you can watch/process individual files according to your needs 

### Using Browsersync

To use Browsersync you need to set your `localURL` on line 41 within assets/build/gulpfile.js
