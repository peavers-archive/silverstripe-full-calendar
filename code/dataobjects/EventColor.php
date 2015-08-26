<?php

/**
 * Class EventColor
 *
 * @package: full-calendar
 */
class EventColor extends DataObject implements PermissionProvider
{
	private static $db = array(
		'Title'   => 'Varchar(255)',
		'HexCode' => 'Varchar(7)',
		'Type'    => 'Varchar(255)',
	);

	private static $has_one = array(
		'FullCalendar' => 'FullCalendar',
	);

	private static $defaults = array(
		'HexCode' => '#',
	);

	private static $summary_fields = array(
		'Title'   => 'Title',
		'HexCode' => 'HexCode',
		'Type'    => 'Can be used for',
	);

	public function getCMSFields()
	{
		$fields = parent::getCMSFields();

		$fields->addFieldsToTab("Root.Main", array(

				TextField::create('Title', 'Color name')
					->setDescription('A name to identify this color'),
				HexColorField::create('HexCode', 'Hex code')
					->setDescription("Remember to include the '#'")
					->setAttribute('Placeholder', '#000000'),
				OptionsetField::create('Type', 'Color type')
					->setDescription('Where is this color allowed to be used')
					->setSource(array(
						'Background' => 'Only for background colors',
						'Text'       => 'Only for text colors',
						'Both'       => 'For background and text colors',
					)),
			));

		$fields->removeByName('FullCalendarID');

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

	//
	// Permission providers
	//
	public function canEdit($member = null)
	{
		return Permission::check('FULL_CALENDAR_COLOR_EDIT');
	}

	public function canDelete($member = null)
	{
		return Permission::check('FULL_CALENDAR_COLOR_DELETE');
	}

	public function canCreate($member = null)
	{
		return Permission::check('FULL_CALENDAR_COLOR_CREATE');
	}

	public function canView($member = null)
	{
		return true;
	}

	public function providePermissions()
	{
		return array(
			'FULL_CALENDAR_COLOR_EDIT'   => array(
				'name'     => 'Edit colors',
				'category' => 'Full Calendar permissions',
			),
			'FULL_CALENDAR_COLOR_DELETE' => array(
				'name'     => 'Delete colors',
				'category' => 'Full Calendar permissions',
			),
			'FULL_CALENDAR_COLOR_CREATE' => array(
				'name'     => 'Create colors',
				'category' => 'Full Calendar permissions',
			),
		);
	}
}

