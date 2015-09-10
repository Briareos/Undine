/*******************************
         Check Install
*******************************/

var
  // node dependencies
  gulp         = require('gulp'),
  fs           = require('fs'),
  console      = require('better-console'),
  install      = require('./config/project/install')
;

// export task
module.exports = function() {

  setTimeout(function() {
    if( !install.isSetup() ) {
      console.log('Starting install...');
      gulp.series('install')();
      return;
    }
    else {
      gulp.series('watch')();
    }
  }, 50); // Delay to allow console.clear to remove messages from check event


};