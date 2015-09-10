var gulp = require('gulp');

module.exports = {
    start: function (task, callback) {
        if (!callback) {
            return gulp.series(task)();
        } else {
            return gulp.series(task, function(done) {
                done();
                callback();
            })();
        }
    },
    src: function () {
        return gulp.src.apply(gulp, arguments);
    },
    dest: function () {
        return gulp.dest.apply(gulp, arguments);
    },
    pipe: function () {
        return gulp.pipe.apply(gulp, arguments);
    },
    task: function (name, description, fn) {
        if (typeof description === 'string' && fn !== undefined) {
            fn.description = description;
            gulp.task(name, fn);
        } else {
            gulp.task(name, fn = description);
        }
    },
    watch: function (glob, opts, task) {
        if (typeof opts === 'function') {
            task = opts;
            opts = {};
        }

        var emitter = gulp.watch(glob, opts, function (done) {
            done();
        });

        emitter.on('change', function (file) {
            task(file);
        });

        return emitter;
    }
};
