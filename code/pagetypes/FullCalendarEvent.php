<?php

/**
 * Class FullCalendarEvent
 */
class FullCalendarEvent extends Page
{
    private static $singular_name = "Full Calendar event";

    private static $plural_name = "Full Calendar events";

    private static $description = "Event item that belongs has a start and end date";

    private static $can_be_root = false;

    private static $allowed_children = array();

    private static $db = array(
        'IncludeOnCalendar' => 'Boolean',
        'Title'             => 'Varchar(255)',
        'StartDate'         => 'Date',
        'EndDate'           => 'Date',
        'Url'               => 'Varchar(255)',
        'BackgroundColor'   => 'Varchar(7)',
        'TextColor'         => 'Varchar(7)',
        'ShortDescription'  => 'Varchar(255)'
    );

    private static $defaults = array(
        'IncludeOnCalendar' => true,
    );

    /**
     * Setup the basic CMS user fields
     *
     * @return mixed
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab("Root.Main", array(

            DateField::create("StartDate", "Start date"),
            DateField::create("EndDate", "End date"),

            DropdownField::create('IncludeOnCalendar', 'Include on calendar')
                ->setDescription('Should this event be shown on the calendar')
                ->setSource(array(
                    true  => "Yes",
                    false => "No"
                )),

            DropdownField::create("TextColor", "Text colour")
                ->setDescription('Colors are created via Full Calendar Settings')
                ->setSource(EventColor::get()
                    ->filter(array('FullCalendarID' => $this->ParentID))
                    ->where("Type = 'Both' OR Type = 'Text'")
                    ->sort(array("Title" => "ASC"))
                    ->map('HexCode', 'Title')),

            DropdownField::create("BackgroundColor", "Background colour")
                ->setDescription('Colors are created via Full Calendar Settings')
                ->setSource(EventColor::get()
                    ->filter(array('FullCalendarID' => $this->ParentID))
                    ->where("Type = 'Both' OR Type = 'Background'")
                    ->sort(array("Title" => "ASC"))
                    ->map('HexCode', 'Title')),

            TextareaField::create('ShortDescription', 'A short description')
                ->setDescription("Text shown when an event is first clicked on. Should be a quick description of the event. <strong>Limit 255 characters</strong>")

        ), "Content");

        return $fields;
    }

    /**
     * Clear the cache if a new event is written
     */
    public function onAfterWrite()
    {
        $cache = SS_Cache::factory('calendar');
        $cache->remove('events');

        parent::onAfterWrite();
    }
}

class FullCalendarEvent_Controller extends Page_Controller
{
    public function init()
    {
        parent::init();
    }
}