# Article APP Api

## Installation

Clone the Repository:
```sh
git clone https://github.com/rafifrafael/article-rm-api.git
```

Navigate to the Project Directory:
```sh
cd article-rm-api
```

Install Dependencies:
```sh
composer install
```

Run Database Migrations:
```sh
php spark migrate
```

Run:
```sh
php spark serve
```


## Server Requirements

PHP version 7.4 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
