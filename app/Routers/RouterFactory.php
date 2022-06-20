<?php
namespace Zitkino\Routers;

use Nette\Application\Routers\{Route, RouteList};

/**
 * Router factory.
 */
class RouterFactory {
	public function createRouter(): RouteList {
		$router = new RouteList();
		
		$router->addRoute("index[.php]", "Home:default", Route::ONE_WAY);
		$router->addRoute("mapa[/]", "Home:map");
		$router->addRoute("kontakt[/]", "Home:contact");
		$router->addRoute("info[/]", "Home:about");
		
		$router->addRoute("kina[/]", "Cinema:default");
		$router->addRoute("kino[/]", "Cinema:default");
		$router->addRoute("kino/<id>", "Cinema:profile");
		
		$router->addRoute("klasicka[/]", ["presenter" => "Cinema", "action" => "type", "type" => "classic"]);
		$router->addRoute("klasicky[/]", ["presenter" => "Cinema", "action" => "type", "type" => "classic"]);
		$router->addRoute("klasicky/program", ["presenter" => "Cinema", "action" => "programme", "type" => "classic"]);
		$router->addRoute("klasicka/program", ["presenter" => "Cinema", "action" => "programme", "type" => "classic"]);
		
		$router->addRoute("multiplexy[/]", ["presenter" => "Cinema", "action" => "type", "type" => "multiplex"]);
		$router->addRoute("multiplex[/]", ["presenter" => "Cinema", "action" => "type", "type" => "multiplex"]);
		$router->addRoute("multiplexy/program", ["presenter" => "Cinema", "action" => "programme", "type" => "multiplex"]);
		$router->addRoute("multiplex/program", ["presenter" => "Cinema", "action" => "programme", "type" => "multiplex"]);
		
		$router->addRoute("letni[/]", ["presenter" => "Cinema", "action" => "type", "type" => "summer"]);
		$router->addRoute("letni/program", ["presenter" => "Cinema", "action" => "programme", "type" => "summer"]);
		
		// old zitkino addresses
		$oldCinemas = ["kino-art-brno", "kinokavarna-brno", "kino-lucerna-brno", "kino-scala-brno", "cinema-city-olympia-brno", "cinema-city-velky-spalicek-brno", "letni-kino-na-dvore-mdb-brno"];
		$newCinemas = ["art", "kinokavarna", "lucerna", "scala", "olympia", "velky-spalicek", "mdb"];
		$i = 0;
		foreach($oldCinemas as $cinema) {
			$router->addRoute("cinema/".$cinema, ["presenter" => "Cinema", "action" => "profile", "id" => $newCinemas[$i]], Route::ONE_WAY);
			$i++;
		}
		$router->addRoute("cinema[/]", "Cinema:default", Route::ONE_WAY);
		$router->addRoute("film[/<f=>]", "Home:default", Route::ONE_WAY);

//		$router->addRoute("<action>", "Home:default");
		$router->addRoute("[<presenter>/]<action>", "Home:default");
		
		return $router;
	}
}
