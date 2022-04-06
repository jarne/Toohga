/**
 * Toohga | gulp file
 */

const gulp = require("gulp");
const concat = require("gulp-concat");
const sassLib = require("sass");
const sass = require("gulp-sass")(sassLib);
const cleanCss = require("gulp-clean-css");
const babel = require("gulp-babel");
const minify = require("gulp-minify");

gulp.task("bootstrap-css", () => {
    return gulp
        .src("assets/src/css/bootstrapCustom.scss")
        .pipe(sass())
        .pipe(cleanCss())
        .pipe(concat("bootstrap.min.css"))
        .pipe(gulp.dest("public/assets/dest/css"));
});

gulp.task("bootstrap-js", () => {
    return gulp
        .src("node_modules/bootstrap/dist/js/bootstrap.bundle.min.js")
        .pipe(gulp.dest("public/assets/dest/js"));
});

gulp.task("open-iconic-css", () => {
    return gulp
        .src("node_modules/open-iconic/font/css/open-iconic-bootstrap.min.css")
        .pipe(gulp.dest("public/assets/dest/css"));
});

gulp.task("open-iconic-fonts", () => {
    return gulp
        .src("node_modules/open-iconic/font/fonts/*")
        .pipe(gulp.dest("public/assets/dest/fonts"));
});

gulp.task("index-css", () => {
    return gulp
        .src("assets/src/css/index.css")
        .pipe(concat("index.min.css"))
        .pipe(cleanCss())
        .pipe(gulp.dest("public/assets/dest/css"));
});

gulp.task("index-js", () => {
    return gulp
        .src("assets/src/js/index.js")
        .pipe(
            babel({
                presets: ["@babel/env"],
            })
        )
        .pipe(
            minify({
                noSource: true,
            })
        )
        .pipe(concat("index.min.js"))
        .pipe(gulp.dest("public/assets/dest/js"));
});

gulp.task("admin-css", () => {
    return gulp
        .src("assets/src/css/admin.css")
        .pipe(concat("admin.min.css"))
        .pipe(cleanCss())
        .pipe(gulp.dest("public/assets/dest/css"));
});

gulp.task("admin-js", () => {
    return gulp
        .src("assets/src/js/admin.js")
        .pipe(
            babel({
                presets: ["@babel/env"],
            })
        )
        .pipe(
            minify({
                noSource: true,
            })
        )
        .pipe(concat("admin.min.js"))
        .pipe(gulp.dest("public/assets/dest/js"));
});

gulp.task("images", () => {
    return gulp
        .src("assets/src/images/*")
        .pipe(gulp.dest("public/assets/dest/images"));
});

gulp.task("watch", () => {
    gulp.watch(
        "assets/src/css/bootstrapCustom.scss",
        gulp.parallel("bootstrap-css")
    );
    gulp.watch(
        "node_modules/bootstrap/dist/js/bootstrap.bundle.min.js",
        gulp.parallel("bootstrap-js")
    );

    gulp.watch(
        "node_modules/open-iconic/font/css/open-iconic-bootstrap.min.css",
        gulp.parallel("open-iconic-css")
    );
    gulp.watch(
        "node_modules/open-iconic/font/fonts/*",
        gulp.parallel("open-iconic-fonts")
    );

    gulp.watch("assets/src/css/index.css", gulp.parallel("index-css"));
    gulp.watch("assets/src/js/index.js", gulp.parallel("index-js"));

    gulp.watch("assets/src/css/admin.css", gulp.parallel("admin-css"));
    gulp.watch("assets/src/js/admin.js", gulp.parallel("admin-js"));

    gulp.watch("assets/src/images/*", gulp.parallel("images"));
});

gulp.task(
    "default",
    gulp.parallel(
        "bootstrap-css",
        "bootstrap-js",
        "open-iconic-css",
        "open-iconic-fonts",
        "index-css",
        "index-js",
        "admin-css",
        "admin-js",
        "images"
    )
);
