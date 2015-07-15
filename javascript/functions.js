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
            url: rootUrl + "eventsAsJSON",
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

        $('#calendar').fullCalendar({
            // Settings
            columnFormat: json[0].columnFormat,
            defaultView: json[0].view,
            firstDay: json[0].firstDay,

            // Events
            events: json,
            eventClick: function (event) {
                $('.event-header').html(event.title).css({'background-color': event.color, 'color': event.textColor});
                $('.event-start-date').html(event.startDate);
                $('.event-end-date').html(event.endDate);
                $('.event-content').html(event.shortContent);

                // Hide the button if you don't have any content to link through to.
                if (event.content == null) {
                    $('.event-button').hide();
                } else {
                    $('.event-button').show().find('a').attr('href', event.eventUrl).css({color: 'event.color'});
                }
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
            tpl: {
                closeBtn: '<a title="Close" class="fancybox-item fancybox-close custom-close" href="javascript:;"></a>'
            },
            openEffect: 'fade',
            closeEffect: 'fade',
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
