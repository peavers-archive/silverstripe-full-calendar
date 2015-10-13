(function ($) {
})(window.jQuery);

jQuery(function ($) {
	"use strict";

	/**
	 * Used to fetch ajax call
	 *
	 * @type {*|jQuery}
	 */
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
			timeFormat: 'H:mm',
			eventRender: function (event, element) {
				element.attr('data-fancybox-href', event.fancybox);
				element.attr('title', event.title);
				element.find('.fc-time').hide();
			},
			eventClick: function () {
				$('.light-box').fancybox({
					scrolling: 'hidden',
					padding: 0,
					autoSize: false,
					fitToView: false,
					width: 600,
					height: 325,
					autoCenter: true,
					closeBtn: true,
					openEffect: 'fade',
					closeEffect: 'fade',
					type: 'ajax',
					helpers: {
						title: null,
						overlay: {
							locked: false
						}
					},
					beforeShow: function () {
						$("body").css({'overflow-y': 'hidden'});
					},
					afterClose: function () {
						$("body").css({'overflow-y': 'visible'});
					}
				});
			}
		})
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
