(function ($) {
})(window.jQuery);

jQuery(function ($) {
	"use strict";

	var rootUrl = $("#calendar").data("root-url");

	var jsonData;

	/**
	 * Makes ajax call to gather events
	 */
	function loadCalendar() {
		$.ajax({
			url: rootUrl + "eventsAsJSON",
			type: "GET",
			cache: true,
			success: function (json) {
				jsonData = json;

				calendarSettings();
			}
		});
	}

	/**
	 * Settings for the calendar, see http://fullcalendar.io/docs/ for further options
	 *
	 * @param json
	 */
	function calendarSettings() {
		$('#calendar').fullCalendar({

			// Settings
			columnFormat: jsonData[0].columnFormat,
			defaultView: jsonData[0].view,
			firstDay: jsonData[0].firstDay,

			// Events
			events: jsonData,
			eventClick: function (event) {
				$('.title').html(event.title);
				$('.event-header').addClass(event.colorClass);
				$('.event-title').html(event.title).addClass(event.textColor);
				$('.event-start-date').html(event.startDate);
				$('.event-end-date').html(event.endDate);
				$('.event-download').find('a').attr('href', event.downloadLink);
				$('.event-description').html(event.shortContent);
				$('.event-button').show().find('a').attr('href', event.eventUrl);
				$('.fancybox-skin').addClass(event.textColor);

				fancyboxSettings(event.textColor, event.colorClass);
			}
		})
	}

	/**
	 * Fancybox
	 */
	function fancyboxSettings(textColor, backgroundColor) {
		$.fancybox({
			padding: '',
			width: 600,
			height: 325,
			scrolling: 'no',
			fitToView: true,
			autoCenter: true,
			autoSize: false,
			closeBtn: true,
			openEffect: 'fade',
			closeEffect: 'fade',
			'href': '#fancy-box',
			tpl: {
				closeBtn: '<a title="Close" id="close-button" class="fancybox-item fancybox-close custom-close ' + textColor + '"  href=""></a>'
			},
			helpers: {
				overlay: {
					locked: false
				}
			},

			// Remove the colour classes from everything
			beforeClose: function () {
				$('.event-header').removeClass(backgroundColor);
				$('.event-title').removeClass(textColor);
				$('#close-button').removeClass(textColor);
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
