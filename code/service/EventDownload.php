<?php

/**
 * Class EventDownload
 */
class EventDownload {

	/**
	 * @var
	 */
	private $file;

	/**
	 * @var
	 */
	private $filePath;

	/**
	 *
	 */
	public function __construct() {

		$this->setupFile();
	}

	/**
	 * @param $timestamp
	 *
	 * @return bool|string
	 */
	function dateToCal($timestamp) {

		return date('Ymd\THis\Z', $timestamp);
	}

	/**
	 * @param $string
	 *
	 * @return mixed
	 */
	function escapeString($string) {

		$string = strip_tags($string);
		$string = substr($string, 0, 100);

		return preg_replace('/([\,;])/', '\\\$1', $string);
	}

	public function addToFile($string) {

		file_put_contents('../' . $this->filePath, $string, FILE_APPEND);
	}

	/**
	 *
	 */
	public function setupFile() {

		$folder = Folder::find_or_make('/CalendarFiles/');
		$this->filePath = 'assets/CalendarFiles/' . date("H:i:s") . 'test.ics';

		$this->file = new File();
		$this->file->Name = $this->filePath;
		$this->file->Filename = $this->filePath;
		$this->file->ParentID = $folder->ID;

		$this->file->write();

		$this->addToFile("BEGIN:VCALENDAR\n");
		$this->addToFile("VERSION:2.0\n");
	}

	/**
	 * @param $ID
	 */
	public function generateEventList($ID) {

		$events = FullCalendarEvent::get()
			->filter(array("ParentID" => $ID));

		$content = null;
		foreach ($events as $event) {
			$start = $this->dateToCal(strtotime($event->StartDate . "H:i:s"));
			$end = $this->dateToCal(strtotime($event->EndDate . "H:i:s"));
			$description = $this->escapeString($event->ShortDescription);

			$content .= "BEGIN:VEVENT\n";
			$content .= "CLASS:PUBLIC\n";
			$content .= "DESCRIPTION:{$description}\n";
			$content .= "DTSTART:{$start}\n";
			$content .= "DTEND:{$end}\n";
			$content .= "LOCATION:(null)\n";
			$content .= "TRANSP:TRANSPARENT\n";
			$content .= "END:VEVENT\n";

			$this->addToFile($content);
		}

		$this->addToFile("END:VCALENDAR\n");
	}

}
