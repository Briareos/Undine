var gulp = require('gulp');

module.exports = function () {
    var args = Array.prototype.slice.call(arguments);

    var tasks = args.map(function (arg) {
        if (Array.isArray(arg)) {
            return gulp.parallel.apply(gulp, arg);
        } else {
            return arg;
        }
    });

    return gulp.series(tasks)();
};
