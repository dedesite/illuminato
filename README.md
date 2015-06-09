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

Yes, it uses namespace and composer so it needs PHP 5.4 at least so don't expect it to be able on the official addons store.

# Installation

Since IlluminatoShop uses namespace and also the `use` keyword, you need to remove the `eval` code in `classes/module/Module.php` in `getModulesOnDisk` function.

Old code (line 1242 in Prestashop 1.6.14) :
```
if (eval('if (false){	'.$file.' }') !== false)
	require_once( _PS_MODULE_DIR_.$module.'/'.$module.'.php' );
else
	$errors[] = sprintf(Tools::displayError('%1$s (parse error in %2$s)'), $module, substr($filepath, strlen(_PS_ROOT_DIR_)));

```

New code :
```
require_once( _PS_MODULE_DIR_.$module.'/'.$module.'.php' );
```

# Create a module

## Declaration

You will need to include PrestUils.php and extends SimpleModule and then simply put the needed informations.
Note : For now, PrestaUtils is only compatible with PrestaShop 1.6 and use Twitter bootstrap as default.
Note 2 : You don't have to precise the module name, PrestaUtils will take the folder's name and if it's not the same as the module file name's, it will throw an error.
Note 3 : If the displayName is not set, it will use the folder's name

Nothing really different from a "normal" PrestaShop module.

## Installation

The install phase is simplify cause you don't need to register to hook, PrestaUtils do it for you.
You also don't need to addJs or CSS at header hook, it PrestaUtils find it for you at install, it will add it automatically.

## Configuration

To avoid long variables names and collision in configuration, the `Config` tool automatically add your module name to the config var names.

So instead of :
```
$enable_grades = Configuration::get('MYMOD_GRADES');
```

You can just do : 
```
$enable_grades = Config::get('GRADES');
```

Which will do exacly the same.

