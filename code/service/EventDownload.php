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

		file_put_contents('../'
			. $this->filePath, "BEGIN:VCALENDAR\n", FILE_APPEND);
		file_put_contents('../'
			. $this->filePath, "VERSION:2.0\n", FILE_APPEND);
	}

	/**
	 * @param $ID
	 */
	public function generateEventList($ID) {

		$events = FullCalendarEvent::get()->filter(array("ParentID" => $ID));

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

			file_put_contents('../' . $this->filePath, $content, FILE_APPEND);
		}

		file_put_contents('../'
			. $this->filePath, "END:VCALENDAR\n", FILE_APPEND);

	}

}
