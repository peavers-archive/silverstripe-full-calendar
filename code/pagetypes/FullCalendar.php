<?php

/**
 * Class FullCalendar
 */
class FullCalendar extends Page
{
    private static $singular_name = 'Full Calendar';

    private static $description = 'Calendar page that displays events';

    private static $can_be_root = true;

    private static $allowed_children = array(
        'FullCalendarEvent'
    );

    private static $defaults = array(
        'CacheSetting' => '1',
        'LegacyEvents' => '0'
    );

    private static $db = array(
        'CacheSetting' => 'Boolean',
        'LegacyEvents' => 'Boolean'
    );

    private static $has_one = array(
        'LoadAnimation' => 'Image'
    );

    private static $has_many = array(
        'EventColor' => 'EventColor'
    );

    private static $icon = 'full-calendar/images/icons/sitetree_images/holder.png';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.FullCalendarSettings', array(

            HeaderField::create('', 'Functional settings'),
            CheckboxField::create('CacheSetting', 'Enable caching')
                ->setDescription('Should only disable for debugging/development purposes'),
            CheckboxField::create('LegacyEvents', 'Enable past events')
                ->setDescription('Show events where the end date has passed today\'s date'),

            HeaderField::create('', 'Display settings'),
            UploadField::create('LoadAnimation', 'Loading animation'),
            GridField::create('EventColor', 'Create color', $this->EventColor(), GridFieldConfig_RecordEditor::create()),
        ));

        return $fields;
    }

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
        'eventsAsJson',
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

        Requirements::combine_files('silverstripe-calendar.css', array(
            'full-calendar/css/lib/font-awesome.css',
            'full-calendar/css/lib/fullcalendar.css',
            'full-calendar/css/lib/jquery.fancybox.css',
            'full-calendar/css/style.css'
        ));

        Requirements::javascript('full-calendar/javascript/lib/moment.min.js');
        Requirements::combine_files('silverstripe-calendar.js', array(
            'full-calendar/javascript/lib/jquery.min.js',
            'full-calendar/javascript/lib/fullcalendar.min.js',
            'full-calendar/javascript/lib/jquery.fancybox.js',
            'full-calendar/javascript/functions.js',
        ));

        Requirements::set_combined_files_folder(ASSETS_DIR . '/_combinedfiles/calendar-module');

    }

    /**
     * Ajax call to return all events to the calendar frontend
     *
     * @param string $message
     * @param string $status
     * @return string
     */
    public function eventsAsJson($message = "", $status = "success")
    {
        $this->getResponse()->addHeader(
            'Content-Type',
            'application/json; charset=utf-8'
        );

        if ($status != "success") {
            $this->setStatusCode(400, $message);
        }

        if ($this->CacheSetting) {
            return $this->getCachedData();
        } else {
            return $this->getData();
        }
    }

    /**
     * Builds a cache of events if one doesn't exist, store the cache for 12 hours . The cache is cleared / reset
     * when a new event is published .
     *
     * @return json load of events to display
     */
    public function getCachedData()
    {
        $cache = SS_Cache::factory('calendar');
        SS_Cache::set_cache_lifetime('calendar', 60 * 60 * 12);

        if (!($result = unserialize($cache->load('events')))) {
            $result = $this->getData();
            $cache->save(serialize($result), 'events');
        }

        return $result;
    }

    /**
     * Decides what filter to use based on user settings, returns all events that match
     *
     * @return string
     */
    public function getData()
    {
        if ($this->LegacyEvents) {
            $filter = array(
                'IncludeOnCalendar' => true,
            );
        } else {
            $filter = array(
                'IncludeOnCalendar'   => true,
                'EndDate:GreaterThan' => date("Y-m-d")
            );
        }

        $result = array();
        foreach (FullCalendarEvent::get()->filter($filter) as $event) {

            $result[] = array(
                "title"     => $event->Title,
                "start"     => $event->StartDate,
                "end"       => $event->EndDate,
                "color"     => $event->BackgroundColor,
                "textColor" => $event->TextColor,

                "startDate" => $this->getDateFormat($event, 'StartDate'),
                "endDate"   => $this->getDateFormat($event, 'EndDate'),

                "eventUrl"  => $event->URLSegment,
                "content"   => $this->getShortDescription($event),
            );
        }
        return json_encode($result);
    }

    /**
     * Returns the description, or a placeholder message if no description
     *
     * @param $event
     * @return string either a description or a message saying no description
     */
    public function getShortDescription($event)
    {
        $event = strip_tags($event->ShortDescription);

        if ($event == "") {
            return "No description is set";
        } else {
            return $event;
        }
    }

    /**
     * Returns a formatted date
     *
     * @param $event
     * @param $date
     * @return bool|string
     */
    public function getDateFormat($event, $date)
    {
        return date('l jS F Y', strtotime($event->$date));
    }

}
