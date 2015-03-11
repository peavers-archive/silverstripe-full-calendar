(function ($) {
})(window.jQuery);

jQuery(function ($) {
    "use strict";

    var rootUrl = $("#calendar").data("root-url");

    /**
     * Makes ajax call to gather events
     */
    function loadCalendar() {
        $.ajax({
            url: rootUrl + "/eventsAsJson",
            type: "GET",
            cache: true,
            success: function (json) {
                calendarSettings(json);
            }
        });
    }

    /**
     * Settings for the calendar, see http://fullcalendar.io/docs/ for further options
     *
     * @param json
     */
    function calendarSettings(json) {

        console.log(json);

        $('#calendar').fullCalendar({
            events: json,
            columnFormat: 'dddd',
            eventClick: function (event) {
                $('.event-header').html(event.title).css('background-color', event.color);
                $('#fancy-start-date').html(event.startDate);
                $('#fancy-end-date').html(event.endDate);
                $('.event-content').html(event.content);
                $('.event-button').attr('href', event.eventUrl);
                fancyboxSettings();
            }
        })
    }

    /**
     * Fancybox
     */
    function fancyboxSettings() {
        $.fancybox({
            padding: '',
            width: 600,
            height: 325,
            scrolling: 'no',
            fitToView: true,
            autoCenter: true,
            autoSize: false,
            closeBtn: true,
            openEffect: 'elastic',
            closeEffect: 'elastic',
            openSpeed: 350,
            closeSpeed: 250,
            'href': '#fancy-box',
            helpers: {
                overlay: {
                    locked: false
                }
            }
        });
    }

    /**
     * Tidies up the ajax function, removes the loader when ready.
     *
     * Ensures the calendar is hidden while loading regardless of CSS properties.
     */
    $(document).on({
        ajaxStart: function () {
            $('#calendar').css({'visibility': 'hidden', 'opacity': 0});
            $('.document-overlay').css({'visibility': 'visible', 'opacity': 1});
        },
        ajaxStop: function () {
            $('.document-overlay').css({'visibility': 'hidden', 'opacity': 0, 'display': 'none'});
            $('#calendar').css({'visibility': 'visible', 'opacity': 1});
        }
    });

    /**
     * Called when the page is ready
     */
    $(document).ready(function () {
        loadCalendar();
    });
});
