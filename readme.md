## Synopsis

A lightweight calendar module for Silverstripe. Implements the popular javascript http://fullcalendar.io/ library.

## Features
* Lightbox event details
* Stylish colour inheritance based on user selection
* Hide past events automatically
* Easily change the style/theme to match your brand
* Change between month and agenda views
* Page type for a list of upcoming events

## Installation

### Composer
Ideally composer will be used to install this module. 
```composer require "moe/full-calendar:@stable"```

### From source
1. Download the latest [release] (https://github.com/peavers/silverstripe-full-calendar/releases)
1. Extract the files
1. Make sure the folder after being extracted is named 'full-calendar'
1. Upload to your site root

## Basic modification

### Javascript
Common settings can be changed from the CMS, but to add additional options just add
them to the calendarSettings. 
```javascript
function calendarSettings(json) {
    $('#calendar').fullCalendar({
        //custom settings here
    })
}
```
For more setting options see http://fullcalendar.io/docs/

### CSS
All libraries that have been imported are using their current versions default files. All modifications have been made and commented in a single file named style.css
Add to this file to customize the look and feel. 

## CMS Setup/Usage

### Page types
* Full Calendar - This is the holder for all events, also contains the main calendar. Create this first.
* Full Calendar Event - A unique event, needs to be created as a child to Full Calendar
* Full Calendar Event List - A single page that displays events in a list. This should be created as a child of Full 
Calendar

### Colors
To create new colours, visit the Full Calendar Settings tab on the Full Calendar page type. Each colour requires a 
title and hex value. Colours can be limited to only being used as text and or backgrounds. A permission group has 
been created to limit the people that can create colours. 

## Screen shots

### Calendar View
![Calendar view](https://github.com/peavers/silverstripe-full-calendar/blob/master/images/screens/calendar.png?raw=true "Calendar view")
---------------------------------------
### Event view
![Event view](https://github.com/peavers/silverstripe-full-calendar/blob/master/images/screens/calendar-event.png?raw=true "Event view")

## Libraries used
* Fullcalendar
* Moment.js
* Fancybox v2
* Font Awesome
