# Introduction

IlluminatoShop is a set of tools that allows Prestashop Module developpers to use Laravel 5 API inside PrestaShop.

For now, it is a nasty hack, maybe it will evolve into something cleaner.

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

