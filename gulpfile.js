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

function buildDashboardCssDev() {
    return gulp.src('./dashboard/style/dashboard.scss')
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.init()))
        .pipe(sass().on('error', sass.logError))
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.write()))
        .pipe(gulp.dest('./web/css'));
}

function buildDashboardCss() {
    return gulp.src('./dashboard/style/dashboard.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(minifyCss())
        .pipe(gulp.dest('./web/css'));
}

function buildDashboardTemplate() {
    return gulp.src('./dashboard/app/**/*.html')
        .pipe(minifyHtml())
        .pipe(ngTemplate({
            filename: 'template.js',
            module: 'undine.dashboard.template',
            standalone: true
        }))
        .pipe(minifyJs())
        .pipe(gulp.dest('./web/js'));
}

function buildDashboardJs() {
    return gulp.src('./dashboard/app/**/*.js')
        .pipe(ngAnnotate())
        .pipe(gulp.dest('./web/js'));
}

function buildDashboardImage() {
    return gulp.src('./dashboard/image/**/*')
        .pipe(minifyImages())
        .pipe(rev())
        .pipe(gulp.dest('./web/image'));
}

function buildDashboardIndexDev(cb) {
    // Force the twig cache reload, since we changed an asset.
    fs.utimes(__dirname + '/app/Resources/views/dashboard/dev.html.twig', new Date(), new Date(), cb);
}

function buildDashboardIndex() {
    return gulp.src('./dashboard/dev.html.twig')
        .pipe(glob())
        .pipe(gulp.dest('./app/Resources/views/dashboard'))
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
    watch('./dashboard/style/**/*', gulp.series(buildDashboardCssDev, reloadComponent.bind(null, 'css')));
    watch('./dashboard/app/**/*.html', gulp.series(reloadComponent.bind(null, 'html')));
    watch('./dashboard/app/**/*.js', gulp.series(buildDashboardIndexDev, reloadComponent.bind(null, 'html')));
    watch('./dashboard/Resources/views/dashboard/dev.html.twig', gulp.series(reloadComponent.bind(null, 'html')));
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
    gulp.parallel(
        buildDashboardCss,
        buildDashboardTemplate,
        buildDashboardJs,
        buildDashboardImage,
        buildDashboardIndex
    )
);
