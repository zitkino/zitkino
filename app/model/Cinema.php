<?php
namespace Zitkino;
use \Lib\Net\Database;

/**
 * Description of Cinema
 */
class Cinema {
	private $id, $name, $shortName, $type, $address, $city, $gmaps, $url, $programme, $facebook, $twitter, $googleplus;

	public function __construct($id) {
		$db = new Database(__DIR__."/../database.ini");
		
		if(is_numeric($id)) {
			$this->id = $id;
			$this->name = $db->returning("SELECT `name` FROM `cinemas` WHERE id='".$this->id."'");
			$this->shortName = $db->returning("SELECT `shortName` FROM `cinemas` WHERE id='".$this->id."'");
			$this->type = $db->returning("SELECT `type` FROM `cinemas` WHERE id='".$this->id."'");
			$this->address = $db->returning("SELECT `address` FROM `cinemas` WHERE id='".$this->id."'");
			$this->city = $db->returning("SELECT `city` FROM `cinemas` WHERE id='".$this->id."'");
			$this->gmaps = $db->returning("SELECT `gmaps` FROM `cinemas` WHERE id='".$this->id."'");
			$this->url = $db->returning("SELECT `url` FROM `cinemas` WHERE id='".$this->id."'");
			$this->programme = $db->returning("SELECT `programme` FROM `cinemas` WHERE id='".$this->id."'");
			$this->facebook = $db->returning("SELECT `facebook` FROM `cinemas` WHERE id='".$this->id."'");
			$this->twitter = $db->returning("SELECT `twitter` FROM `cinemas` WHERE id='".$this->id."'");
			$this->googleplus = $db->returning("SELECT `google+` FROM `cinemas` WHERE id='".$this->id."'");
		}
		else {
			$this->id = $db->returning("SELECT `id` FROM `cinemas` WHERE `shortName`='".$id."'");
			$this->name = $db->returning("SELECT `name` FROM `cinemas` WHERE shortName='".$id."'");
			$this->shortName = $id;
			$this->type = $db->returning("SELECT `type` FROM `cinemas` WHERE shortName='".$id."'");
			$this->address = $db->returning("SELECT `address` FROM `cinemas` WHERE shortName='".$id."'");
			$this->city = $db->returning("SELECT `city` FROM `cinemas` WHERE shortName='".$id."'");
			$this->gmaps = $db->returning("SELECT `gmaps` FROM `cinemas` WHERE shortName='".$id."'");
			$this->url = $db->returning("SELECT `url` FROM `cinemas` WHERE shortName='".$id."'");
			$this->programme = $db->returning("SELECT `programme` FROM `cinemas` WHERE shortName='".$id."'");
			$this->facebook = $db->returning("SELECT `facebook` FROM `cinemas` WHERE shortName='".$id."'");
			$this->twitter = $db->returning("SELECT `twitter` FROM `cinemas` WHERE shortName='".$id."'");
			$this->googleplus = $db->returning("SELECT `google+` FROM `cinemas` WHERE shortName='".$id."'");
		}
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
	
	function getGmaps() {
		return $this->gmaps;
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
