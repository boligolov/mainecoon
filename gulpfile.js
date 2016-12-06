const gulp = require('gulp');
const sass = require('gulp-sass');
const pug = require('gulp-pug');
const exec = require('gulp-exec');
const autoprefixer = require('gulp-autoprefixer');
const cssmin = require('gulp-cssmin');
const rename = require('gulp-rename');
const runSequence = require('run-sequence');

const src = 'src/';
const assets = src + 'assets/';
const dist_standalone = 'dist/standalone/';
const dist_complex = 'dist/complex/';



gulp.task('make', function() {
    var options = {
        continueOnError: false, // default = false, true means don't emit error event
        pipeStdout: false, // default = false, true means stdout is written to file.contents
        customTemplatingThing: "test" // content passed to gutil.template()
    };
    var reportOptions = {
        err: true, // default = true, false means don't write err
        stderr: true, // default = true, false means don't write stderr
        stdout: true // default = true, false means don't write stdout
    };
    return gulp.src(dist_complex)
        //.pipe(exec('php make.php', options))
        //.pipe(exec.reporter(reportOptions))
        .pipe(exec('xcopy "D:\\Development\\Server\\domains\\mainecoon.ru\\dist\\complex\\*" "D:\\Development\\Server\\domains\\flaxfactory.ru" /Y /E', options))
        .pipe(exec.reporter(reportOptions));
});


gulp.task('online', ['sass', 'pug', 'copy', 'copy:js'], function() {
    gulp.watch([assets + 'scss/**/*.sass'], runSequence('sass', 'copy', 'copy:js', 'make'));
    gulp.watch([assets + 'pug/**/*.pug'], runSequence('pug', 'copy', 'copy:js', 'make'));
    gulp.watch([src + '**/*.php'], runSequence('copy', 'copy:js', 'make'));
});


gulp.task('pug', function() {
    return gulp.src(assets+'pug/**/*.pug')
        .pipe(pug({
            pretty: true
        }))
        .pipe(rename(function (path) {
            path.extname = ".php"
        }))
        .pipe(gulp.dest(src+'mainecoon/view/'));
});

gulp.task('sass', function() {
    return gulp.src(assets+'scss/app.scss')
        .pipe(sass({
            includePaths: require('node-normalize-scss').includePaths
        }).on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['last 2 versions', 'ie >= 9']
        }))
        .pipe(cssmin())
        .pipe(gulp.dest(dist_complex+'mainecoon/css/'));
});



gulp.task('copy', function () {
    return gulp.src([
        src + '/**/*',
        '!' + assets,
        '!' + assets + '/**'
    ], {
        dot: false
    }).pipe(gulp.dest(dist_complex));
});
gulp.task('copy:js', function () {
    return gulp.src([
        assets + '/js/*'
    ], {
        dot: false
    }).pipe(gulp.dest(dist_complex+'mainecoon/js/'));
});

/*
gulp.task('build', function (done) {
    runSequence(
        ['sass', 'pug'],
        'copy',
        done);
});*/
