var fs = require('fs');
var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var watch = require('gulp-watch');
var gulpIf = require('gulp-if');
var ws = require('ws');
var ngAnnotate = require('gulp-ng-annotate');
var ngTemplate = require('gulp-angular-templatecache');
var concat = require('gulp-concat');
var glob = require('gulp-glob-html');
var rev = require('gulp-rev');
var revReplace = require('gulp-rev-replace');
var filter = require('gulp-filter');
var autoprefixer = require('gulp-autoprefixer');
var del = require('del');
var debug = require('gulp-debug');

var minifyImages = require('gulp-imagemin');
var minifyCss = require('gulp-minify-css');
var minifyHtml = require('gulp-minify-html');
var minifyJs = require('gulp-uglify');

var noop = function () {
};

var config = {
    useSourceMaps: true,
    liveReloadPort: 48263
};

function clean(cb) {
    del([
        './var/tmp/**',
        '!./var/tmp',
        '!./var/tmp/.gitkeep',
        './web/css/**',
        '!./var/css',
        '!./web/css/.gitkeep',
        './web/js/**',
        '!./var/js',
        '!./web/js/.gitkeep',
        './web/image/**',
        '!./web/image',
        '!./web/image/.gitkeep'
    ], cb);
}

function buildDashboardImage() {
    return gulp.src('./dashboard/image/**/*')
        .pipe(minifyImages())
        .pipe(rev())
        .pipe(gulp.dest('./web/image'))
        .pipe(rev.manifest('rev-image.json'))
        .pipe(gulp.dest('./var/tmp'));
}

function buildDashboardCssDev() {
    return gulp.src('./dashboard/style/dashboard.scss')
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.init()))
        .pipe(sass().on('error', sass.logError))
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.write()))
        .pipe(gulp.dest('./web/css'));
}

function buildDashboardCss() {
    var filterScss = filter('**.scss', {restore: true});

    return gulp.src([
        './dashboard/style/dashboard.scss',
        './dashboard/bower_components/semantic-ui/dist/semantic.min.css'
    ])
        .pipe(filterScss)
        .pipe(sass().on('error', sass.logError))
        .pipe(revReplace({manifest: gulp.src('./var/tmp/rev-image.json')}))
        .pipe(minifyCss())
        .pipe(filterScss.restore)
        .pipe(concat('dashboard.css'))
        .pipe(rev())
        .pipe(gulp.dest('./web/css'))
        .pipe(rev.manifest('rev-css.json'))
        .pipe(gulp.dest('./var/tmp'));
}

function buildDashboardJs() {
    var localFilter = filter('app/**/*.js', {restore: true});
    var htmlFilter = filter('app/**/*.html', {restore: true});
    var vendorFilter = filter('bower_components/**/*.js', {restore: true});

    return gulp.src([
        './dashboard/bower_components/jquery/dist/jquery.min.js',
        './dashboard/bower_components/angularjs/angular.min.js',
        './dashboard/bower_components/angular-ui-router/release/angular-ui-router.min.js',
        './dashboard/bower_components/semantic-ui/dist/semantic.min.js',
        './dashboard/app/**/*.html',
        './dashboard/app/app.js',
        './dashboard/app/states.js',
        './dashboard/app/run.js',
        './dashboard/app/*/**/*.js'
    ], {base: './dashboard'})
        .pipe(vendorFilter)
        .pipe(concat('vendor.js'))
        .pipe(vendorFilter.restore)
        .pipe(htmlFilter)
        .pipe(minifyHtml())
        .pipe(ngTemplate({
            filetitle: 'template.js',
            module: 'undine.dashboard.template',
            standalone: true
        }))
        .pipe(minifyJs())
        .pipe(htmlFilter.restore)
        .pipe(localFilter)
        .pipe(ngAnnotate())
        .pipe(concat('app.js'))
        .pipe(minifyJs())
        .pipe(localFilter.restore)
        .pipe(concat('dashboard.js'))
        .pipe(rev())
        .pipe(gulp.dest('./web/js'))
        .pipe(rev.manifest('rev-dashboard.json'))
        .pipe(gulp.dest('./var/tmp'));
}

function buildDashboardIndexDev(cb) {
    // Force the twig cache reload.
    fs.utimes(__dirname + '/app/Resources/views/dashboard/dev.html.twig', new Date(), new Date(), cb);
}

function buildDashboardIndex(cb) {
    // Force the twig cache reload.
    fs.utimes(__dirname + '/app/Resources/views/dashboard/prod.html.twig', new Date(), new Date(), cb);
}

var reload = noop;

function server() {
    var wss = new ws.Server({port: config.liveReloadPort});

    wss.on('connection', function connection(socket) {
        reload = function (what) {
            socket.send(what);
        };
        socket.on('close', function () {
            reload = noop;
        });
    });
}

function reloadComponent(component) {
    reload(component);
}


function watchDev() {
    watch('./dashboard/image/**/*', gulp.series(reloadComponent.bind(null, 'html')));
    watch('./dashboard/style/**/*', gulp.series(buildDashboardCssDev, reloadComponent.bind(null, 'css')));
    watch('./dashboard/app/**/*.html', gulp.series(reloadComponent.bind(null, 'html')));
    watch('./dashboard/app/**/*.js', gulp.series(buildDashboardIndexDev, reloadComponent.bind(null, 'html')));
    watch('./app/Resources/views/dashboard/dev.html.twig', gulp.series(reloadComponent.bind(null, 'html')));
}

gulp.task('default',
    gulp.series(
        gulp.parallel(
            buildDashboardCssDev,
            buildDashboardIndexDev
        ),
        gulp.parallel(
            server,
            watchDev
        )));

gulp.task('build',
    gulp.series(
        clean,
        buildDashboardImage,
        gulp.parallel(
            buildDashboardCss,
            buildDashboardJs,
            buildDashboardIndex
        )
    )
);
