<?php

/**
 * Class EventDownload
 */
class EventDownload
{

	/**
	 * Silverstripe object that is the file
	 *
	 * @var
	 */
	private $fileObject;

	/**
	 * Relative location of the file on the server
	 *
	 * @var
	 */
	private $filePath;

	/**
	 * Name of the file
	 *
	 * @var
	 */
	private $fileName;

	/**
	 * sets up base file and folder ready for file generating
	 * @param $filename
	 */
	public function __construct($filename)
	{
		$folder = Folder::find_or_make('/ics-files/');

		$this->fileName = strtolower($filename);

		$this->fileObject = new File();
		$this->fileObject->SetName($this->fileName . ".ics");
		$this->fileObject->setParentID($folder->ID);
		$this->fileObject->write();

		$this->filePath = $this->fileObject->getFullPath();
	}

	/**
	 * @return mixed
	 */
	public function getFileObject()
	{
		return $this->fileObject;
	}

	/**
	 * @param $fullCalendarID int the id of the FullCalendar page to return events for
	 * @param $singleEventID int the fullCalendarID of a single event
	 */
	public function generateEventList($fullCalendarID = null, $singleEventID = null)
	{
		if (!is_null($fullCalendarID)) {
			$events = FullCalendarEvent::get()->filter(array('ParentID' => $fullCalendarID));
		} else {
			$events = FullCalendarEvent::get()->filter(array('ID' => $singleEventID))->first();
		}

		// Nuke current the file contents
		file_put_contents($this->filePath, '');

		$this->addToFile(
			"BEGIN:VCALENDAR\r\n" .
			"VERSION:2.0\r\n" .
			"METHOD:PUBLISH\r\n" .
			"PRODID:-//hacksw/handcal//NONSGML v1.0//EN\r\n" .
			"CALSCALE:GREGORIAN\r\n"
		);

		foreach ($events as $event) {
			$this->addToFile(
				"BEGIN:VEVENT\r\n" .
				"CLASS:PUBLIC\r\n" .
				"UID:{$this->generateRandomString()}\r\n" .
				"DTSTART:{$this->dateToCal(strtotime($event->StartDate))}\r\n" .
				"DTEND:{$this->dateToCal(strtotime($event->EndDate))}\r\n" .
				"DESCRIPTION:{$this->escapeString($event->ShortDescription)}\r\n" .
				"SUMMARY;LANGUAGE=en-gb:{$this->escapeString($event->Title)}\r\n" .
				"SEQUENCE:0\r\n" .
				"STATUS:NEEDS-ACTION\r\n" .
				"TRANSP:OPAQUE\r\n" .
				"END:VEVENT\r\n"
			);
		}

		$this->addToFile("END:VCALENDAR\r\n");
	}

	/**
	 * @param $string
	 */
	private function addToFile($string)
	{
		file_put_contents($this->filePath, $string, FILE_APPEND);
	}

	/**
	 * @param int $length
	 * @return string
	 */
	function generateRandomString($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	/**
	 * @param $timestamp
	 *
	 * @return bool|string
	 */
	private function dateToCal($timestamp)
	{
		return date('Ymd\THis\Z', $timestamp);
	}

	/**
	 * @param $string
	 *
	 * @return mixed
	 */
	private function escapeString($string)
	{
		$string = strip_tags($string);
		$string = substr($string, 0, 100);

		return preg_replace('/([\,;])/', '\\\$1', $string);
	}
}
