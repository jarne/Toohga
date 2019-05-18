/**
 * Toohga | gulp file
 */

const gulp = require("gulp");
const concat = require("gulp-concat");
const cleanCss = require("gulp-clean-css");
const minify = require("gulp-minify");

gulp.task("bootstrap-css", () => {
    return gulp.src("node_modules/bootstrap/dist/css/bootstrap.min.css")
        .pipe(gulp.dest("assets/dest/css"));
});

gulp.task("open-iconic-css", () => {
    return gulp.src("node_modules/open-iconic/font/css/open-iconic-bootstrap.min.css")
        .pipe(gulp.dest("assets/dest/css"));
});

gulp.task("open-iconic-fonts", () => {
    return gulp.src("node_modules/open-iconic/font/fonts/*")
        .pipe(gulp.dest("assets/dest/fonts"));
});

gulp.task("index-css", () => {
    return gulp.src("assets/src/css/index.css")
        .pipe(concat("index.min.css"))
        .pipe(cleanCss())
        .pipe(gulp.dest("assets/dest/css"));
});

gulp.task("index-js", () => {
    return gulp.src("assets/src/js/index.js")
        .pipe(minify({
            noSource: true
        }))
        .pipe(concat("index.min.js"))
        .pipe(gulp.dest("assets/dest/js"));
});

gulp.task("privacy-css", () => {
    return gulp.src("assets/src/css/privacy.css")
        .pipe(concat("privacy.min.css"))
        .pipe(cleanCss())
        .pipe(gulp.dest("assets/dest/css"));
});

gulp.task("watch", () => {
    gulp.watch("node_modules/bootstrap/dist/css/bootstrap.min.css", gulp.parallel("bootstrap-css"));

    gulp.watch("node_modules/open-iconic/font/css/open-iconic-bootstrap.min.css", gulp.parallel("open-iconic-css"));
    gulp.watch("node_modules/open-iconic/font/fonts/*", gulp.parallel("open-iconic-fonts"));

    gulp.watch("assets/src/css/index.css", gulp.parallel("index-css"));
    gulp.watch("assets/src/js/index.js", gulp.parallel("index-js"));

    gulp.watch("assets/src/css/privacy.css", gulp.parallel("privacy-css"));
});

gulp.task("default", gulp.parallel("bootstrap-css", "open-iconic-css", "open-iconic-fonts", "index-css", "index-js", "privacy-css"));
