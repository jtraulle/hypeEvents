.hj-events-rsvp-menu {
	font-size:11px;
	height:auto;
	margin:5px 0;
}

.hj-events-rsvp-menu > li {
	margin:0 3px;
}

.hj-events-rsvp-menu > li > a:hover {
	text-decoration:underline;
}

.hj-rsvp-confirmed {
	font-size:12px;
	font-weight:bold;
	color:#00539F;
}

.elgg-module-eventicon {
	width: 80px;
	height: 80px;
	margin-bottom: 0;
}

a > .elgg-module-eventicon {
	color:#4682B4;
}

a:hover > .elgg-module-eventicon {
	text-decoration:none;
}

.elgg-module-eventicon > .elgg-head {
	margin-bottom:0;
	border-bottom:0;
	height: 20px;
	text-align: center;
	padding: 5px 0 3px;
	background: #F0F8FF;
	color: #4682B4;
	-moz-border-radius: 10px 10px 0 0;
	-webkit-border-radius: 10px 10px 0 0;
	border-radius: 10px 10px 0 0;
	text-transform: uppercase;
}

.elgg-module-eventicon > .elgg-body {
	padding: 10px;
	font-size: 35px;
	font-weight: bold;
	border: 2px solid #F0F8FF;
	line-height: 27px;
	text-align: center;
	-moz-border-radius: 0 0 10px 10px;
	-webkit-border-radius: 0 0 10px 10px;
	border-radius: 0 0 10px 10px;

}



/* ***************************
CALENDAR ICONS
https://github.com/HerdHound/jQuery-calendarIcon/blob/master/jquery.calendar-icon.css
**************************** */

.hj-dt-to-img-div {
	width:80px;
	height:80px;
	padding: 0 10px 20px 10px
}

.calendar-icon-container {
	/* Reset all relevant styles */
	position: relative;
	display: block;
	width: 79px;
	height: 80px;
	background: transparent url('<?php echo elgg_get_site_url() ?>mod/hypeEvents/graphics/Blue.png') no-repeat;
	color: #FFF;
	font-family: Arial, Helvetica, Sans, "Liberation Sans", sans-serif;
	font-size: 12px;
	font-weight: bold;
	margin:0 auto;
}

.calendar-icon-top {
	display: block;
	position: absolute;
	top: 1px;
}

.calendar-icon-month {
	left: 6px;
	font-size: 14px;
	top: 4px;
	text-transform: uppercase;
}

.calendar-icon-year {
	right: 6px;
	text-align: right;
	font-size: 14px;
	top: 4px;
}

.calendar-icon-date,
.calendar-icon-day {
	display: block;
	color: #555;
	position: absolute;
	text-align: center;
}

.calendar-icon-date {
	font-size: 32px;
	top: 35px;
	left: 0px;
	right: 0px;
	font-weight: bold;
	width: auto;
}

.calendar-icon-day {
	bottom: 6px;
	left: 0;
	right: 0;
}