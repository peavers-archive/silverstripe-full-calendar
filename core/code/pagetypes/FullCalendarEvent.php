<?php

/**
 * Class FullCalendarEvent.
 */
class FullCalendarEvent extends Page
{
    private static $singular_name = 'Full Calendar event';

    private static $plural_name = 'Full Calendar events';

    private static $description = 'Event item that belongs has a start and end date';

    private static $can_be_root = false;

    private static $show_in_sitetree = false;

    private static $allowed_children = [];

    private static $summary_fields = [
        'Title'     => 'Title',
        'StartDate' => 'StartDate',
        'EndDate'   => 'EndDate',
    ];

    private static $field_labels = [
        'Title'     => 'Title',
        'StartDate' => 'StartDate',
        'EndDate'   => 'EndDate',
    ];

    private static $db = [
        'IncludeOnCalendar' => 'Boolean',
        'Title'             => 'Varchar(255)',
        'StartDate'         => 'SS_Datetime',
        'EndDate'           => 'SS_Datetime',
        'Url'               => 'Varchar(255)',
        'EventColor'        => 'Varchar(255)',
        'TextColor'         => 'Varchar(255)',
        'ShortDescription'  => 'HTMLText',
        'CalFileURL'        => 'Varchar(255)',
    ];

    /**
     * Setup the basic CMS user fields.
     *
     * @return mixed
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            FieldGroup::create(
                DatetimeField::create('StartDate', 'Starts'),
                DatetimeField::create('EndDate', 'Ends'))
                ->setTitle('Event dates'),

            ColorSwabField::create('EventColor', 'Event colour'),

            OptionsetField::create('TextColor', 'Text colour')
                ->setSource([
                    'text-black' => 'Black',
                    'text-white' => 'White',
                ])
                ->setDescription('Depending on the background colour, you may want to use black or white text'),

            OptionsetField::create('IncludeOnCalendar', 'Include on calendar')
                ->setDescription('Should this event be shown on the calendar')
                ->setSource([
                    true  => 'Yes',
                    false => 'No',
                ]),

            HtmlEditorField::create('ShortDescription', 'A short description')
                ->setRows(1)
                ->setDescription('Text shown when an event is first clicked on. Should be a quick description of the event. <strong>Limit 255 characters</strong>'),

        ], 'Content');

        return $fields;
    }

    /**
     * Full calendar will return an error if you're missing one of these values.
     *
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return new RequiredFields([
            'StartDate',
            'EndDate',
            'EventColor',
            'TextColor',
        ]);
    }
}

/**
 * Class FullCalendarEvent_Controller.
 */
class FullCalendarEvent_Controller extends Page_Controller
{

}
