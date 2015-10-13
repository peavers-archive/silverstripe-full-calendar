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
			'ShortDescription' => 'Description',
			'StartDate' => 'Start date',
			'EndDate' => 'End date',
		));

		$sortColumns = $this->getComponentByType('GridFieldSortableHeader');
		$sortColumns->setFieldSorting(array(
			'Title' => 'Title',
			'ShortDescription' => 'ShortDescription',
			'StartDate' => 'StartDate',
			'EndDate' => 'EndDate',
		));
	}
}
