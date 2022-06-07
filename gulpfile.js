const autoprefixer = require("gulp-autoprefixer");
const concat = require("gulp-concat");
const gulp = require("gulp");
const sass = require('gulp-sass')(require('sass'));
const sourcemaps = require("gulp-sourcemaps");
const terser = require("gulp-terser-js");
const rename = require("gulp-rename");

const files = {
	styles: {
		path: ["assets/scss/**/*.scss"],
		watch: "assets/scss/**/*.scss"
	},
	scripts: {
		path: [
			"node_modules/jquery/dist/jquery.js",
			"node_modules/bootstrap/dist/js/bootstrap.js",
			"node_modules/naja/dist/Naja.js",
			"node_modules/@hermajan/booty/js/booty.js",
			"assets/js/**/*.js"
		],
		watch: "assets/js/**/*.js"
	}
};

// Compiles the SCSS files into CSS
function styles() {
	return gulp.src(files.styles.path, {sourcemaps: true})
		// .pipe(sourcemaps.init()) // initializes sourcemaps first
		// .pipe(sass({outputStyle: "compressed"}).on("error", sass.logError)) // compiles SCSS to CSS and minifies CSS files
		// .pipe(autoprefixer()) // adds vendor prefixes to CSS rules
		// .pipe(sourcemaps.write(".")) // writes sourcemaps file in current directory
		// .pipe(gulp.dest("dist")); // puts final CSS in dist folder
		.pipe(sass({outputStyle: "expanded"}).on("error", sass.logError)) // compiles SCSS to CSS
		.pipe(autoprefixer()) // adds vendor prefixes to CSS rules
		.pipe(gulp.dest("www/css")) // puts final CSS in dist folder
		.pipe(sass({outputStyle: "compressed"}).on("error", sass.logError)) // compiles to CSS and minifies CSS files
		.pipe(rename({suffix: ".min"}))
		.pipe(gulp.dest("www/css"));
}

// Concatenates and uglifies JS files
function javascripts() {
	return gulp.src(files.scripts.path, {sourcemaps: true})
		.pipe(sourcemaps.init()) // initializes sourcemaps first
		.pipe(concat("scripts.js"))
		.pipe(terser()) // minifies file
		.pipe(sourcemaps.write(".")) // writes sourcemaps file in current directory
		.pipe(gulp.dest("www/js"));
}

// Watch SCSS and JS files for changes
function watch() {
	gulp.watch([files.styles.watch, files.scripts.watch], gulp.parallel(styles, javascripts));
}

// Export the default Gulp task so it can be run
exports.default = gulp.series(gulp.parallel(styles, javascripts), watch);

exports.setup = gulp.parallel(styles, javascripts);

// function bootstrapFonts() {
// 	gulp.src("node_modules/bootstrap-icons/font/fonts/bootstrap-icons.woff")
// 		.pipe(gulp.dest("www/fonts"));
//
// 	return gulp.src("node_modules/bootstrap-icons/font/fonts/bootstrap-icons.woff2")
// 		.pipe(gulp.dest("www/fonts"));
// }
// exports.bootstrap = gulp.series(bootstrapFonts);
