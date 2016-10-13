<?php
namespace Zitkino\parsers;

/**
 * Parser
 */
abstract class Parser {
	/**
	 * Gets movies and other data from the web page.
	 */
	abstract public function getContent();
}
