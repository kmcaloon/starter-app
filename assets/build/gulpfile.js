/*-------------------------------------
    = MODULES
--------------------------------------*/
var gulp            = require( 'gulp' );
var gutil           = require( 'gulp-util' );
var sourcemaps      = require( 'gulp-sourcemaps' );
var map             = require( 'map-stream' );
var lazypipe        = require( 'lazypipe' );
var concatUtil      = require('gulp-concat-util');
var rename          = require( 'gulp-rename' );
var named           = require('vinyl-named');

// Error Handling
var plumber         = require( 'gulp-plumber' );
var notify          = require( 'gulp-notify' );

// Reloads
var browserSync     = require( 'browser-sync' ).create();
var reload          = browserSync.reload;

// Sass
var minifyCss       = require( 'gulp-cssnano' );
var autoprefixer    = require( 'gulp-autoprefixer' );
var sass            = require( 'gulp-sass' );
var sassGlob        = require( 'gulp-sass-glob' );
var globbing        = require( 'gulp-css-globbing' );
var concatCss       = require( 'gulp-concat-css' );

// JS
var uglify          = require( 'gulp-uglify' );
var webpack2        = require( 'webpack' );
var gulpWebpack     = require( 'gulp-webpack' );

// Images
var imagemin        = require( 'gulp-imagemin' ); 


/*-------------------------------------
    = SETTINGS
--------------------------------------*/
var localURL                = 'localhost/starter';
var rootDir                 = '../../'
var distDir                 = rootDir + 'dist/';
var stylesDir               = '../styles/';
var jsDir                   = '../scripts/';
var imgDir                  = '../images/';
var stylesDist              = distDir + 'styles/';
var jsDist                  = distDir + 'scripts/';
var imgDist                 = distDir + 'images/';

/*-----------------------------------------
    = ENTRY POINTS FOR PROCESSING
------------------------------------------*/
var stylesheets = ['main.scss', 'admin.scss'];
var styles_production = [];
var scripts = ['main.js'];
var scripts_production = [];
// Get full path for every script
var scripts_paths = [];
for( i = 0; i < scripts.length; i++ ) {
  scripts_paths.push( jsDir + scripts[i] );
}


/*-------------------------------------
    = FUNCTIONS
--------------------------------------*/

/**
*   Error Reporting
*/
var reportError = function( error ) {

  var lineNumber = ( error.lineNumber ) ? 'LINE ' + error.lineNumber + ' -- ' : '';

  notify({
      title: 'Task Failed [' + error.plugin + ']',
      message: lineNumber + 'See console.',
      sound: 'Sosumi' // See: https://github.com/mikaelbr/node-notifier#all-notification-options-with-their-defaults
  }).write( error );

  gutil.beep(); // Beep 'sosumi' again

  // Pretty error reporting
  var report = '';
  var chalk = gutil.colors.white.bgRed;

  report += chalk( 'TASK:' ) + ' [' + error.plugin + ']\n';
  report += chalk( 'PROB:' ) + ' ' + error.message + '\n';
  if ( error.lineNumber ) { report += chalk( 'LINE:' ) + ' ' + error.lineNumber + '\n'; }
  if ( error.fileName )   { report += chalk( 'FILE:' ) + ' ' + error.fileName + '\n'; }

  console.error( report );

  // Prevent the 'watch' task from stopping
  this.emit( 'end' );
}

/**
*   Process Styles
*/
var processStyles = lazypipe()

    .pipe( sassGlob )
    .pipe( globbing, {extensions: '.scss'} )
    .pipe( sass )
    .pipe( autoprefixer )
    .pipe( minifyCss, {zindex: 'false'});

/**
 * Init browsersync
 */
var browserOff = true;
var initBrowser = function() {

  if( ! browserSync.active && browserOff ) {

    browserSync.init({
      proxy: localURL,
      open: false,
      reloadOnRestart: true
    });

    browserOff = false;

  }
} 

/*-------------------------------------
  = TASKS
--------------------------------------*/

/**
*   Critical Styles 
*/
gulp.task( 'critical.scss', function() {

  return gulp.src( stylesDir + 'critical.scss' )
    .pipe( plumber({
        errorHandler: reportError
    }) )
    .on( 'error', reportError )
    .pipe( processStyles() )
    .pipe( concatUtil.header( '<style>' ) )
    .pipe( concatUtil.footer( '</style>' ) )
    // convert it to an include file
    .pipe( rename({
        basename: 'critical-styles',
        extname: '.html'
    }) )
    // insert file in the includes folder
    .pipe( gulp.dest( stylesDist ) )
    .pipe(browserSync.stream());

});

/** 
 * Stylesheets
 */
stylesheets.forEach( function( style ) {

  var production_task = style + '--production';

  /**
   * Process for development
   */
  gulp.task( style, function() {

    // Init browser
    initBrowser();

    // Watch this file
    gulp.watch( [stylesDir + '**/*'], [style] );

    // Process this file
    return gulp.src( stylesDir + style )
      .pipe( sourcemaps.init() )
      .pipe( plumber({
          errorHandler: reportError
      }) )
      .on( 'error', reportError )
      .pipe( processStyles() )
      .pipe( sourcemaps.write() )
      .pipe( gulp.dest(stylesDist) )
      .pipe( browserSync.stream() );

  });

  /**
   * Process for production (no sourcemaps)
   */
  gulp.task( production_task, function () {

     return gulp.src( stylesDir + style )
      .pipe( plumber({
          errorHandler: reportError
      }) )
      .on( 'error', reportError )
      .pipe( processStyles() )
      .pipe( gulp.dest( stylesDist ) );

  });

  styles_production.push( production_task );

});

gulp.task( 'process-styles--dev', stylesheets );
gulp.task( 'process-styles--production', styles_production );

/**
 * Scripts
 */
scripts.forEach( function( script ) {

  var script_name = script.replace( '.js', '' );
  var production_task = script + '--production';

  /**
   * For development
   */
  gulp.task( script, function () {

    // Init browser
    initBrowser();

    // Watch this file
    gulp.watch( jsDir + '**/*.js', [script] );

    return gulp.src( [jsDir + script] )
      .pipe( sourcemaps.init() )
      .pipe( named() )
      .pipe( gulpWebpack( require( './webpack.config.js' ), webpack2 ) )
      .pipe( sourcemaps.write( '/' ) )
      .pipe( gulp.dest( jsDist ) )
      .pipe(browserSync.stream());

  });

  /**
   * For production
   */
  gulp.task( production_task, function () {

    return gulp.src( [jsDir + script] )
      .pipe( gulpWebpack( require( './webpack.config.js' ), webpack2 ) )
      .pipe( uglify( {compress: {dead_code: true, unused: true, conditionals: true, sequences: true, booleans: true, join_vars: true}} ) )
      .pipe(rename({
        basename: script_name,
        extension: '.js'
      }))
      .pipe( gulp.dest( jsDist ) );

    
  });

  scripts_production.push( production_task) ;

});

/**
 * All scripts
 */
gulp.task( 'process-scripts--dev', function() {

  return gulp.src( scripts_paths )
    .pipe( named() )
    .pipe( gulpWebpack( require( './webpack.config.js' ), webpack2 ) )
    .pipe( gulp.dest( jsDist ) );

});
gulp.task( 'process-scripts--production', function() {

  return gulp.src( scripts_paths )
    .pipe( named() )
    .pipe( gulpWebpack( require( './webpack.config.js' ), webpack2 ) )
    .pipe( uglify( {compress: {dead_code: true, unused: true, conditionals: true, sequences: true, booleans: true, join_vars: true}} ) )
    .pipe( gulp.dest( jsDist ) );

});


/**
* Images
* - For optimizing images
*/
gulp.task( 'process-images', function() {
 
  return gulp.src( imgDir + '*.{svg,png,jpg,gif}' )
    .pipe( plumber({
      errorHandler: reportError
    }) )
    .on( 'error', reportError )
    .pipe( imagemin({
      optimizationLevel: 7,
      progressive: true
    }) )
    .pipe( gulp.dest( imgDist ) )
    .pipe(browserSync.stream());

});



/**
* Asset Processing
*/
//Dev
gulp.task( 'process-assets--dev', ['critical.scss', 'process-styles--dev', 'process-scripts--dev', 'process-images'] )

// Production
gulp.task( 'process-assets--production', ['critical.scss', 'process-styles--production', 'process-scripts--production', 'process-images'] );

/**
* Main Tasks
*/
// Dev
gulp.task( 'default', ['process-assets--dev'] );
// Production
gulp.task( 'production', ['process-assets--production'] );