<?php

/**
 * Class FullCalendar.
 */
class FullCalendar extends Page
{
	private static $singular_name = 'Full Calendar';

	private static $description = 'Calendar page that displays events';

	private static $can_be_root = true;

	private static $extensions = [
		'Lumberjack',
	];

	private static $db = [
		'LegacyEvents' => 'Boolean',
		'CalendarView' => 'Varchar(255)',
		'FirstDay' => 'Int',
		'ColumnFormat' => 'Varchar(255)',
	];

	private static $has_one = [
		'LoadAnimation' => 'Image',
		'CalFile' => 'File',
	];

	private static $defaults = [
		'CacheSetting' => '1',
		'LegacyEvents' => '0',
		'CalendarView' => 'month',
	];

	private static $allowed_children = [
		'FullCalendarEvent',
	];

	private static $icon = 'full-calendar/core/images/icons/sitetree_images/holder.png';

	public function getCMSFields()
	{
		$fields = parent::getCMSFields();

		$fields->addFieldsToTab('Root.FullCalendarSettings', TabSet::create('FullCalendarSettings',

			Tab::create('Functional Settings',
				DropdownField::create('LegacyEvents', 'Enable past events')
					->setDescription('Show events where the end date has passed today\'s date')
					->setSource([
						true => 'Yes',
						false => 'No',
					])
			),

			Tab::create('Display Settings',
				DropdownField::create('CalendarView', 'Calendar view')
					->setDescription('(<a href="http://fullcalendar.io/docs/views/Available_Views/" target="_blank">?</a>)')
					->setSource([
						'month' => 'Month',
						'basicWeek' => 'Basic week',
						'basicDay' => 'Basic day',
						'agendaWeek' => 'Agenda week',
						'agendaDay' => 'Agenda day',
					]),

				DropdownField::create('FirstDay', 'First day of the week')
					->setDescription('The day that each week begins.')
					->setSource([
						0 => 'Sunday',
						1 => 'Monday',
						2 => 'Tuesday',
						3 => 'Wednesday',
						4 => 'Thursday',
						5 => 'Friday',
						6 => 'Saturday',
					]),

				DropdownField::create('ColumnFormat', 'Column format')
					->setDescription("Determines the text that will be displayed on the calendar's column headings.")
					->setSource([
						'ddd' => 'Mon, Tues, Wed',
						'ddd M/D' => 'Mon 9/7, Tues 9/8, Wed 9/9',
						'dddd' => 'Monday, Tuesday, Wednesday',
					]),

				UploadField::create('LoadAnimation', 'Loading animation')
			)
		));


		return $fields;
	}

	/**
	 * Get the root of this page, used for the ajax call.
	 *
	 * @return $this
	 */
	public function getDocumentRoot()
	{
		return $this;
	}

	/**
	 * Create the .ics file and save it to the Calendar.
	 */
	public function onBeforeWrite()
	{
		// Write the ics file for the event
		$service = new IcsGenerator($this->Title);
		$service->generateEvent($this->ID, null);

		// Attach the file to this page
		$this->CalFileID = $service->getFileObject()->ID;

		parent::onBeforeWrite();
	}
}

/**
 * Class FullCalendar_Controller.
 */
class FullCalendar_Controller extends Page_Controller
{
	private static $allowed_actions = [
		'eventsAsJSON',
		'viewCalendarEvent',
	];

	private static $url_handlers = [
		'eventsAsJSON' => 'eventsAsJSON',
		'viewCalendarEvent/$ID!' => 'viewCalendarEvent',
	];

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

		Requirements::javascript(FULL_CALENDAR . '/theme/dist/js/main.js');
		Requirements::css(FULL_CALENDAR . '/theme/dist/css/main.css');
	}

	/**
	 * Ajax call to return all events to the calendar frontend, if told to use cache get the cached version otherwise
	 * create a new version of data to return.
	 *
	 * @param string $message
	 * @param string $status
	 *
	 * @return string
	 */
	public function eventsAsJSON($message = '', $status = 'success')
	{
		$this->getResponse()->addHeader('Content-Type', 'application/json; charset=utf-8');

		if ($status != 'success') {
			$this->setStatusCode(400, $message);
		}

		$filter = [
			'ParentID' => $this->ID,
			'IncludeOnCalendar' => true,
		];

		if (!$this->LegacyEvents) {
			$filter['StartDate:GreaterThanOrEqual'] = date('Y-m-d');
		}

		$result = [];
		foreach (FullCalendarEvent::get()->filter($filter) as $event) {
			$result[] = [
				// Calendar settings
				'view' => $this->CalendarView,
				'firstDay' => $this->FirstDay,
				'columnFormat' => $this->ColumnFormat,

				// Event data
				'title' => $event->Title,
				'start' => $event->StartDate,
				'end' => $event->EndDate,
				'allDay' => false,
				'fancybox' => Director::absoluteURL($this) . '/viewCalendarEvent/' . $event->ID,

				// Event settings
				'colorClass' => $event->EventColor,
				'textColor' => $event->TextColor,
				'className' => [
					'light-box',
					$event->EventColor,
					$event->TextColor,
				],
			];
		}

		return json_encode($result);
	}

	/**
	 * @return mixed
	 */
	public function getUpcomingEvents()
	{
		$filter = [
			'ParentID' => $this->ID,
			'IncludeOnCalendar' => true,
			'StartDate:GreaterThanOrEqual' => date('Y-m-d'),
		];

		return FullCalendarEvent::get()->filter($filter)->limit(5)->sort('StartDate ASC');
	}

	/**
	 * @param SS_HTTPRequest $request
	 *
	 * @return mixed
	 */
	public function viewCalendarEvent(SS_HTTPRequest $request)
	{
		return Page::get()->byId($request->param('ID'))->renderWith('FC_EventAjax');
	}
}
