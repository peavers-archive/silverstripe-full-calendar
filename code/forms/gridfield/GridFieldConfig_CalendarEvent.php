<?php

/**
 * Class GridFieldConfig_CalendarEvent.php
 * @package: full-calendar
 */
class GridFieldConfig_CalendarEvent extends GridFieldConfig_Lumberjack
{
    public function __construct($itemsPerPage = 50)
    {
        parent::__construct($itemsPerPage);

        $dataColumns = $this->getComponentByType('GridFieldDataColumns');
        $dataColumns->setDisplayFields(array(
            'Title' => 'Event',
            'StartDate.Nice' => 'Start date',
            'EndDate.Nice' => 'End date',
        ));

        $sortColumns = $this->getComponentByType('GridFieldSortableHeader');
        $sortColumns->setFieldSorting(array(
            'Title' => 'Title',
            'StartDate.Nice' => 'StartDate',
            'EndDate.Nice' => 'EndDate',
        ));
    }
}
