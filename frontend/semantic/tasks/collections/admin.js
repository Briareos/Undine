/*******************************
     Admin Task Collection
*******************************/

/*
  This are tasks to be run by project maintainers
  - Creating Component Repos
  - Syncing with GitHub via APIs
  - Modifying package files
*/

/*******************************
             Tasks
*******************************/


module.exports = function(gulp) {
  var
    // less/css distributions
    initComponents      = require('../admin/components/init'),
    createComponents    = require('../admin/components/create'),
    updateComponents    = require('../admin/components/update'),

    // single component releases
    initDistributions   = require('../admin/distributions/init'),
    createDistributions = require('../admin/distributions/create'),
    updateDistributions = require('../admin/distributions/update'),

    release             = require('../admin/release'),
    publish             = require('../admin/publish'),
    register            = require('../admin/register')
  ;

  /* Release */
  initDistributions.description = 'Grabs each component from GitHub';
  createDistributions.description = 'Updates files in each repo';
  initComponents.description = 'Grabs each component from GitHub';
  createComponents.description = 'Updates files in each repo';

  gulp.task('init distributions', initDistributions);
  gulp.task('create distributions', createDistributions);
  gulp.task('init components', initComponents);
  gulp.task('create components', createComponents);

  /* Publish */
  updateDistributions.description = 'Commits component updates from create to GitHub';
  updateComponents.description = 'Commits component updates from create to GitHub';

  gulp.task('update distributions', updateDistributions);
  gulp.task('update components', updateComponents);

  /* Tasks */
  release.description = 'Stages changes in GitHub repos for all distributions';
  release.publish = 'Publishes all releases (components, package)';
  release.register = 'Registers all packages with NPM';

  gulp.task('release', release);
  gulp.task('publish', publish);
  gulp.task('register', register);

};