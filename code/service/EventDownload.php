<?php

/**
 * Class EventDownload
 */
class EventDownload
{

	/**
	 * @var
	 */
	private $file;

	/**
	 * @var
	 */
	private $filePath;

	/**
	 * @param $title
	 */
	public function __construct($title)
	{
		$this->setupFile($title);
	}

	/**
	 * @param $title
	 */
	private function setupFile($title)
	{
		$folder = Folder::find_or_make('/ics-files/');

		$this->file = new File();
		$this->file->SetName($title . ".ics");
		$this->file->setParentID($folder->ID);
		$this->file->write();

		$this->filePath = $this->file->getFullPath();
	}

	/**
	 * @param $string
	 * @throws ValidationException
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

	/**
	 * @param null $ID the id of the FullCalendar page to return events for
	 * @param null $singleEventID the ID of a single event
	 */
	public function generateEventList($ID = null, $singleEventID = null)
	{
		if (!is_null($ID)) {
			$events = FullCalendarEvent::get()->filter(array("ParentID" => $ID));
		} else {
			$events = DataObject::get_by_id('FullCalendarEvent', $singleEventID);
		}

		$this->addToFile("BEGIN:VCALENDAR\n");
		$this->addToFile("VERSION:2.0\n");

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

		return $this->file->getURL();
	}
}
