<?php

/**
 * Class FullCalendarEventList
 */
class FullCalendarEventList extends Page
{

    private static $singular_name = 'Full Calendar event list';

    private static $description = 'Display all events in a paginated list';

    private static $can_be_root = false;

    private static $allowed_children = array();

    private static $show_in_sitetree = true;

}

/**
 * Class FullCalendarEventList_Controller
 */
class FullCalendarEventList_Controller extends Page_Controller
{

    /**
     * Return a list of events but we don't care about past events so exclude anything that ended before today.
     * Sort the list so that the next upcoming event is at the top, and work down from there.
     *
     * @return mixed
     */
    public function getEvent()
    {

        $filter = array(
            'ParentID'            => $this->Parent()->ID,
            'EndDate:GreaterThan' => date("Y-m-d")
        );

        return PaginatedList::create(FullCalendarEvent::get()->filter($filter)->sort("EndDate ASC"), $this->request);
    }
}
