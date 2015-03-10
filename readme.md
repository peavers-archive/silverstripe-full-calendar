## Synopsis

A lightweight calendar module for Silverstripe. Implements the popular javascript http://fullcalendar.io/ library.

## Basic modification

Adding to the calendarSettings javascript function allows easy changes to functions
```
function calendarSettings(json) {
    $('#calendar').fullCalendar({
        events: json,
        firstDay: 1,
        columnFormat: 'dddd'
    })
}
```
The only required setting here is `events`. For more setting options please see http://fullcalendar.io/docs/

