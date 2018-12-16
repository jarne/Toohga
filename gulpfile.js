/**
 * Created by jarne on 28.07.17.
 */

const gulp = require("gulp");
const concat = require("gulp-concat");
const cleanCss = require("gulp-clean-css");
const minify = require("gulp-minify");

gulp.task("jquery", () => {
    return gulp.src("bower_components/jquery/dist/jquery.min.js")
        .pipe(gulp.dest("assets/dest/js"));
});

gulp.task("bootstrap-css", () => {
    return gulp.src("bower_components/bootstrap/dist/css/bootstrap.min.css")
        .pipe(gulp.dest("assets/dest/css"));
});

gulp.task("bootstrap-js", () => {
    return gulp.src("bower_components/bootstrap/dist/js/bootstrap.min.js")
        .pipe(gulp.dest("assets/dest/js"));
});

gulp.task("open-iconic-css", () => {
    return gulp.src("bower_components/open-iconic/font/css/open-iconic-bootstrap.min.css")
        .pipe(gulp.dest("assets/dest/css"));
});

gulp.task("open-iconic-fonts", () => {
    return gulp.src("bower_components/open-iconic/font/fonts/*")
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

gulp.task("watch", function() {
    gulp.watch("bower_components/jquery/jquery.min.js", gulp.parallel("jquery"));

    gulp.watch("bower_components/bootstrap/dist/css/bootstrap.min.css", gulp.parallel("bootstrap-css"));
    gulp.watch("bower_components/bootstrap/dist/js/bootstrap.min.js", gulp.parallel("bootstrap-js"));

    gulp.watch("bower_components/open-iconic/font/css/open-iconic-bootstrap.min.css", gulp.parallel("open-iconic-css"));
    gulp.watch("bower_components/open-iconic/font/fonts/*", gulp.parallel("open-iconic-fonts"));

    gulp.watch("assets/src/css/index.css", gulp.parallel("index-css"));
    gulp.watch("assets/src/js/index.js", gulp.parallel("index-js"));

    gulp.watch("assets/src/css/privacy.css", gulp.parallel("privacy-css"));
});

gulp.task("default", gulp.parallel("jquery", "bootstrap-css", "bootstrap-js", "open-iconic-css", "open-iconic-fonts", "index-css", "index-js", "privacy-css"));
