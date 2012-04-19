<?php if (FALSE) : ?>
	<script type="text/javascript">
<?php endif; ?>
	elgg.provide('hj.events.base');

	hj.events.base.init = function() {
		$('#calendar_start_datepicker').datepicker({
			dateFormat: 'dd-mm-yy',
			minDate: 0,
			onSelect: function(dateText) {
				$('#calendar_end_datepicker').datepicker("setDate", dateText);
			}
		});

		$('#calendar_end_datepicker').datepicker({
			dateFormat: 'dd-mm-yy',
			minDate: 0,
			onSelect: function(dateText, inst) {
				var dateEndParts = dateText.split("-");
				var timestampEnd = Date.UTC(dateEndParts[2], dateEndParts[1] - 1, dateEndParts[0]);
				timestampEnd = timestampEnd / 1000;

				var dateStart = $('#calendar_start_datepicker').datepicker("getDate");
				var dateStartParts = $.datepicker.formatDate('dd-mm-yy', dateStart).split('-');
				var timestampStart = Date.UTC(dateStartParts[2], dateStartParts[1] - 1, dateStartParts[0]);
				timestampStart = timestampStart / 1000;
				
				if (timestampEnd < timestampStart) {
					elgg.system_message(elgg.echo('hj:events:endbeforestart'));
					$('#calendar_end_datepicker').datepicker("setDate", dateStart);
				}
			}
		});

		$('select[name="calendar_start[time]"]')
		.unbind('change')
		.bind('change', function(event) {
			$('select[name="calendar_end[time]"]').val($(this).val());
		});

		$('select[name="calendar_start[timezone]"]')
		.unbind('change')
		.bind('change', function(event) {
			$('select[name="calendar_end[timezone]"]').val($(this).val());
		});

		$('.hj-ajaxed-rsvp')
		.unbind('click')
		.bind('click', function(event) {
			event.preventDefault();

			var action = $(this).attr('href'),
			values = $(this).data('options'),
			source = $(this);

			elgg.action(action, {
				success : function() {
					source.addClass('hj-rsvp-confirmed');
					source.parent('li').siblings().each(function() {
						$(this).find('a').removeClass('hj-rsvp-confirmed');
					})
				}
			});
		});

		$('#hj-events-calendar-datepicker')
		.css({'position':'relative'})
		.datepicker({
			dateFormat: 'yy-mm-dd',
			onSelect: function(dateText) {
				var dateParts = dateText.split("-");
				var timestamp = Date.UTC(dateParts[0], dateParts[1] - 1, dateParts[2]);
				timestamp = timestamp / 1000;
				elgg.system_message(elgg.echo('hj:events:searching'));
				elgg.action('action/events/search', {
					data: {
						date : timestamp
					},
					success : function(output) {
						$('#hj-events-module > .elgg-body')
						.html(output.output);
						elgg.trigger_hook('success', 'hj:framework:ajax');
					}
				})
			}
		});

		$('#hj-events-search')
		.unbind('submit')
		.bind('submit', function(event) {
			event.preventDefault();
			var data = $(this).serialize();

			elgg.action('action/events/search', {
				data : data,
				success : function(output) {
					$('#hj-events-module > .elgg-body')
					.html(output.output);
					elgg.trigger_hook('success', 'hj:framework:ajax');
				}
			})
		})

		$('.hj-dt-to-img').each(function() {
			$div = $(this).next('.hj-dt-to-img-div');
			if ($div.html().length == 0) {
				var dtToParse = new Date($(this).val());
				$(this).next('.hj-dt-to-img-div').calendarIcon(dtToParse);
			}
		})
	}

	elgg.register_hook_handler('init', 'system', hj.events.base.init);
	elgg.register_hook_handler('success', 'hj:framework:ajax', hj.events.base.init, 500);

// jQuery-calendaricon script by Branko Vukelic
// https://github.com/HerdHound/jQuery-calendarIcon/blob/master/jquery.calendar-icon.js
(function ($) {
    var dateIconClasses = 'calendar-icon-container';
    var dateIconMonthClasses = 'calendar-icon-month calendar-icon-top';
    var dateIconYearClasses = 'calendar-icon-year calendar-icon-top';
    var dateIconDateClasses = 'calendar-icon-date';
    var dateIconDayClasses = 'calendar-icon-day';

    $.fn.calendarIcon = function(date) {
        var daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fry', 'Sat'];
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var year, day, month, dayOfWeek, dateIcon;
        //var currentYear = (new Date()).getFullYear();
		var currentYear = '';

        if (typeof date === 'string') {
            date = Date.parse(date);
        }

        year = date.getFullYear() !== currentYear && '`' + (date.getFullYear() + '').slice(2) || '';
        day = '' + date.getDate();
        month = months[date.getMonth()];
        dayOfWeek = daysOfWeek[date.getDay()];

        dateIcon = $('<div>')
        .addClass(dateIconClasses)
        .appendTo(this);

        $('<span>')
        .addClass(dateIconMonthClasses)
        .text(month)
        .appendTo(dateIcon);

        $('<span>')
        .addClass(dateIconYearClasses)
        .text(year)
        .appendTo(dateIcon);

        $('<span>')
        .addClass(dateIconDateClasses)
        .text(day)
        .appendTo(dateIcon);

        $('<span>')
        .addClass(dateIconDayClasses)
        .text(dayOfWeek)
        .appendTo(dateIcon);

        return this;

    };
})(jQuery);

<?php if (FALSE) : ?></script><?php endif; ?>