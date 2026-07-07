<?php
namespace App\Parsers;

use App\Exceptions\ParserException;
use Nette\Utils\{Json, JsonException};

/**
 * Lucerna parser.
 */
class Lucerna extends Parser {
	/**
	 * @throws ParserException
	 */
	public function parse(): void {
		$data = $this->downloadData();
		
		$payload = [];
		preg_match_all('/self\.__next_f\.push\(\[1,"(.*?)"\]\)/s', $data, $matches);
		foreach($matches[1] as $match) {
			$payload[] = str_replace(['\\"', '\\\\', '\\/'], ['"', '\\', '/'], $match);
		}
		
		foreach($payload as $jsonPayload) {
			$eventsData = $this->extractEventsFromPayload($jsonPayload);
			if($eventsData === null) {
				continue;
			}
			
			$prefixes = ["PŘEDPREMIÉRA | ", "THE BEST OF 2025: "];
			$suffixes = [" | titulky", " | dabing"];
			
			$events = $eventsData["events"];
			foreach($events as $event) {
				$name = $event["names"]["cs"] ?? $event["names"]["en"] ?? "";
				foreach($prefixes as $prefix) {
					if(str_starts_with($name, $prefix)) {
						$name = substr($name, strlen($prefix));
						break; // remove only the first matching prefix
					}
				}
				
				foreach($suffixes as $suffix) {
					if(str_ends_with($name, $suffix)) {
						$name = substr($name, 0, -strlen($suffix));
						break; // remove only the first matching suffix
					}
				}
				
				$link = $event["ecommerceEventURL"] ?? null;
				$startsAt = \DateTime::createFromFormat("Y-m-d\TH:i:s.v\Z", $event["startsAt"], new \DateTimeZone("UTC"));
				$datetime = $startsAt->setTimezone(new \DateTimeZone("Europe/Prague"));
				
				$price = null;
				
				$format = $event["formatTranslated"]["cs"] ?? null;
				if($format === "2D projekce") {
					$format = "2D";
				}
				
				$type = $event["marketingLabel"]["name"] ?? null;
				
				$dubbing = null;
				$subtitles = null;
				$version = $event["versionTranslated"]["cs"] ?? null;
				switch($version) {
					case "Český dabing":
					case "Český dubbing":
						$dubbing = "česky";
						break;
					case "České titulky":
						$subtitles = "české";
						break;
				}
				
				$movie = $this->parserService->builderService->movie(name: $name);
				$screening = $this->parserService->builderService->screening(cinema: $this->cinema, movie: $movie, format: $format, type: $type, dubbing: $dubbing, subtitles: $subtitles, price: $price, link: $link, showtimes: [$datetime]);
				
				$this->cinema->addScreening($screening);
			}
		}
		
		$this->cinema->parsed = new \DateTime();
  $this->parserService->cinemaRepository->save($this->cinema);
	}
	
	private function extractEventsFromPayload(string $jsonPayload): ?array {
		$jsonPayload = trim($jsonPayload);
		$jsonPayload = preg_replace('/^\w+:/', '"payload":', $jsonPayload, 1);
		$jsonPayload = preg_replace('/\\\n$/', "", $jsonPayload);
		
		try {
			$decoded = Json::decode("{".$jsonPayload."}", true);
		} catch(JsonException $e) {
			return null;
		}
		return $this->findEvents($decoded);
	}
	
	private function findEvents(mixed $node): ?array {
		if(!is_array($node)) {
			return null;
		}
		
		if(array_key_exists("events", $node) && is_array($node["events"])) {
			return $node;
		}
		
		foreach($node as $value) {
			$result = $this->findEvents($value);
			if($result !== null) {
				return $result;
			}
		}
		
		return null;
	}
}

