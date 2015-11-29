<?php

class CalendarEvent
{
	/**
	 *
	 * The event ID
	 * @var string
	 */
	private $uid;
	/**
	 * The event start date
	 * @var DateTime
	 */
	private $start;
	/**
	 * The event end date
	 * @var DateTime
	 */
	private $end;
	/**
	 *
	 * The event title
	 * @var string
	 */
	private $summary;
	/**
	 * The event description
	 * @var string
	 */
	private $description;
	/**
	 * The event location
	 * @var string
	 */
	private $location;

	public function __construct($parameters)
	{
		$parameters += array(
			'summary' => 'Untitled Event',
			'description' => '',
			'location' => ''
		);
		if (isset($parameters['uid'])) {
			$this->uid = $parameters['uid'];
		} else {
			$this->uid = uniqid(rand(0, getmypid()));
		}
		$this->start = $parameters['start'];
		$this->end = $parameters['end'];
		$this->summary = $parameters['summary'];
		$this->description = $parameters['description'];
		$this->location = $parameters['location'];
		return $this;
	}

	/**
	 * Get the start time set for the even
	 * @return string
	 */
	private function formatDate($date)
	{

		return $date->format('Ymd\THis\Z');
	}

	private function formatValue($str)
	{
		return addcslashes($str, ",\\;");
	}

	public function generateString()
	{
		$created = new DateTime();
		$content = '';
		$content = "BEGIN:VEVENT\r\n"
			. "UID:{$this->uid}\r\n"
			. "DTSTART:{$this->formatDate($this->start)}\r\n"
			. "DTEND:{$this->formatDate($this->end)}\r\n"
			. "DTSTAMP:{$this->formatDate($this->start)}\r\n"
			. "CREATED:{$this->formatDate($created)}\r\n"
			. "DESCRIPTION:{$this->formatValue($this->description)}\r\n"
			. "LAST-MODIFIED:{$this->formatDate($this->start)}\r\n"
			. "LOCATION:{$this->location}\r\n"
			. "SUMMARY:{$this->formatValue($this->summary)}\r\n"
			. "SEQUENCE:0\r\n"
			. "STATUS:CONFIRMED\r\n"
			. "TRANSP:OPAQUE\r\n"
			. "END:VEVENT\r\n";
		return $content;
	}
}

class Calendar
{
	protected $events;
	protected $title;
	protected $author;

	public function __construct($parameters)
	{
		$parameters += array(
			'events' => array(),
			'title' => 'Calendar',
			'author' => 'Calender Generator'
		);
		$this->events = $parameters['events'];
		$this->title = $parameters['title'];
		$this->author = $parameters['author'];
	}

	/**
	 *
	 * Call this function to download the invite.
	 */
	public function generateDownload()
	{
		$generated = $this->generateString();
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); //date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); //tell it we just updated
		header('Cache-Control: no-store, no-cache, must-revalidate'); //force revaidation
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		header('Content-type: text/calendar; charset=utf-8');
		header('Content-Disposition: inline; filename="calendar.ics"');
		header("Content-Description: File Transfer");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . strlen($generated));
		return $generated;
	}

	/**
	 *
	 * The function generates the actual content of the ICS
	 * file and returns it.
	 *
	 * @return string|bool
	 */
	public function generateString()
	{
		$content = "BEGIN:VCALENDAR\r\n"
			. "VERSION:2.0\r\n"
			. "PRODID:-//" . $this->author . "//NONSGML//EN\r\n"
			. "X-WR-CALNAME:" . $this->title . "\r\n"
			. "CALSCALE:GREGORIAN\r\n";

		foreach ($this->events as $event) {
			$content .= $event;
		}
		$content .= "END:VCALENDAR";
		return $content;
	}
}
