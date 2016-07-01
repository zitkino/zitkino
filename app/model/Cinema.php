<?php
namespace zitkino;

//require_once __DIR__ . '/../../libs/nette/nette.phar';
//use Nette\Database\Connection;

require_once "Database.php";
use lib\Net\Database;

/**
 * Description of Cinema
 */
class Cinema {
	private $id, $name, $shortName, $type, $address, $city, $url, $programme, $facebook, $twitter, $googleplus;
	
	public function __construct($id) {
//		$connection = new Connection("mysql:host=localhost;dbname=zitkino", "root", "");
//		$co=$connection->query('SELECT * FROM cinemas WHERE id=?', "1")->dump();
//		var_dump($co);		
		
		$db = new Database("localhost", "root", "", "zitkino");
		
		$this->id = $id;
		$this->name = $db->returning("SELECT `name` FROM `cinemas` WHERE id=$this->id");
		$this->shortName = $db->returning("SELECT `short_name` FROM `cinemas` WHERE id=$this->id");
		$this->type = $db->returning("SELECT `type` FROM `cinemas` WHERE id=$this->id");
		$this->address = $db->returning("SELECT `address` FROM `cinemas` WHERE id=$this->id");
		$this->city = $db->returning("SELECT `city` FROM `cinemas` WHERE id=$this->id");
		$this->url = $db->returning("SELECT `url` FROM `cinemas` WHERE id=$this->id");
		$this->programme = $db->returning("SELECT `programme` FROM `cinemas` WHERE id=$this->id");
		$this->facebook = $db->returning("SELECT `facebook` FROM `cinemas` WHERE id=$this->id");
		$this->twitter = $db->returning("SELECT `twitter` FROM `cinemas` WHERE id=$this->id");
		$this->googleplus = $db->returning("SELECT `google+` FROM `cinemas` WHERE id=$this->id");
	}
	
	function getId() {
		return $this->id;
	}

	function getName() {
		return $this->name;
	}

	function getShortName() {
		return $this->shortName;
	}

	function getType() {
		return $this->type;
	}

	function getAddress() {
		return $this->address;
	}

	function getCity() {
		return $this->city;
	}

	function getUrl() {
		return $this->url;
	}

	function getProgramme() {
		return $this->programme;
	}

	function getFacebook() {
		return $this->facebook;
	}

	function getTwitter() {
		return $this->twitter;
	}

	function getGoogleplus() {
		return $this->googleplus;
	}


}