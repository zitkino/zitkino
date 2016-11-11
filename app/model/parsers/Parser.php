<?php
namespace Zitkino\parsers;
use DOMDocument, DOMXPath;

/**
 * Parser
 */
abstract class Parser {
	private $url = "";
	private $document;
	private $movies = [];
	
	public function getUrl() {
		return $this->url;
	}
	public function getMovies() {
		return $this->movies;
	}
	
	public function setUrl($url) {
		$this->url = $url;
	}
	public function setMovies($movies) {
		$this->movies = $movies;
	}
	
	/**
	 * Initiates DOM document.
	 */
	public function initiateDocument() {
		$this->document = new DOMDocument("1.0", "UTF-8");
		$this->document->formatOutput = true;
		$this->document->preserveWhiteSpace = true;
	}
	
	/**
	 * Downloads data from internet.
	 * @return DOMXPath XPath document for parsing.
	 */
	public function downloadData() {
		$handle = curl_init($this->url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_ENCODING, "UTF-8");
		$html = curl_exec($handle);
		libxml_use_internal_errors(true); // Prevent HTML errors from displaying
		$this->document->loadHTML(mb_convert_encoding($html, "HTML-ENTITIES", "UTF-8"));
		$xpath = new DOMXPath($this->document);
		
		return $xpath;
	}

	/**
	 * Gets movies and other data from the web page.
	 */
	abstract public function getContent();
}
