<?php

/**
 * Class IcsGenerator.
 */
class IcsGenerator
{
    /**
     * Silverstripe object that is the file.
     *
     * @var
     */
    private $fileObject;

    /**
     * Relative location of the file on the server.
     *
     * @var
     */
    private $filePath;

    /**
     * Name of the file.
     *
     * @var
     */
    private $fileName;

    /**
     * sets up base file and folder ready for file generating.
     *
     * @param $filename
     */
    public function __construct($filename)
    {
        $folder = Folder::find_or_make('/ics-files/'.$filename);

        $this->fileName = strtolower($filename);

        $this->fileObject = new File();
        $this->fileObject->SetName($this->fileName.'.ics');
        $this->fileObject->setParentID($folder->ID);
        $this->fileObject->write();

        $this->filePath = $this->fileObject->getFullPath();
    }

    /**
     * @return mixed
     */
    public function getFileObject()
    {
        return $this->fileObject;
    }

    /**
     * @param $fullCalendarID int the id of the FullCalendar page to return events for
     * @param $singleEventID int the fullCalendarID of a single event
     */
    public function generateEventList($fullCalendarID = null, $singleEventID = null)
    {
        $events = !is_null($fullCalendarID) ? FullCalendarEvent::get()->filter(['ParentID' => $fullCalendarID]) : FullCalendarEvent::get()->filter(['ID' => $singleEventID])->first();

        file_put_contents($this->filePath, '');

        // Events
        $calendarEvents = [];
        foreach ($events as $event) {
            $params = [
                'start'       => new DateTime($event->StartDate),
                'end'         => new DateTime($event->EndDate),
                'summary'     => $event->Title,
                'description' => strip_tags($event->ShortDescription),
                'location'    => '',
            ];
            $calendarEvent = new CalendarEvent($params);
            array_push($calendarEvents, $calendarEvent->generateString());
        }

        // Calendar
        $calendarParams = [
            'events' => $calendarEvents,
            'title'  => 'Calendar',
            'author' => 'CalenderGenerator',
        ];

        $calendar = new Calendar($calendarParams);
        file_put_contents($this->filePath, $calendar->generateDownload(), FILE_APPEND);
    }
}
