Wumpa
=====
Home-made PHP micro-framework

What's in a name ?
-----
A Wumpa is a fruit from the Crash Bandicoot game univers... And I love those games.  
Yep, that's all... What did you expect more ? ^^

1) Overview
-----
Wumpa is a one man project. It's a light weight PHP micro-framework providing basic tools for small projects.  
It is not intended to compete with any of the framework on the market.  
I started to work on this project in order to build myself a micro framework that fits exactly my way to code and my needs.  

2) Installation
-----
To get started you just need to the sources.

The easiest way is to use [Composer](https://getcomposer.org "Composer").

	$ php composer.phar create-project wumpa/wumpa -s "dev"

You can also download the sources from the [GitHub repo](https://github.com/de-luca/wumpa "Wumpa repo on GitHub").

3) Features
-----
- Flexible routing system (with request validation)
- Database management system (MySQL or PgSQL, can be extended)
- Exception handler
- Some encryption tools (not to be used for password)
- File management
- Console component for project initialization
- Model class generator (only Postgres database supported right now)

External PHP libraries: (Check their documentation to get started with these)
- PDO
- Twig
- Composer autoloading

Todo list:
- Add MySQL support (model generation)
- Error handling and reporting
- i18n

4) Documentation & Getting started
-----
To get started and to learn how to use Wumpa check out the [wiki](https://github.com/de-luca/Wumpa/wiki).  
