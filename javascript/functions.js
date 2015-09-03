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
				$('.description').html(event.shortContent);
				$('.event-header').html(event.title).addClass(event.colorClass).addClass(event.textColor);
				$('.event-start-date').html(event.startDate);
				$('.event-end-date').html(event.endDate);
				$('.event-content').html(event.shortContent);

				//AddThis values
				$('.start').html(event.addThisStartDate);
				$('.end').html(event.addThisEndDate);

				// Hide the button if you don't have any content to link through to.
				if (event.content == null) {
					$('.event-button').hide();
				} else {
					$('.event-button').show().find('a').attr('href', event.eventUrl).css({color: 'event.color'});
				}

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
				closeBtn: '<a title="Close" id="close-button" class="fancybox-item fancybox-close custom-close ' + textColor + '"  href="javascript:;"></a>'
			},
			helpers: {
				overlay: {
					locked: false
				}
			},

			// Remove the colour classes from everything
			beforeClose: function () {
				$('.event-header').removeClass(backgroundColor).removeClass(textColor);
				$('#close-button').removeClass(textColor);
			},

			// Refresh the addThis library data
			afterLoad: function () {
				addThisSettings();
			}
		});
	}

	/**
	 * Enables AddThis on the calendar
	 */
	function addThisSettings() {

		addthisevent.refresh();

		addthisevent.settings({
			css: false,
			outlook: {
				show: true,
				text: "Outlook"
			},
			google: {
				show: true,
				text: "Google <em>(online)</em>"
			},
			yahoo: {
				show: true,
				text: "Yahoo <em>(online)</em>"
			},
			outlookcom: {
				show: true,
				text: "Outlook.com <em>(online)</em>"
			},
			appleical: {
				show: true,
				text: "Apple Calendar"
			},
			dropdown: {
				order: "appleical,google,outlook,outlookcom,yahoo"
			}
		});
	}

	/**
	 * Download calendar all events
	 */
	function downloadCal() {
		$(".download-button").click(function () {
			var cal = ics();
			$.each(jsonData, function (idx, jsonData) {
				cal.addEvent(jsonData.title, jsonData.content, jsonData.eventUrl, jsonData.start, jsonData.end);
			});
			cal.download("Calendar Events", ".ics");
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
		downloadCal();
	});
});
