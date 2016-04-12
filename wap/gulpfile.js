var gulp = require('gulp');
var gutil = require('gulp-util');
var concat = require('gulp-concat');
var minifyCss = require('gulp-minify-css');
var rename = require('gulp-rename');
var header = require('gulp-header');
var uglify = require('gulp-uglify');
var clean = require('gulp-clean');
var sh = require('shelljs');
var fs = require('fs');

var banner = [
  '/**',
  ' ** DaZhuang',
  ' ** Last publish: ' + new Date(),
  ' ** @version v0.1.0',
  ' **/',
  ''
].join('\n');

var paths = {
  sass: ['./scss/**/*.scss'],
  css: ['./www/lib/ionic/css/ionic.css', './www/css/*.css'],
  jsBase: ['./www/lib/ionic/js/ionic.bundle.min.js', './www/lib/ng-file-upload/ng-file-upload-00.js'],
  jsApp: [
    './www/js/app.js',
    './www/js/util.js',
    './www/js/config.app.js',
    './www/js/service/*.js',
    './www/js/route/*.js',
    './www/js/controllers/*.js'
  ]
};

// 清除发布目录
gulp.task('clean', function(){
  return gulp.src('./www/dist/', {read: false})
      .pipe(clean());
});

// 压缩合并css
gulp.task('css-min', function() {
  // 自己的css文件
  gulp.src(paths.css)
      .pipe(minifyCss())
      .pipe(concat('app.min.css'))
      .pipe(header(banner))
      .pipe(gulp.dest('./www/dist/css/'));
});

// 压缩合并js
gulp.task('js-min-base', function(){
  return gulp.src(paths.jsBase)
      .pipe(uglify({ outSourceMap: false}))
      .pipe(concat('base.min.js'))
      .pipe(header(banner))
      .pipe(gulp.dest('./www/dist/js/'))
});
gulp.task('js-min-app', function(){
  return gulp.src(paths.jsApp)
      .pipe(uglify({ outSourceMap: false}))
      .pipe(concat('app.min.js'))
      .pipe(header(banner))
      .pipe(gulp.dest('./www/dist/js/'))
});

//拷贝字体
gulp.task('copy-fonts', function() {
  gulp.src('./www/fonts/**/*')
      .pipe(gulp.dest('./www/dist/fonts/'));
});

//更新版本
gulp.task('update-version', function() {
  var indexFile = './www/index.prd.html';
  var indexContent = fs.readFileSync(indexFile, 'utf8');
  console.log(indexContent);
  console.log('------');
  var newVersion = new Date().getTime();
  var newIndex = indexContent.replace(/pub_time=.*e/ig, "pub_time=" + newVersion + "e");
  console.log(newIndex);
  if(!fs.writeFileSync(indexFile, newIndex, 'utf8')){
    console.log('重写 ' + indexFile + ' 文件失败！');
  }else{
    console.log('\n 已替换新的 js & css 版本号：' + newVersion + '\n');
  }
});

gulp.task('build', function(){
  sh.exec('gulp clean');
  sh.exec('gulp css-min');
  sh.exec('gulp js-min-base');
  sh.exec('gulp js-min-app');
  sh.exec('gulp copy-fonts');

});

gulp.task('default', ['build']);