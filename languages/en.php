<?php

$english = array(

	'hj:events' => 'Events',
	'hj:events:menu:owner_block' => 'Events',

	'item:object:hjevent' => 'Event',
	'items:object:hjevent' => 'Events',
	
	'hj:hjportfolio:hjevent' => 'Events',
	'hj:portfolio:hjevent' => 'Events',
	
	'hj:events:timeformat' => 'H:i',
	'hj:events:dateformat' => 'F j, Y',
	'hj:events:fulltimeformat' => 'F j, Y G:i T',

	'hj:label:form:new:hypeEvents:event' => 'Create an Event',
	'hj:label:form:edit:hypeEvents:event' => 'Edit Event',

	'item:object:hjevent' => 'Event',
	'items:object:hjevent' => 'Events',
	
	'river:create:object:hjevent' => '%s created a new Event | %s',
	'river:update:object:hjevent' => '%s updated an event | %s',

	'hj:label:hjevent:icon' => 'Event Icon',
	'hj:label:hjevent:title' => 'Title',
	'hj:label:hjevent:description' => 'Description',
	'hj:label:hjevent:calendar_start' => 'Starts',
	'hj:label:hjevent:calendar_end' => 'Ends',
	'hj:label:hjevent:host' => 'Host/Organizer',
	'hj:label:hjevent:venue' => 'Venue',
	'hj:label:hjevent:location' => 'Venue Address',
	'hj:label:hjevent:contact_details' => 'Contact Information',
	'hj:label:hjevent:www' => 'Web-Site',
	'hj:label:hjevent:costs' => 'Costs',
	'hj:label:hjevent:tags' => 'Tags',
	'hj:label:hjevent:access_id' => 'Who can see this event',

	'hj:events:starttime' => 'Starts: %s',
	'hj:events:endtime' => 'Ends: %s',

	'hj:events:rsvp' => 'RSVP',
	'hj:events:rsvp:attending' => 'Attending',
	'hj:events:rsvp:not_attending' => 'Not Attending',
	'hj:events:rsvp:maybe_attending' => 'Maybe Attending',

	'hj:events:rsvp:success' => 'Your RSVP was successful',
	'hj:events:rsvp:error' => 'Sorry, we could not RSVP you for this event',

	'hj:events:event:details' => 'Event Details',
	'hj:events:map' => 'Map',
	
	'hj:events:eventfilter' => 'Filter Events',
	'hj:events:filtered' => 'Search Results',
	'hj:events:addnew' => 'Add Event',
	'hj:events:upcoming' => 'Upcoming Events',
	'hj:events:past' => 'Past Events',
	'hj:events:allevents' => 'All Events',
	'hj:events:myevents' => 'My Events',
	'hj:events:friendevents' => 'Friends\' Events',

	'hj:events:calendar' => 'Find Events by Date',
	'hj:events:search' => 'Search Events',
	'hj:events:searching' => 'Searching ...',
	'hj:events:search:after' => 'Events After',
	'hj:events:search:before' => 'Events Before',
	'hj:events:search:location' => 'Events In',
	'hj:events:search:notfound' => 'No events were found',

	'hj:events:owned' => 'Events created by %s',
	'hj:events:user:attending' => 'Events that %s is attending',
	'hj:events:user:maybeattending' => 'Events that %s is maybe attending',

	'hj:events:guestlist' => 'Other people attending',
	'river:attending:object:hjevent' => '%s is attending an event | %s',

	'hj:events:ical' => 'Download iCal file',
	'hj:events:import' => 'Import iCal',
	'hj:events:import:instructions' => 'Select an .ics file or specify a url to an .ics file starting with http:// or webcal://. Please note that events will be imported as Private, please change the settings if you want them to appear on the site',
	'hj:events:import:file' => 'Upload a file',
	'hj:events:import:url' => 'Specify a url',
	'hj:events:import:status' => 'Import Results: <br />
		Imported: %s <br />
		Updated: %s <br />
		Failed: %s
	',
	'hj:events:export:site' => 'Export Site Calendar',
	'hj:events:export:attending' => 'Export Events I\'m attending',

	'hj:events:import:checkupdates' => 'Save this url',
	'hj:events:fetchfeed' => 'Get Updates',
	'hj:events:mysavedfeeds' => 'My Feeds',

	'hj:events:nourltoparse' => 'The calendar you are trying to parse is empty',
	

);


add_translation("en", $english);
?>