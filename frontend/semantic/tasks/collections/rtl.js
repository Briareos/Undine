/*******************************
        Define Sub-Tasks
*******************************/

module.exports = function(gulp) {

  var
    // rtl
    buildRTL     = require('./../rtl/build'),
    watchRTL     = require('./../rtl/watch')
  ;

  watchRTL.description = 'Build all files as RTL';
  buildRTL.description = 'Watch files as RTL';

  gulp.task('watch-rtl', watchRTL);
  gulp.task('build-rtl', buildRTL);

};
