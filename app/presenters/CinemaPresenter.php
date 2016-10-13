<?php
namespace App\Presenters;
use Nette;

/**
 * Cinema presenter.
 */
class CinemaPresenter extends BasePresenter {
	public function beforeRender() {
        BasePresenter::beforeRender();
		
        $this->template->menuExists=false;
        //$this->template->menuURL='Home/menu.latte';
	}
	public function renderProfile($id)	{
		$cinema = new \Zitkino\Cinema($id);
		$this->template->cinema = $cinema;
		
		$parser = "\Zitkino\parsers\\".ucfirst($cinema->getShortName())."Parser";
		if(class_exists($parser)) {
			$pa = new $parser();
			$this->template->movies = $pa->getMovies();
		}
		else {
			$this->template->movies = null;
		}
		
		$gmaps = $cinema->getGmaps();
		if(is_null($gmaps)) {
			$address = $cinema->getAddress().", ".$cinema->getCity();
			$param = "/v1/place?q=".urlencode($address);
		}
		else {
			$param = "?pb=".$gmaps;
		}
		$this->template->gmap = $param;
	}    
}