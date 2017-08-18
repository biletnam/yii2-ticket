Manufacturing Ticket System
===========================
Allows for a custom ticket drill down (i.e. select process->which machine etc.)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist bausch/yii2-ticket "*"
```

or add

```
"bausch/yii2-ticket": "*"
```

to the require section of your `composer.json` file.


Perform db migration
-----

```
php yii migrate --interactive=0 --migrationPath=@vendor/bausch/yii2-ticket/migrations/ 
```


Usage
-----

Once the extension is installed, simply use it in your code by  :

TBD

