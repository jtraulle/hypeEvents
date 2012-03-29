<?php

class hjEvent extends ElggObject {

	protected function initializeAttributes() {
		parent::initializeAttributes();
		$this->attributes['subtype'] = "hjevent";
	}

	public function addRSVP(ElggUser $user, $rsvp = 'attending') {
		$rsvp_types = array('attending', 'maybe_attending', 'not_attending');
		foreach ($rsvp_types as $rsvp_type) {
			remove_entity_relationship($user->guid, $rsvp_type, $this->guid);
		}
		return add_entity_relationship($user->guid, $rsvp, $this->guid);
	}

	public function getRSVPs($rsvp = null, $limit = 0) {
		return elgg_get_entities_from_relationship(array(
					'relationship' => $rsvp,
					'relationship_guid' => $this->guid,
					'inverse_relationship' => true,
					'limit' => $limit
				));
	}

}