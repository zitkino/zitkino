<?php
namespace Zitkino;

use Nette\Application\Routers\{
	Route, RouteList
};


/**
 * Router factory.
 */
class RouterFactory {
	/**
	 * @return RouteList
	 */
	public function createRouter() {
		$router = new RouteList();
		
		$router[] = new Route("index[.php]", "Home:default", Route::ONE_WAY);
		$router[] = new Route("mapa[/]", "Home:map");
		$router[] = new Route("kontakt[/]", "Home:contact");
		$router[] = new Route("info[/]", "Home:about");
		
		$router[] = new Route("kina[/]", "Cinema:default");
		$router[] = new Route("kino[/]", "Cinema:default");
		$router[] = new Route("kino/<id>", "Cinema:profile");
		
		$router[] = new Route("klasicka[/]", "Cinema:classic");
		$router[] = new Route("klasicky/program", "Cinema:classic_programme");
		$router[] = new Route("klasicka/program", "Cinema:classic_programme");
		
		$router[] = new Route("multiplexy[/]", "Cinema:multiplex");
		$router[] = new Route("multiplex[/]", "Cinema:multiplex");
		$router[] = new Route("multiplexy/program", "Cinema:multiplex_programme");
		$router[] = new Route("multiplex/program", "Cinema:multiplex_programme");
		
		$router[] = new Route("letni[/]", "Cinema:summer");
		$router[] = new Route("letni/program", "Cinema:summer_programme");
		
		// old zitkino addresses
		$oldCinemas = ["kino-art-brno", "kinokavarna-brno", "kino-lucerna-brno", "kino-scala-brno", "cinema-city-olympia-brno", "cinema-city-velky-spalicek-brno", "letni-kino-na-dvore-mdb-brno"];
		$newCinemas = ["art", "kinokavarna", "lucerna", "scala", "olympia", "velkySpalicek", "mdb"];
		$i = 0;
		foreach($oldCinemas as $cinema) {
			$router[] = new Route("cinema/".$cinema, ["presenter" => "Cinema", "action" => "profile", "id" => $newCinemas[$i]], Route::ONE_WAY);
			$i++;
		}
		$router[] = new Route("cinema[/]", "Cinema:default", Route::ONE_WAY);
		$router[] = new Route("film[/<f=>]", "Home:default", Route::ONE_WAY);

		$router[] = new Route("<action>", "Home:default");
		$router[] = new Route("[<presenter>/]<action>", "Home:default");

		return $router;
	}
}
