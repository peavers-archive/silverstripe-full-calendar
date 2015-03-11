<?php

/**
 * Class FullCalendarEvent
 */
class FullCalendarEvent extends Page
{
    private static $singular_name = "[Calendar] Event item";

    private static $plural_name = "[Calendar] Event items";

    private static $can_be_root = false;

    private static $allowed_children = array();

    private static $db = array(
        'IncludeOnCalendar' => 'Boolean',
        'Title'             => 'Varchar(255)',
        'StartDate'         => 'Date',
        'EndDate'           => 'Date',
        'Url'               => 'Varchar(255)',
        'BackgroundColor'   => 'Varchar(255)',
        'TextColor'         => 'Varchar(255)',
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

            DropdownField::create('IncludeOnCalendar', 'Include on calendar')
                ->setSource(array(true => "Yes", false => "No"))
                ->setEmptyString('(Select one)'),

            FieldGroup::create(
                DateField::create("StartDate", "Start date"),
                DateField::create("EndDate", "End date")
            )->setTitle("Event Time"),

            TextField::create("TextColor", "Text colour")
                ->setDescription("You can use any of the CSS color formats such #f00, #ff0000, rgb(255,0,0), or red."),

            TextField::create("BackgroundColor", "Background colour")
                ->setDescription("You can use any of the CSS color formats such #f00, #ff0000, rgb(255,0,0), or red."),


            HtmlEditorField::create("Content", "Page content")
        ));

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