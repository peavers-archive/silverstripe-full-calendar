<?php

/**
 * Class EventFilter
 */
class EventFilter extends Lumberjack
{
    public function updateCMSFields(FieldList $fields)
    {
        $events = FullCalendarEvent::get()->filter(array('ParentID' => $this->owner->ID))->sort('StartDate ASC');

        $gridField = EventFilter_GridField::create('CalendarEvents',
            $this->getLumberjackTitle(),
            $events,
            $this->owner->getLumberjackGridFieldConfig()
        );

        $fields->insertBefore(Tab::create('CalendarEvents', $this->getLumberjackTitle(), $gridField), 'Main');
    }
}

/**
 * Class EventFilter_GridField
 */
class EventFilter_GridField extends GridField
{
    public function transform(FormTransformation $transformation)
    {
        return $this;
    }
}
