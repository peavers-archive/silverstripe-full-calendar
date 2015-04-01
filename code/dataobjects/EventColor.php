<?php

/**
 * Class EventColor
 * @package: full-calendar
 */
class EventColor extends DataObject
{

    private static $db = array(
        'Title'   => 'Varchar(255)',
        'HexCode' => 'Varchar(7)',
        'Type'    => 'Varchar(255)',
    );

    private static $has_one = array(
        'FullCalendar' => 'FullCalendar'
    );

    private static $defaults = array(
        'HexCode' => "#"
    );

    private static $summary_fields = array(
        'Title'   => 'Title',
        'HexCode' => 'HexCode',
        'Type'    => 'Allowed'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab("Root.Main", array(

                TextField::create('Title', 'Friendly name')
                    ->setDescription("A name to identify this color"),

                TextField::create('HexCode', 'Hex code')
                    ->setDescription("Remember to include the '#'")
                    ->setAttribute('Placeholder', "#000000"),

                OptionsetField::create('Type', 'Color type')
                    ->setSource(array(
                        'Background' => 'Only for background colors',
                        'Text'       => 'Only for Text colors',
                        'Both'       => 'For background and text colors',
                    ))
                    ->setDescription('Where is this color allowed to be used'),
            )
        );

        $fields->removeByName("FullCalendarID");

        return $fields;
    }

    /**
     * Just to make sure the CMS looks pretty, force a capital letter and the HexCode to lowercase
     */
    public function onBeforeWrite()
    {
        $this->Title = ucfirst($this->Title);
        $this->HexCode = strtolower($this->HexCode);

        parent::OnBeforeWrite();
    }

}
