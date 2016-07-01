<?php
namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route;

/**
 * Router factory.
 */
class RouterFactory {
	/**
	 * @return Nette\Application\IRouter
	 */
	public function createRouter() {
		$router = new RouteList();
		
		Route::$defaultFlags = Route::SECURED;
		
		$router[] = new Route('index[.php]', 'Home:default', Route::ONE_WAY);
			
		$router[] = new Route('<action>', 'Home:default');
		//$router[] = new Route('[<presenter>]/index[.php]', 'Home:default');
		//$router[] = new Route('[<presenter>/]<action>/index[.php]', 'Home:default');
		$router[] = new Route('[<presenter>/]<action>', 'Home:default');

		return $router;
	}

}
