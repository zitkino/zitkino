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
		
		$router->addRoute("klasicka[/]", "Cinema:classic");
		$router->addRoute("klasicky[/]", "Cinema:classic");
		$router->addRoute("klasicky/program", "Cinema:classic_programme");
		$router->addRoute("klasicka/program", "Cinema:classic_programme");
		
		$router->addRoute("multiplexy[/]", "Cinema:multiplex");
		$router->addRoute("multiplex[/]", "Cinema:multiplex");
		$router->addRoute("multiplexy/program", "Cinema:multiplex_programme");
		$router->addRoute("multiplex/program", "Cinema:multiplex_programme");
		
		$router->addRoute("letni[/]", "Cinema:summer");
		$router->addRoute("letni/program", "Cinema:summer_programme");
		
		// old zitkino addresses
		$oldCinemas = ["kino-art-brno", "kinokavarna-brno", "kino-lucerna-brno", "kino-scala-brno", "cinema-city-olympia-brno", "cinema-city-velky-spalicek-brno", "letni-kino-na-dvore-mdb-brno"];
		$newCinemas = ["art", "kinokavarna", "lucerna", "scala", "olympia", "velkySpalicek", "mdb"];
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
