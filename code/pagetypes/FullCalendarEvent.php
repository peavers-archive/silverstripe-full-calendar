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

    private static $show_in_sitetree = false;

    private static $allowed_children = array();

    private static $summary_fields = array(
        'Title' => 'Title',
        'StartDate' => 'StartDate',
        'EndDate' => 'EndDate',
    );

    private static $field_labels = array(
        'Title' => 'Title',
        'StartDate' => 'StartDate',
        'EndDate' => 'EndDate',
    );

    private static $db = array(
        'IncludeOnCalendar' => 'Boolean',
        'Title' => 'Varchar(255)',
        'StartDate' => 'SS_Datetime',
        'EndDate' => 'SS_Datetime',
        'Url' => 'Varchar(255)',
        'EventColor' => 'Varchar(255)',
        'TextColor' => 'Varchar(255)',
        'ShortDescription' => 'HTMLText',
        'CalFileURL' => 'Varchar(255)',
    );

    private static $has_one = array(
        'CalFile' => 'File'
    );

    private static $defaults = array(
        'IncludeOnCalendar' => true,
        'TextColor' => 'text-black',
        'EventColor' => 'color-blue-600',
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
            FieldGroup::create(
                DatetimeField::create("StartDate", "Starts"),
                DatetimeField::create("EndDate", "Ends"))
                ->setTitle("Event dates"),

            ColorSwabField::create('EventColor', 'Event colour'),

            OptionsetField::create('TextColor', 'Text colour')
                ->setSource(array(
                    'text-black' => 'Black',
                    'text-white' => 'White',
                ))
                ->setDescription('Depending on the background colour, you may want to use black or white text'),

            OptionsetField::create('IncludeOnCalendar', 'Include on calendar')
                ->setDescription('Should this event be shown on the calendar')
                ->setSource(array(
                    true => "Yes",
                    false => "No",
                )),

            HtmlEditorField::create('ShortDescription', 'A short description')
                ->setRows(1)
                ->setDescription("Text shown when an event is first clicked on. Should be a quick description of the event. <strong>Limit 255 characters</strong>"),

        ), "Content");

        return $fields;
    }

    /**
     * Sets the Date field to the current date.
     */
    public function populateDefaults()
    {
        $this->StartDate = date('Y-m-d 07:00:00');
        $this->EndDate = date('Y-m-d 07:00:00');

        parent::populateDefaults();
    }

    /**
     * Full calendar will return an error if you're missing one of these values.
     *
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return new RequiredFields(array(
            'StartDate',
            'EndDate',
            'EventColor',
            'TextColor',
        ));
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // Make sure a valid date range is entered
        if (DateTime::createFromFormat('Y-m-d', $this->StartDate) > DateTime::createFromFormat('Y-m-d', $this->EndDate)) {
            throw new ValidationException("End date cannot occur before start date");
        }

        // Make sure something is set...
        if ($this->ShortDescription === "") {
            $this->ShortDescription = "(No description set)";
        }

        // Write the ics file for the event
        $service = new IcsGenerator($this->Title);
        $service->generateEventList(null, $this->ID);

        // Attach the file to this page
        $this->CalFileID = $service->getFileObject()->ID;
        $this->CalFileURL = $service->getFileObject()->getURL();
    }
}

/**
 * Class FullCalendarEvent_Controller
 */
class FullCalendarEvent_Controller extends Page_Controller
{
}
