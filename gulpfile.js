const {src, dest, watch, series, parallel} = require('gulp');
const sourcemaps = require('gulp-sourcemaps');
const sass = require('gulp-sass');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const autoprefixer = require('gulp-autoprefixer');
const cssnano = require('gulp-cssnano');

const files = {
	scssPath: 'assets/scss/**/*.scss',
	jsPath: 'assets/js/**/*.js'
};

// Compiles the SCSS files into CSS
function scssTask() {
	return src(files.scssPath)
		.pipe(sourcemaps.init()) // initializes sourcemaps first
		.pipe(sass()) // compiles SCSS to CSS
		.pipe(autoprefixer()) // adds vendor prefixes to CSS rules
		.pipe(cssnano()) // minifies CSS files
		.pipe(sourcemaps.write('.')) // writes sourcemaps file in current directory
		.pipe(dest('dist')); // puts final CSS in dist folder
}

// Concatenates and uglifies JS files
function jsTask() {
	return src([
		files.jsPath
		//,'!' + 'includes/js/jquery.min.js', // to exclude any specific files
	])
	// .pipe(concat('script.js'))
		.pipe(uglify())
		.pipe(dest('dist'));
}

// Watch SCSS and JS files for changes
function watchTask() {
	watch([files.scssPath, files.jsPath], parallel(scssTask, jsTask));
}

// Export the default Gulp task so it can be run
exports.default = series(
	parallel(scssTask, jsTask),
	watchTask
);
