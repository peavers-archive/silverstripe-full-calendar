<?php

/**
 * Class FullCalendar
 */
class FullCalendar extends Page
{
    private static $singular_name = "[Calendar] Page";

    private static $plural_name = "[Calendar] Page";

    private static $can_be_root = true;

    private static $allowed_children = array(
        "FullCalendarEvent"
    );

    private static $db = array(
        'CacheSetting' => 'Boolean',
        'LegacyEvents' => 'Boolean'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.FullCalendarSettings', array(
            HeaderField::create("", "General settings"),
            CheckboxField::create("CacheSetting", 'Enable caching')
                ->setDescription("Should only disable for debugging/development purposes"),
            HeaderField::create("", "Display settings"),
            CheckboxField::create("LegacyEvents", 'Enable past events')
                ->setDescription("Show events where the end date has passed today's date"),
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
            "calendar/css/lib/fullcalendar.css",
            "calendar/css/lib/jquery.fancybox.css",
            "//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css",
            "calendar/css/style.css"
        ));

        Requirements::javascript("calendar/javascript/lib/moment.min.js");
        Requirements::combine_files('silverstripe-calendar.js', array(
            "calendar/javascript/lib/jquery.min.js",
            "calendar/javascript/lib/fullcalendar.min.js",
            "calendar/javascript/lib/jquery.fancybox.js",
            "calendar/javascript/functions.js",
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
            "Content-Type",
            "application/json; charset=utf-8",
            "Cache-Control: public, max-age=290304000"
        );

        if ($status != "success") {
            $this->setStatusCode(400, $message);
        }

        if ($this->CacheSetting) {
            return $this->cachedData();
        } else {
            return $this->getData();
        }
    }

    /**
     * Builds a cache of events if one doesn't exist, store the cache for 12 hours . The cache is cleared / reset
     * when a new event is published .
     *
     * Only return events that are set to IncludeOnCalendar and the EndDate is greater than today(Don't show
     * legacy events)
     *
     * @return json load of events to display
     */
    public function cachedData()
    {
        $cache = SS_Cache::factory('calendar');
        SS_Cache::set_cache_lifetime('calendar', 60 * 60 * 12);

        if (!($result = unserialize($cache->load('events')))) {
            $result = $this->getData();
            $cache->save(serialize($result), 'events');
        }

        return $result;
    }

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

                "startDate" => date('l jS F Y', strtotime($event->StartDate)),
                "endDate"   => date('l jS F Y', strtotime($event->EndDate)),

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

}
