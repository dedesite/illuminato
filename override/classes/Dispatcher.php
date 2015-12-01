<?php
class Dispatcher extends DispatcherCore
{
	/**
	 * Bootstrap Illuminate before PrestaShop dispatch (first function call in prestashop)
	 */
	public function dispatch() {
		/*
		|--------------------------------------------------------------------------
		| Register The Auto Loader
		|--------------------------------------------------------------------------
		|
		| Composer provides a convenient, automatically generated class loader for
		| our application. We just need to utilize it! We'll simply require it
		| into the script here so that we don't have to worry about manual
		| loading any of our classes later on. It feels nice to relax.
		|
		*/
		require _PS_MODULE_DIR_.'/illuminato/src/bootstrap/autoload.php';
		
		// @todo Don't know why yet but this file isn't loaded with autoload...
		require_once _PS_MODULE_DIR_.'/illuminato/src/Module.php';

		/*
		|--------------------------------------------------------------------------
		| Turn On The Lights
		|--------------------------------------------------------------------------
		|
		| We need to illuminate PHP development, so let us turn on the lights.
		| This bootstraps the framework and gets it ready for use, then it
		| will load up this application so that we can run it and send
		| the responses back to the browser and delight our users.
		|
		*/
		$app = require_once _PS_MODULE_DIR_.'/illuminato/src/bootstrap/app.php';

		/*
		|--------------------------------------------------------------------------
		| Run The Application
		|--------------------------------------------------------------------------
		|
		| Once we have the application, we can handle the incoming request
		| through the kernel, and send the associated response back to
		| the client's browser allowing them to enjoy the creative
		| and wonderful application we have prepared for them.
		|
		*/

		$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

		$response = $kernel->handle(
			$request = Illuminate\Http\Request::capture()
		);

		/*
		 | Set locale from Prestashop context
		 */
		$context = \Context::getContext();
		$app->setLocale($context->language->iso_code);

		/*

		$response->send();

		$kernel->terminate($request, $response);
		*/
		parent::dispatch();
	}
}