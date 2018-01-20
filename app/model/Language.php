<?php
namespace Zitkino;

/**
 * Language.
 */
class Language {
	/** @var string */
	private $code, $czech, $english;
	
	public function getCode() {
		return $this->code;
	}
	public function setCode($code) {
		$this->code = $code;
	}
	
	public function getCzech() {
		return $this->czech;
	}
	public function setCzech($czech) {
		$this->czech = $czech;
	}
	
	public function getEnglish() {
		return $this->english;
	}
	public function setEnglish($english) {
		$this->english = $english;
	}
}
