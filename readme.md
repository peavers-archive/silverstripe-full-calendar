## Synopsis

A lightweight calendar module for Silverstripe. Implements the popular javascript http://fullcalendar.io/ library.

## Features
* Lightbox event details
* Stylish colour inheritance based on user selection
* Hide past events automatically
* Easily change the style/theme to match your brand

## Basic modification

### Javascript
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

### CSS
All libraries that have been imported are using their current versions default flies. All modifications have been made and commented in a single file named style.css
Add to this file to customize the look and feel. 

## Screen shots

### Calendar View
![Calendar view](https://dl.dropboxusercontent.com/u/5616402/Website%20Hosting/Github%20Images/full-calendar/calendar.png "Calendar view")
---------------------------------------
### Event view
![Event view](https://dl.dropboxusercontent.com/u/5616402/Website%20Hosting/Github%20Images/full-calendar/calendar-event.png "Event view")

## Libraries used
* Fullcalendar
* Moment.js
* Fancybox v2
* Font Awesome