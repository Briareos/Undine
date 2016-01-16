var fs = require('fs');
var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var watch = require('gulp-watch');
var gulpIf = require('gulp-if');
var ws = require('ws');
var concat = require('gulp-concat');
var rev = require('gulp-rev');
var revReplace = require('gulp-rev-replace');
var filter = require('gulp-filter');
var autoprefixer = require('gulp-autoprefixer');
var tsc = require('gulp-typescript');
var del = require('del');
var debug = require('gulp-debug');
var less = require('gulp-less');
var minifyImages = require('gulp-imagemin');
var minifyCss = require('gulp-cssnano');
var minifyJs = require('gulp-uglify');

var tscOptions = {
    typescript: require('typescript'),
    target: 'ES5',
    module: 'system',
    experimentalDecorators: true,
    moduleResolution: 'node',
    sortOutput: true,
    emitDecoratorMetadata: true
};

var lessOptions = {paths: ['./node_modules/semantic-ui-less']};

var noop = function (cb) {
    if (typeof cb === 'function') {
        cb();
    }
};

var config = {
    useSourceMaps: true,
    liveReloadPort: 48263
};

function cleanDev(cb) {
    del([
        './var/tmp/css/**',
        './var/tmp/js/**',
        './var/tmp/font/**',
        './var/tmp/themes/**'
    ], cb);
}

function copyTheme() {
    // Semantic looks for a hardcoded theme.config file, so use this hack.
    return gulp.src('./frontend/semantic/theme.config')
        .pipe(gulp.dest('./node_modules/semantic-ui-less'));
}

function clean(cb) {
    del([
        './var/tmp/**',
        '!./var/tmp',
        '!./var/tmp/.gitkeep',
        './web/css/**',
        '!./web/css',
        '!./web/css/.gitkeep',
        './web/js/**',
        '!./web/js',
        '!./web/js/.gitkeep',
        './web/image/**',
        '!./web/image',
        '!./web/image/.gitkeep',
        './web/themes/**',
        '!./web/themes',
        '!./web/themes/.gitkeep'
    ], cb);
}

function buildImage() {
    return gulp.src('./frontend/image/**/*')
        .pipe(minifyImages())
        .pipe(rev())
        .pipe(gulp.dest('./web/image'))
        .pipe(rev.manifest('rev-image.json'))
        .pipe(gulp.dest('./var/tmp'));
}

function buildDashboardCssDev() {
    return gulp.src('./frontend/dashboard/style/dashboard.scss')
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.init()))
        .pipe(sass().on('error', sass.logError))
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.write()))
        .pipe(gulp.dest('./var/tmp/css/dashboard'));
}

function buildAdminCssDev() {
    return gulp.src('./frontend/admin/style/admin.scss')
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.init()))
        .pipe(sass().on('error', sass.logError))
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.write()))
        .pipe(gulp.dest('./var/tmp/css/admin'));
}

function buildWebCssDev() {
    return gulp.src('./frontend/web/style/web.scss')
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.init()))
        .pipe(sass().on('error', sass.logError))
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.write()))
        .pipe(gulp.dest('./var/tmp/css/web'));
}

function buildDashboardCss() {
    var filterLess = filter('**.less', {restore: true});
    var filterScss = filter('**.scss', {restore: true});

    return gulp.src([
            './frontend/dashboard/style/dashboard.scss',
            './frontend/semantic/semantic.less'
        ])
        .pipe(filterLess)
        .pipe(less(lessOptions))
        .pipe(filterLess.restore)
        .pipe(filterScss)
        .pipe(sass().on('error', sass.logError))
        .pipe(revReplace({manifest: gulp.src('./var/tmp/rev-image.json')}))
        .pipe(filterScss.restore)
        .pipe(concat('dashboard.css'))
        .pipe(minifyCss())
        .pipe(rev())
        .pipe(gulp.dest('./web/css/dashboard'))
        .pipe(rev.manifest('rev-css-dashboard.json'))
        .pipe(gulp.dest('./var/tmp'));
}

function buildAdminCss() {
    var filterLess = filter('**.less', {restore: true});
    var filterScss = filter('**.scss', {restore: true});

    return gulp.src([
            './frontend/admin/style/admin.scss',
            './frontend/semantic/semantic.less'
        ])
        .pipe(filterLess)
        .pipe(less(lessOptions))
        .pipe(filterLess.restore)
        .pipe(filterScss)
        .pipe(sass().on('error', sass.logError))
        .pipe(revReplace({manifest: gulp.src('./var/tmp/rev-image.json')}))
        .pipe(filterScss.restore)
        .pipe(concat('admin.css'))
        .pipe(minifyCss())
        .pipe(rev())
        .pipe(gulp.dest('./web/css/admin'))
        .pipe(rev.manifest('rev-css-admin.json'))
        .pipe(gulp.dest('./var/tmp'));
}

function buildWebCss() {
    var filterLess = filter('**.less', {restore: true});
    var filterScss = filter('**.scss', {restore: true});

    return gulp.src([
            './frontend/admin/style/admin.scss',
            './frontend/semantic/semantic.less'
        ])
        .pipe(filterLess)
        .pipe(less(lessOptions))
        .pipe(filterLess.restore)
        .pipe(filterScss)
        .pipe(sass().on('error', sass.logError))
        .pipe(revReplace({manifest: gulp.src('./var/tmp/rev-image.json')}))
        .pipe(filterScss.restore)
        .pipe(concat('web.css'))
        .pipe(minifyCss())
        .pipe(rev())
        .pipe(gulp.dest('./web/css/web'))
        .pipe(rev.manifest('rev-css-web.json'))
        .pipe(gulp.dest('./var/tmp'));
}

function buildDashboardTypescriptDev() {
    return gulp.src('./frontend/dashboard/app/app.ts', {base: './frontend/dashboard/app'})
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.init()))
        .pipe(tsc(Object.assign(tscOptions, {outFile: 'dashboard.js'})))
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.write()))
        .pipe(gulp.dest('./var/tmp/js/dashboard'));
}

function buildAdminTypescriptDev() {
    return gulp.src('./frontend/admin/app/app.ts', {base: './frontend/admin'})
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.init()))
        .pipe(tsc(Object.assign(tscOptions, {outFile: 'admin.js'})))
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.write()))
        .pipe(gulp.dest('./var/tmp/js/admin'));
}

function buildWebTypescriptDev() {
    return gulp.src('./frontend/web/app/app.ts', {base: './frontend/web'})
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.init()))
        .pipe(tsc(Object.assign(tscOptions, {outFile: 'web.js'})))
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.write()))
        .pipe(gulp.dest('./var/tmp/js/web'));
}

function buildDashboardTypescript() {
    var typescriptFilter = filter('app/**/*.ts', {restore: true});
    var vendorFilter = filter('../../node_modules/**/*.js', {restore: true});

    return gulp.src([
            './node_modules/angular2/bundles/angular2-polyfills.js',
            './node_modules/systemjs/dist/system-register-only.js',
            './node_modules/rxjs/bundles/Rx.min.js',
            './node_modules/angular2/bundles/angular2.min.js',
            './node_modules/angular2/bundles/router.min.js',
            './frontend/dashboard/app/**/*.ts'
        ], {base: './frontend/dashboard'})
        .pipe(vendorFilter)
        .pipe(concat('vendor.js'))
        .pipe(vendorFilter.restore)
        .pipe(typescriptFilter)
        .pipe(tsc(Object.assign(tscOptions, {outFile: 'app.js'})))
        .pipe(minifyJs())
        .pipe(typescriptFilter.restore)
        .pipe(concat('dashboard.js'))
        .pipe(rev())
        .pipe(gulp.dest('./web/js/dashboard'))
        .pipe(rev.manifest('rev-js-dashboard.json'))
        .pipe(gulp.dest('./var/tmp'));
}

function buildAdminTypescript() {
    var typescriptFilter = filter('admin/app/**/*.ts', {restore: true});
    var vendorFilter = filter('../../bower_components/**/*.js', {restore: true});

    return gulp.src([
            './node_modules/systemjs/dist/system-register-only.js',
            './node_modules/angular2/bundles/angular2.min.js',
            './node_modules/jquery/dist/jquery.min.js',
            './admin/app/**/*.ts'
        ], {base: './frontend'})
        .pipe(vendorFilter)
        .pipe(concat('vendor.js'))
        .pipe(vendorFilter.restore)
        .pipe(minifyJs())
        .pipe(typescriptFilter)
        .pipe(tsc(Object.assign(tscOptions, {outFile: 'admin.js'})))
        .pipe(minifyJs())
        .pipe(typescriptFilter.restore)
        .pipe(concat('admin.js'))
        .pipe(rev())
        .pipe(gulp.dest('./web/js/admin'))
        .pipe(rev.manifest('rev-js-admin.json'))
        .pipe(gulp.dest('./var/tmp'));
}

function buildWebTypescript() {
    var typescriptFilter = filter('web/app/**/*.ts', {restore: true});
    var vendorFilter = filter('../../bower_components/**/*.js', {restore: true});

    return gulp.src([
            './node_modules/systemjs/dist/system-register-only.js',
            './node_modules/angular2/bundles/angular2.min.js',
            './node_modules/jquery/dist/jquery.min.js',
            './node_modules/semantic-ui-visibility/visibility.min.js',
            './node_modules/semantic-ui-sidebar/sidebar.min.js',
            './node_modules/semantic-ui-transition/transition.min.js',
            './frontend/web/app/**/*.ts'
        ], {base: './frontend'})
        .pipe(vendorFilter)
        .pipe(concat('vendor.js'))
        .pipe(vendorFilter.restore)
        .pipe(typescriptFilter)
        .pipe(tsc({
            typescript: require('typescript'),
            target: 'ES5',
            module: 'system',
            experimentalDecorators: true,
            moduleResolution: 'node',
            sortOutput: true,
            emitDecoratorMetadata: true,
            outFile:'web.js'
        }))
        .pipe(minifyJs())
        .pipe(typescriptFilter.restore)
        .pipe(concat('web.js'))
        .pipe(rev())
        .pipe(gulp.dest('./web/js/web'))
        .pipe(rev.manifest('rev-js-web.json'))
        .pipe(gulp.dest('./var/tmp'));
}

function buildDashboardIndex(cb) {
    // Force the twig cache reload.
    fs.utimes(__dirname + '/app/Resources/views/dashboard/app.html.twig', new Date(), new Date(), cb);
}

function buildAdminIndex(cb) {
    // Force the twig cache reload.
    fs.utimes(__dirname + '/app/Resources/views/admin/layout.html.twig', new Date(), new Date(), cb);
}

function buildWebIndex(cb) {
    // Force the twig cache reload.
    fs.utimes(__dirname + '/app/Resources/views/web/layout.html.twig', new Date(), new Date(), cb);
}

function buildSemanticCssDev() {
    return gulp.src('./frontend/semantic/semantic.less')
        .pipe(less(lessOptions))
        .pipe(gulp.dest('./var/tmp/css/semantic'));
}

function buildSemanticTheme() {
    return gulp.src('./node_modules/semantic-ui-less/themes/default/assets/**')
        .pipe(gulp.dest('./web/themes/default/assets'));
}

function buildSemanticThemeDev() {
    return gulp.src('./node_modules/semantic-ui-less/themes/default/assets/**')
        .pipe(gulp.dest('./var/tmp/themes/default/assets'));
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
    watch('./frontend/semantic/**/*', gulp.series(buildSemanticCssDev, copyTheme, reloadComponent.bind(null, 'css')));

    watch('./frontend/image/**/*', gulp.series(reloadComponent.bind(null, 'html')));

    watch('./frontend/dashboard/style/**/*', gulp.series(buildDashboardCssDev, reloadComponent.bind(null, 'css')));
    watch('./frontend/dashboard/app/**/*.ts', gulp.series(buildDashboardTypescriptDev, reloadComponent.bind(null, 'html')));
    watch('./app/Resources/views/dashboard/app.html.twig', gulp.series(reloadComponent.bind(null, 'html')));

    watch('./frontend/admin/style/**/*', gulp.series(buildAdminCssDev, reloadComponent.bind(null, 'css')));
    watch('./frontend/admin/app/**/*.ts', gulp.series(buildAdminTypescriptDev, reloadComponent.bind(null, 'html')));
    watch('./app/Resources/views/admin/layout.html.twig', gulp.series(reloadComponent.bind(null, 'html')));

    watch('./frontend/web/style/**/*', gulp.series(buildWebCssDev, reloadComponent.bind(null, 'css')));
    watch('./frontend/web/app/**/*.ts', gulp.series(buildWebTypescriptDev, reloadComponent.bind(null, 'html')));
    watch('./app/Resources/views/web/layout.html.twig', gulp.series(reloadComponent.bind(null, 'html')));
}

gulp.task('build-dev',
    gulp.series(
        cleanDev,
        copyTheme,
        gulp.parallel(
            buildSemanticCssDev,
            buildSemanticThemeDev,

            buildDashboardCssDev,
            buildDashboardTypescriptDev,
            buildDashboardIndex,

            buildAdminCssDev,
            buildAdminTypescriptDev,
            buildAdminIndex,

            buildWebCssDev,
            buildWebTypescriptDev,
            buildWebIndex
        )
    ));

gulp.task('build',
    gulp.series(
        clean,
        copyTheme,
        buildImage,
        gulp.parallel(
            buildSemanticTheme,

            buildDashboardCss,
            buildDashboardTypescript,
            buildDashboardIndex,

            buildAdminCss,
            buildAdminTypescript,
            buildAdminIndex,

            buildWebCss,
            buildWebTypescript,
            buildWebIndex
        )
    )
);

gulp.task('default', gulp.series(
    'build-dev',
    gulp.parallel(
        server,
        watchDev
    )
));
