const {phpMinify, TransformMode} = require('@cedx/gulp-php-minify');
const gulp = require('gulp');

gulp.task('vendor', () => gulp.src('**/*.php', {read: true})
  .pipe(phpMinify({mode: TransformMode.safe}))
  .pipe(gulp.dest('vendor'))
);
gulp.task('default', ['vendor' ]);