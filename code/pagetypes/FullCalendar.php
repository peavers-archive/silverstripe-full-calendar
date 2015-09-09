<?php

/**
 * Class FullCalendar
 */
class FullCalendar extends Page
{

	private static $singular_name = 'Full Calendar';

	private static $description = 'Calendar page that displays events';

	private static $can_be_root = true;

	private static $db = array(
		'LegacyEvents' => 'Boolean',
		'CalendarView' => 'Varchar(255)',
		'FirstDay' => 'Int',
		'ColumnFormat' => 'Varchar(255)',
	);

	private static $has_one = array(
		'LoadAnimation' => 'Image',
		'CalFile' => 'File',
	);

	private static $defaults = array(
		'CacheSetting' => '1',
		'LegacyEvents' => '0',
		'CalendarView' => 'month',
	);

	private static $allowed_children = array(
		'FullCalendarEvent'
	);

	private static $extensions = array(
		'Lumberjack',
	);

	private static $icon = 'full-calendar/images/icons/sitetree_images/holder.png';

	public function getCMSFields()
	{

		$fields = parent::getCMSFields();

		$fields->addFieldsToTab('Root.FullCalendarSettings', array(

			// Functional, how things work
			ToggleCompositeField::create('', 'Functional Settings', array(
				CheckboxField::create('LegacyEvents', 'Enable past events')
					->setDescription('Show events where the end date has passed today\'s date'),
			)),

			// Display, how the calendar looks to the end user
			ToggleCompositeField::create('', 'Display Settings', array(

				DropdownField::create('CalendarView', 'Calendar view')
					->setDescription('(<a href="http://fullcalendar.io/docs/views/Available_Views/" target="_blank">?</a>)')
					->setSource(array(
						'month' => 'Month',
						'basicWeek' => 'Basic week',
						'basicDay' => 'Basic day',
						'agendaWeek' => 'Agenda week',
						'agendaDay' => 'Agenda day',
					)),

				DropdownField::create('FirstDay', 'First day of the week')
					->setDescription('The day that each week begins.')
					->setSource(array(
						0 => 'Sunday',
						1 => 'Monday',
						2 => 'Tuesday',
						3 => 'Wednesday',
						4 => 'Thursday',
						5 => 'Friday',
						6 => 'Saturday',
					)),

				DropdownField::create('ColumnFormat', 'Column format')
					->setDescription("Determines the text that will be displayed on the calendar's column headings.")
					->setSource(array(
						'ddd' => 'Mon, Tues, Wed',
						'ddd M/D' => 'Mon 9/7, Tues 9/8, Wed 9/9',
						'dddd' => 'Monday, Tuesday, Wednesday',
					)),

				UploadField::create('LoadAnimation', 'Loading animation'),
			)),
		));

		return $fields;
	}

	/**
	 * Generate the .ics file and attach it to this page
	 */
	public function onBeforeWrite()
	{
		parent::onBeforeWrite();

		// Write the ics file for the event
		$service = new IcsGenerator($this->Title);
		$service->generateEventList($this->ID, null);

		// Attach the file to this page
		$this->CalFileID = $service->getFileObject()->ID;
	}

	/**
	 * Get the root of this page, used for the ajax call
	 *
	 * @return $this
	 */
	public function getDocumentRoot()
	{
		return $this;
	}
}

/**
 * Class FullCalendar_Controller
 */
class FullCalendar_Controller extends Page_Controller
{

	private static $allowed_actions = array(
		'eventsAsJSON',
	);

	/**
	 * Blocks default silverstripe jquery, and loads all required JS and CSS.
	 *
	 * Note: moment.min.js breaks javascript minimisation so is excluded from the
	 * combine_files call.
	 */
	public function init()
	{
		parent::init();

		Requirements::block(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::block(THIRDPARTY_DIR . '/jquery-ui/jquery-ui.js');

		Requirements::combine_files('full-calendar.css', array(
			FULL_CALENDAR . '/css/style.css'
		));

		Requirements::javascript(FULL_CALENDAR . '/javascript/lib/moment.min.js');
		Requirements::combine_files('full-calendar.js', array(
			FULL_CALENDAR . '/javascript/lib/jquery.min.js',
			FULL_CALENDAR . '/javascript/lib/fullcalendar.min.js',
			FULL_CALENDAR . '/javascript/lib/jquery.fancybox.js',
			FULL_CALENDAR . '/javascript/functions.js',
		));
	}

	/**
	 * Ajax call to return all events to the calendar frontend, if told to use cache get the cached version otherwise
	 * create a new version of data to return
	 *
	 * @param string $message
	 * @param string $status
	 *
	 * @return string
	 */
	public function eventsAsJSON($message = "", $status = "success")
	{
		$this->getResponse()->addHeader('Content-Type', 'application/json; charset=utf-8');

		if ($status != "success") {
			$this->setStatusCode(400, $message);
		}

		return $this->getData();
	}

	/**
	 * Decides what filter to use based on user settings, returns all events that match
	 *
	 * @return string
	 */
	public function getData()
	{

		$filter = array(
			'ParentID' => $this->ID,
			'IncludeOnCalendar' => true,
		);

		if (!$this->LegacyEvents) {
			$filter['StartDate:GreaterThan'] = date("Y-m-d");
		}

		$result = array();
		foreach (FullCalendarEvent::get()->filter($filter) as $event) {

			$result[] = array(

				// Calendar settings
				"view" => $this->CalendarView,
				"firstDay" => $this->FirstDay,
				"columnFormat" => $this->ColumnFormat,

				// Event data
				"title" => $event->Title,
				"start" => $event->StartDate,
				"end" => $event->EndDate,
				"downloadLink" => $event->CalFileURL,

				// Event settings
				"colorClass" => $event->EventColor,
				"textColor" => $event->TextColor,
				"className" => array(
					$event->EventColor,
					$event->TextColor,
				),

				// Lightbox data
				'startDate' => date('l jS F Y', strtotime($event->StartDate)),
				'endDate' => date('l jS F Y', strtotime($event->EndDate)),
				"eventUrl" => $event->URLSegment,
				"shortContent" => strip_tags($event->ShortDescription),
			);
		}

		return json_encode($result);
	}
}
