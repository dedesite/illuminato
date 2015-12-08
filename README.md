IlluminatoShop
==============

IlluminatoShop is a set of tools that allows Prestashop Module developpers to use Laravel 5 API inside PrestaShop.

For now, it is a nasty hack, maybe it will evolve into something cleaner.

# History

I found myself trying to write a PrestaShop module which uses the Prestashop's FormHelper class and complaining a lot about it. In fact, it was such a complexe tool to use that my code was more complex and long than a plain old Html form, plus you can only use it for simple cases. I found that the FormHelper is not able to generate the forms I needed so I went back to Html forms.

After recreating all my forms in Html, I remeber that Laravel had such a great Helper for forms that I decided to mimic it's API and borrow from it's code. It was cool and my form code got really easy to understand and very short comparing the two previous methods.

Then, I just realize I could maybe use all the Laravel's Tools (input validation, routes, Query Builder, Eloquent, Migration, Queue etc.) and I got deep into it ! Hacking, reading (espacially Fabien Serny's Book on Prestashop module developpment which was an unvaluable ressource for the Prestashop beginner I was) and trying to find a way to get the best of the two worlds (honnestly the best is mostly on Laravel sides) without breaking compatibilities with PrestaShop important feature (automatic upgrades, admin object model list, friendly url, localization interface etc.).

# Disclaimer

Yes, IlluminatoShop do not respects PrestaShop guidings and coding standard since it tends to mimic a Laravel application behaviour, it respects as much as possible Laravels guidings.

Yes, it uses namespace and composer so it needs PHP 5.4 at least. Don't expect it to be able on the official addons store.

# Installation

## Prerequis

* PHP 5.4 or greater
* Prestashop 1.6

## Prestashop modification

Since IlluminatoShop uses namespace and also the `use` keyword, you need to remove the `eval` code in `classes/module/Module.php` in `getModulesOnDisk` function.

Warning : This means that if one of your module got an error, your Prestashop installation won't work at all !

Old code (line 1242 on Prestashop 1.6.14) :
```php
if (eval('if (false){	'.$file.' }') !== false)
	require_once( _PS_MODULE_DIR_.$module.'/'.$module.'.php' );
else
	$errors[] = sprintf(Tools::displayError('%1$s (parse error in %2$s)'), $module, substr($filepath, strlen(_PS_ROOT_DIR_)));

```

New code :
```php
require_once( _PS_MODULE_DIR_.$module.'/'.$module.'.php' );
```

## Module installation

IlluminatoShop is a regular Prestashop module that embeded Laravel's functions and classes.
To install it, just download the archive and upload it on your server (or install it through Prestashop admin interface).

Now, you can start playing with it !!

# Create a module

## Files and folders

Illuminato's modules do not follow Prestashop guidelines but are closer to Laravel Modules or October CMS plugin in their structure.

What remains is the module file at the root of the folder which should have the same name as the module folder.

Here is what looks like a typicall Illuminato module structure :

```
/illuminatocomments
	/assets   <= Here comes all the static datas, it's the only accessible folder (not implemented now)
		/css
		/js
	/config
		config.php <= The static module config or default values goes here
	/models          <= All eloquent models goes here
		comments.php
	/resources
		/lang     <= Illuminato use Laravel translation functionnalities
			/en
				module.php
			/fr
				module.php
		/views
			display.blade.php
	/updates      <= All migrations files goes here
		create_comment_table.php
		add_user_info_to_comment_table.php
		...
	illuminatocomments.php <= The module file
```

## Declaration

You must create inside your module file a class that extend Illuminato's Module class (which Extends Prestashop's Module class) and create a moduleDetails function that will tell all about your module.

```php

if (!defined('_PS_VERSION_'))
	exit;

class IlluminatoComments extends Illuminato\Module
{
	public function moduleDetails()
	{
		return [
			'tab' => 'front_office_features',
			'version' => '0.3',
			'author' => 'Andréas Livet',
			// Namespace use for localisation, config and views (default is module name)
			'namespace' => 'comments',
			// Note that we use Laravel's localization key system for localization
			'displayName' => 'comments::module.name',
			'description' => 'comments::module.description',
		];
	}
}
```

```Note : For now, Illuminato is only compatible with PrestaShop 1.6 and use Twitter bootstrap as default.```
```Note 2 : You don't have to precise the module name, Illuminato will take the folder's name and if it's not the same as the module file name's, it will throw an error.```
```Note 3 : If the displayName is not set, it will use the folder's name```


## Installation

The install phase is simplify cause you don't need to register to hook, if a function start with "hook" in your class Illuminato will automatically register it on install and unregister it on uninstall.

With migrations, all table creation process is automatic, you just have to put your migrations files into the updates folder and Illuminato will apply them every time the admin goes into admin panel (if there is a new one).

Here is an example of a migration file :

```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//This will create a table name ps_illuminato_comments with all the listed colums
		Schema::create('illuminato_comments', function(Blueprint $table)
		{
			$table->increments('id_illuminato_comments');
			$table->integer('id_product');
			$table->tinyInteger('grade');
			$table->text('comment');
			$table->dateTime('date_add');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('illuminato_comments');
	}

}
```

You may still need to use an install function to create configuration variable like this :

```php
public function install()
{
	// Call install parent method
	if (!parent::install())
		return false;
	// This create prestashop config value with the ILLUMINATOCOMMENT_ prefix
	$this->conf->set(['GRADE' => '1', 'COMMENTS' => '1']);

	return true;
}
```

Note that this may change in a near futur and configuration will be setted in migrations file also.

# What works

* Translation via Lang alias
* View with blande compiler via view alias
* Eloquant Model
* Migrations
* static config file via Config
* Html and Form builder via Html:: and Form::
* File
* Input

# What doesn't works or have not been tested

* Routes
* Controllers
* Flash
* Artisan cmd
* Event
* Mail
* Queue
* Cache
* Cookie
* Session
* Response
* Validator
* And so on...

# Roadmap

The goal is to be closer to October CMS plugin behaviour with controller, form definitions with YAML files etc.

* AdminController like October CMS list
* Module's config like October CMS Settings
* Admin settings interface simplify
* Laravel's routes