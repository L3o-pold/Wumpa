Wumpa
=====
Home-made PHP micro-framework

What's in a name ?
-----
A Wumpa is a fruit from the Crash Bandicoot game univers.  
I found this name was funny and I enjoyed playing the games.  
Yep, that's all... What did you expect more ? ^^

1) Overview
-----
Wumpa is a one man project. It's a light weight PHP micro-framework providing basics tools for little projects.  
It is not intended to compete with any of the framework on the market.  
I started to work on this project in order to build myself a micro framework that fits exactly my way to code and my needs.  
This framework follows some PHP standards such as PSR-0 autoloading and use some very neat tools for PHP development like [Composer](https://getcomposer.org "Composer").

2) Installation
----------------------------------
In order to get started you just need to get the sources.

The easiest way is to use Composer.

	$ php composer.phar create-project wumpa/wumpa

You can also download the sources from the [GitHub repo](https://github.com/4nthrx/wumpa "Wumpa repo on GitHub").

3) Features
----------------------------------
Wumpa provide some basic level features required to run your project such as:
- Flexible routing system (with request validation)
- Database management system (MySQL or PgSQL)
- Exception handler
- Console component for project initialisation
- Some encryption tools (not to be used for password)
- File management
- Model class generator (database analyser)

Wumpa use some of the best PHP librairies: (Check their documentation to get started with these)
- PDO in order to query database
- Twig for templating
- Swiftmailer for mailing system
- Composer autoloading
