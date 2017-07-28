/**
 * Created by jarne on 28.07.17.
 */

var gulp = require("gulp");
var concat = require("gulp-concat");
var cleanCss = require("gulp-clean-css");
var uglify = require("gulp-uglify");

gulp.task("jquery", function() {
    return gulp.src("bower_components/jquery/dist/jquery.min.js")
        .pipe(gulp.dest("assets/dest/js"))
});

gulp.task("bootstrap-css", function() {
    return gulp.src("bower_components/bootstrap/dist/css/bootstrap.min.css")
        .pipe(gulp.dest("assets/dest/css"))
});

gulp.task("bootstrap-js", function() {
    return gulp.src("bower_components/bootstrap/dist/js/bootstrap.min.js")
        .pipe(gulp.dest("assets/dest/js"))
});

gulp.task("bootstrap-fonts", function() {
    return gulp.src("bower_components/bootstrap/dist/fonts/*")
        .pipe(gulp.dest("assets/dest/fonts"))
});

gulp.task("index-css", function() {
    return gulp.src("assets/src/css/index.css")
        .pipe(concat("index.min.css"))
        .pipe(cleanCss())
        .pipe(gulp.dest("assets/dest/css"))
});

gulp.task("index-js", function() {
    return gulp.src("assets/src/js/index.js")
        .pipe(concat("index.min.js"))
        .pipe(uglify())
        .pipe(gulp.dest("assets/dest/js"))
});

gulp.task("watch", function() {
    gulp.watch("bower_components/jquery/jquery.min.js", ["jquery"]);

    gulp.watch("bower_components/bootstrap/dist/css/bootstrap.min.css", ["bootstrap-css"]);
    gulp.watch("bower_components/bootstrap/dist/js/bootstrap.min.js", ["bootstrap-js"]);
    gulp.watch("bower_components/bootstrap/dist/fonts/*", ["bootstrap-fonts"]);

    gulp.watch("assets/src/css/index.css", ["index-css"]);
    gulp.watch("assets/src/css/index.js", ["index-js"]);
});

gulp.task("default", ["jquery", "bootstrap-css", "bootstrap-js", "bootstrap-fonts", "index-css", "index-js"]);