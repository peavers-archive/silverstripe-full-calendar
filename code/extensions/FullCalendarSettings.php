<?php

/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 3/11/2015
 * Time: 9:57 PM
 */
class FullCalendarSettings extends DataExtension
{
    private static $db = array(
        'Cache' => 'Boolean(1)'
    );

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab('Root.FullCalendarSettings', array(
            HeaderField::create("", "Developer"),
            CheckboxField::create("Cache", 'Disable caching')
                ->setDescription("Should only disable for debugging/development purposes"),
            HeaderField::create("", "User"),
        ));
    }
}