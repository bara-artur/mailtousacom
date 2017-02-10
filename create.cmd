set php=C:\OpenServer\modules\php\PHP-5.4
set PATH=%PATH%;%php%
php %php%\composer.phar create-project yiisoft/yii2-app-advanced advanced 2.0.6
cd ./advanced
init.bat
pause
