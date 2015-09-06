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
			$events = FullCalendarEvent::get()->filter(array("ParentID" => $fullCalendarID));
		} else {
			$events = DataObject::get_by_id('FullCalendarEvent', $singleEventID);
		}

		$this->addToFile("BEGIN:VCALENDAR\n" . "VERSION:2.0\n");

		foreach ($events as $event) {
			$content = "BEGIN:VEVENT\n";
			$content .= "CLASS:PUBLIC\n";
			$content .= "DESCRIPTION:{$this->escapeString($event->ShortDescription)}\n";
			$content .= "DTSTART:{$this->dateToCal(strtotime($event->StartDate . "H:i:s"))}\n";
			$content .= "DTEND:{$this->dateToCal(strtotime($event->EndDate . "H:i:s"))}\n";
			$content .= "LOCATION:(null)\n";
			$content .= "TRANSP:TRANSPARENT\n";
			$content .= "END:VEVENT\n";

			$this->addToFile($content);
		}

		$this->addToFile("END:VCALENDAR\n");
	}

	/**
	 * @param $string
	 */
	private function addToFile($string)
	{
		file_put_contents($this->filePath, $string, FILE_APPEND);
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
