<?php

use Jsvrcek\ICS\CalendarExport;
use Jsvrcek\ICS\CalendarStream;
use Jsvrcek\ICS\Model\Calendar;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Relationship\Attendee;
use Jsvrcek\ICS\Model\Relationship\Organizer;
use Jsvrcek\ICS\Utility\Formatter;

/**
 * Class IcsGenerator.
 */
class IcsGenerator
{
	/**
	 * Silverstripe object that is the file.
	 *
	 * @var
	 */
	private $fileObject;

	/**
	 * Relative location of the file on the server.
	 *
	 * @var
	 */
	private $filePath;

	/**
	 * Name of the file.
	 *
	 * @var
	 */
	private $fileName;

	public function getFileObject()
	{
		return $this->fileObject;
	}

	/**
	 * sets up base file and folder ready for file generating.
	 *
	 * @param $filename
	 */
	public function __construct($filename)
	{
		$folder = Folder::find_or_make('/ics-files/' . $filename);

		$this->fileName = strtolower($filename);

		$this->fileObject = new File();
		$this->fileObject->SetName($this->fileName . '.ics');
		$this->fileObject->setParentID($folder->ID);
		$this->fileObject->write();

		$this->filePath = $this->fileObject->getFullPath();
	}

	/**
	 * Get all events from a specific calendar, put them into a .ics file.
	 *
	 * @param $fullCalendarID
	 */
	public function generateEvent($fullCalendarID)
	{
		if (!is_null($fullCalendarID)) {
			$calendarPage = FullCalendar::get()->filter(['ID' => $fullCalendarID]);
			$events = FullCalendarEvent::get()->filter(['ParentID' => $fullCalendarID]);
		}

		$calendar = new Calendar();
		$calendar->setProdId('-//' . $calendarPage->Title . '//EN');

		foreach ($events as $event) {
			$organizer = new Organizer(new Formatter());
			$organizer->setLanguage('en');

			$item = new CalendarEvent();
			$item
				->setStart(new \DateTime($event->StartDate))
				->setEnd(new \DateTime($event->EndDate))
				->setSummary(addcslashes($event->Title, ',\\;'))
				->setUid(uniqid(rand(0, getmypid())))
				->setStatus('CONFIRMED')
				->setOrganizer($organizer);

			$calendar->addEvent($item);
		}

		$calendarExport = new CalendarExport(new CalendarStream, new Formatter());
		$calendarExport->addCalendar($calendar);

		file_put_contents($this->filePath, $calendarExport->getStream());
	}
}
