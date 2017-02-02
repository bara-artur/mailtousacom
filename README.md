Yii 2 mail to USA
============================

Проект бла бла бла.

### Участники проекта

- 1
- 2
- 3

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



REQUIREMENTS
------------


- PHP 5.4.0.
- composer

INSTALLATION
------------

- clone from github
```sh 
git clone https://github.com/bara-artur/mailtousa
```
- change dir
```sh
cd mailtousa
```
- install composer module
```sh
composer install
```
- make all config in @app/config
- make migrate RBAC
```sh
php yii migrate/up --migrationPath=@yii/rbac/migrations
```
- make migrate site
```sh
php yii migrate
```


Известные ошибки
-----------------
Если при запуске сайта выдает ошибку доступа к
The file or directory to be published does not exist: /vendor/bower/jquery/dist
то клонируйте содержимое из /vendor/bower-asset/ в /vendor/bower/