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
var typescript = require('gulp-typescript');
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

function cleanDev(cb) {
    del([
        './var/tmp/css/**',
        './var/tmp/js/**'
    ], cb);
}

function clean(cb) {
    del([
        './var/tmp/rev-*.json',
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
    var filterScss = filter('**.scss', {restore: true});

    return gulp.src([
        './frontend/dashboard/style/dashboard.scss',
        './frontend/bower_components/semantic-ui/dist/semantic.min.css'
    ])
        .pipe(filterScss)
        .pipe(sass().on('error', sass.logError))
        .pipe(revReplace({manifest: gulp.src('./var/tmp/rev-image.json')}))
        .pipe(minifyCss())
        .pipe(filterScss.restore)
        .pipe(concat('dashboard.css'))
        .pipe(rev())
        .pipe(gulp.dest('./web/css/dashboard'))
        .pipe(rev.manifest('rev-css-dashboard.json'))
        .pipe(gulp.dest('./var/tmp'));
}

function buildAdminCss() {
    var filterScss = filter('**.scss', {restore: true});

    return gulp.src([
        './frontend/admin/style/admin.scss',
        './frontend/bower_components/semantic-ui/dist/semantic.min.css'
    ])
        .pipe(filterScss)
        .pipe(sass().on('error', sass.logError))
        .pipe(revReplace({manifest: gulp.src('./var/tmp/rev-image.json')}))
        .pipe(minifyCss())
        .pipe(filterScss.restore)
        .pipe(concat('admin.css'))
        .pipe(rev())
        .pipe(gulp.dest('./web/css/admin'))
        .pipe(rev.manifest('rev-css-admin.json'))
        .pipe(gulp.dest('./var/tmp'));
}

function buildWebCss() {
    var filterScss = filter('**.scss', {restore: true});

    return gulp.src([
        './frontend/admin/style/admin.scss',
        './frontend/bower_components/semantic-ui/dist/semantic.min.css'
    ])
        .pipe(filterScss)
        .pipe(sass().on('error', sass.logError))
        .pipe(revReplace({manifest: gulp.src('./var/tmp/rev-image.json')}))
        .pipe(minifyCss())
        .pipe(filterScss.restore)
        .pipe(concat('web.css'))
        .pipe(rev())
        .pipe(gulp.dest('./web/css/web'))
        .pipe(rev.manifest('rev-css-web.json'))
        .pipe(gulp.dest('./var/tmp'));
}

function buildDashboardTypescriptDev() {
    return gulp.src([
        './frontend/dashboard/app/all.ts',
        './frontend/dashboard/app/app.ts',
        './frontend/dashboard/app/states.ts',
        './frontend/dashboard/app/run.ts',
        './frontend/dashboard/app/*/**/*.ts'
    ], {base: './frontend/dashboard'})
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.init()))
        .pipe(typescript({sortOutput: true, target: 'ES5'}))
        .pipe(ngAnnotate())
        .pipe(concat('dashboard.js'))
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.write()))
        .pipe(gulp.dest('./var/tmp/js/dashboard'));
}

function buildAdminTypescriptDev() {
    return gulp.src([
        './frontend/admin/app/all.ts',
        './frontend/admin/app/app.ts',
        './frontend/admin/app/run.ts',
        './frontend/admin/app/*/**/*.ts'
    ], {base: './frontend/admin'})
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.init()))
        .pipe(typescript({sortOutput: true, target: 'ES5'}))
        .pipe(ngAnnotate())
        .pipe(concat('admin.js'))
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.write()))
        .pipe(gulp.dest('./var/tmp/js/admin'));
}

function buildWebTypescriptDev() {
    return gulp.src([
        './frontend/web/app/all.ts',
        './frontend/web/app/app.ts',
        './frontend/web/app/run.ts',
        './frontend/web/app/*/**/*.ts'
    ], {base: './frontend/web'})
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.init()))
        .pipe(typescript({sortOutput: true, target: 'ES5'}))
        .pipe(ngAnnotate())
        .pipe(concat('web.js'))
        .pipe(gulpIf(config.useSourceMaps, sourcemaps.write()))
        .pipe(gulp.dest('./var/tmp/js/web'));
}

function buildDashboardTypescript() {
    var typescriptFilter = filter('dashboard/app/**/*.ts', {restore: true});
    var htmlFilter = filter('dashboard/app/**/*.html', {restore: true});
    var vendorFilter = filter('bower_components/**/*.js', {restore: true});

    return gulp.src([
        './frontend/bower_components/jquery/dist/jquery.min.js',
        './frontend/bower_components/angularjs/angular.min.js',
        './frontend/bower_components/angular-ui-router/release/angular-ui-router.min.js',
        './frontend/bower_components/semantic-ui/dist/semantic.min.js',
        './frontend/dashboard/app/**/*.html',
        './frontend/dashboard/app/all.ts',
        './frontend/dashboard/app/app.ts',
        './frontend/dashboard/app/states.ts',
        './frontend/dashboard/app/run.ts',
        './frontend/dashboard/app/*/**/*.ts'
    ], {base: './frontend'})
        .pipe(vendorFilter)
        .pipe(concat('vendor.js'))
        .pipe(vendorFilter.restore)
        .pipe(htmlFilter)
        .pipe(minifyHtml())
        .pipe(debug({minimal: false}))
        .pipe(ngTemplate({
            filename: 'dashboard-template.js',
            module: 'undine.dashboard.template',
            base: __dirname + '/frontend/dashboard/app',
            root: '/',
            standalone: true
        }))
        .pipe(minifyJs())
        .pipe(htmlFilter.restore)
        .pipe(typescriptFilter)
        .pipe(typescript({sortOutput: true, target: 'ES5'}))
        .pipe(ngAnnotate())
        .pipe(concat('dashboard.js'))
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
    var htmlFilter = filter('admin/app/**/*.html', {restore: true});
    var vendorFilter = filter('bower_components/**/*.js', {restore: true});

    return gulp.src([
        './frontend/bower_components/jquery/dist/jquery.min.js',
        './frontend/bower_components/angularjs/angular.min.js',
        './frontend/bower_components/semantic-ui/dist/semantic.min.js',
        './frontend/admin/app/**/*.html',
        './frontend/admin/app/all.ts',
        './frontend/admin/app/app.ts',
        './frontend/admin/app/run.ts',
        './frontend/admin/app/*/**/*.ts'
    ], {base: './frontend'})
        .pipe(vendorFilter)
        .pipe(concat('vendor.js'))
        .pipe(vendorFilter.restore)
        .pipe(htmlFilter)
        .pipe(minifyHtml())
        .pipe(ngTemplate({
            filename: 'admin-template.js',
            module: 'undine.admin.template',
            base: __dirname + '/frontend/admin/app',
            root: '/',
            standalone: true
        }))
        .pipe(minifyJs())
        .pipe(htmlFilter.restore)
        .pipe(typescriptFilter)
        .pipe(typescript({sortOutput: true, target: 'ES5'}))
        .pipe(ngAnnotate())
        .pipe(concat('admin.js'))
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
    var htmlFilter = filter('web/app/**/*.html', {restore: true});
    var vendorFilter = filter('bower_components/**/*.js', {restore: true});

    return gulp.src([
        './frontend/bower_components/jquery/dist/jquery.min.js',
        './frontend/bower_components/angularjs/angular.min.js',
        './frontend/bower_components/semantic-ui/dist/semantic.min.js',
        './frontend/web/app/**/*.html',
        './frontend/web/app/all.ts',
        './frontend/web/app/app.ts',
        './frontend/web/app/run.ts',
        './frontend/web/app/*/**/*.ts'
    ], {base: './frontend'})
        .pipe(vendorFilter)
        .pipe(concat('vendor.js'))
        .pipe(vendorFilter.restore)
        .pipe(htmlFilter)
        .pipe(minifyHtml())
        .pipe(ngTemplate({
            filename: 'web-template.js',
            module: 'undine.web.template',
            base: __dirname + '/frontend/web/app',
            root: '/',
            standalone: true
        }))
        .pipe(minifyJs())
        .pipe(htmlFilter.restore)
        .pipe(typescriptFilter)
        .pipe(typescript({sortOutput: true, target: 'ES5'}))
        .pipe(ngAnnotate())
        .pipe(concat('web.js'))
        .pipe(minifyJs())
        .pipe(typescriptFilter.restore)
        .pipe(concat('web.js'))
        .pipe(rev())
        .pipe(gulp.dest('./web/js/web'))
        .pipe(rev.manifest('rev-js-web.json'))
        .pipe(gulp.dest('./var/tmp'));
}

function buildDashboardTemplateDev() {
    return gulp.src('./frontend/dashboard/app/**/*.html')
        .pipe(ngTemplate({
            filename: 'dashboard-template.js',
            module: 'undine.dashboard.template',
            root: '/',
            standalone: true
        }))
        .pipe(gulp.dest('./var/tmp/js/dashboard'));
}

function buildAdminTemplateDev() {
    return gulp.src('./frontend/admin/app/**/*.html')
        .pipe(ngTemplate({
            filename: 'admin-template.js',
            module: 'undine.admin.template',
            root: '/',
            standalone: true
        }))
        .pipe(gulp.dest('./var/tmp/js/admin'));
}

function buildWebTemplateDev() {
    return gulp.src('./frontend/web/app/**/*.html')
        .pipe(ngTemplate({
            filename: 'web-template.js',
            module: 'undine.web.template',
            root: '/',
            standalone: true
        }))
        .pipe(gulp.dest('./var/tmp/js/web'));
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

function buildSemanticTheme() {
    return gulp.src('./frontend/bower_components/semantic-ui/dist/themes/default/**', {base: './frontend/bower_components/semantic-ui/dist'})
        .pipe(gulp.dest('./web/css/dashboard'))
        .pipe(gulp.dest('./web/css/admin'))
        .pipe(gulp.dest('./web/css/web'));
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
    watch('./frontend/dashboard/image/**/*', gulp.series(reloadComponent.bind(null, 'html')));

    watch('./frontend/dashboard/style/**/*', gulp.series(buildDashboardCssDev, reloadComponent.bind(null, 'css')));
    watch('./frontend/dashboard/app/**/*.ts', gulp.series(buildDashboardTypescriptDev, reloadComponent.bind(null, 'html')));
    watch('./frontend/dashboard/app/**/*.html', gulp.series(buildDashboardTemplateDev, reloadComponent.bind(null, 'html')));
    watch('./app/Resources/views/dashboard/app.html.twig', gulp.series(reloadComponent.bind(null, 'html')));

    watch('./frontend/admin/style/**/*', gulp.series(buildAdminCssDev, reloadComponent.bind(null, 'css')));
    watch('./frontend/admin/app/**/*.ts', gulp.series(buildAdminTypescriptDev, reloadComponent.bind(null, 'html')));
    watch('./frontend/admin/app/**/*.html', gulp.series(buildAdminTemplateDev, reloadComponent.bind(null, 'html')));
    watch('./app/Resources/views/admin/layout.html.twig', gulp.series(reloadComponent.bind(null, 'html')));

    watch('./frontend/web/style/**/*', gulp.series(buildWebCssDev, reloadComponent.bind(null, 'css')));
    watch('./frontend/web/app/**/*.ts', gulp.series(buildWebTypescriptDev, reloadComponent.bind(null, 'html')));
    watch('./frontend/web/app/**/*.html', gulp.series(buildWebTemplateDev, reloadComponent.bind(null, 'html')));
    watch('./app/Resources/views/web/layout.html.twig', gulp.series(reloadComponent.bind(null, 'html')));
}

gulp.task('default',
    gulp.series(
        cleanDev,
        gulp.parallel(
            buildDashboardCssDev,
            buildDashboardTemplateDev,
            buildDashboardTypescriptDev,
            buildDashboardIndex,

            buildAdminCssDev,
            buildAdminTemplateDev,
            buildAdminTypescriptDev,
            buildAdminIndex,

            buildWebCssDev,
            buildWebTemplateDev,
            buildWebTypescriptDev,
            buildWebIndex
        ),
        gulp.parallel(
            server,
            watchDev
        )));

gulp.task('build',
    gulp.series(
        clean,
        buildImage,
        gulp.parallel(
            buildDashboardCss,
            buildDashboardTypescript,
            buildDashboardIndex,

            buildAdminCss,
            buildAdminTypescript,
            buildAdminIndex,

            buildWebCss,
            buildWebTypescript,
            buildWebIndex,

            buildSemanticTheme
        )
    )
);
