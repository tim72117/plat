var gulp = require('gulp');
var eslint = require('gulp-eslint');
var phpunit = require('gulp-phpunit');
var notify = require('gulp-notify');

gulp.task('default', function() {
    // gulp.src("public/js/ng/*.js")
    // .pipe(eslint())
    // .pipe(eslint.format());

    gulp.watch(['app/library/**/*.php', 'app/tests/**/*.php'], { debounceDelay: 2000 }, ['phpunit']);
});

gulp.task('phpunit', function() {
    var options = {debug: false, notify: false, testClass: 'app/tests/survey'};
    gulp.src('')
        .pipe(phpunit('', options))
        .on('error', notify.onError({
            title: "Failed Tests!",
            message: "Error(s) occurred during testing..."
        }));
});
