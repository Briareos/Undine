/*******************************
 Set-up
 *******************************/

var
    gulp         = require('gulp'),

    // read user config to know what task to load
    config       = require('./tasks/config/user'),

    // watch changes
    watch        = require('./tasks/watch'),

    // build all files
    build        = require('./tasks/build'),
    buildJS      = require('./tasks/build/javascript'),
    buildCSS     = require('./tasks/build/css'),
    buildAssets  = require('./tasks/build/assets'),

    // utility
    clean        = require('./tasks/clean'),
    version      = require('./tasks/version'),

    // docs tasks
    serveDocs    = require('./tasks/docs/serve'),
    buildDocs    = require('./tasks/docs/build'),

    // rtl
    buildRTL     = require('./tasks/rtl/build'),
    watchRTL     = require('./tasks/rtl/watch')
    ;


/*******************************
 Tasks
 *******************************/

watch.description = 'Watch for site/theme changes';
build.description = 'Builds all files from source';
buildJS.description = 'Builds all javascript from source';
buildCSS.description = 'Builds all css from source';
buildAssets.description = 'Copies all assets from source';
clean.description = 'Clean dist folder';
version.description = 'Displays current version of Semantic';

gulp.task('default', gulp.series('watch'));

gulp.task('watch', watch);

gulp.task('build', build);
gulp.task('build-javascript', buildJS);
gulp.task('build-css', buildCSS);
gulp.task('build-assets', buildAssets);

gulp.task('clean', clean);
gulp.task('version', version);

/*--------------
 Docs
 ---------------*/

/*
 Lets you serve files to a local documentation instance
 https://github.com/Semantic-Org/Semantic-UI-Docs/
 */

serveDocs.description = 'Serve file changes to SUI Docs';
buildDocs.description = 'Build all files and add to SUI Docs';

gulp.task('serve-docs', serveDocs);
gulp.task('build-docs', buildDocs);


/*--------------
 RTL
 ---------------*/

if(config.rtl) {
  watchRTL.description = 'Build all files as RTL';
  buildRTL.description = 'Watch files as RTL';

  gulp.task('watch-rtl', watchRTL);
  gulp.task('build-rtl', buildRTL);
}