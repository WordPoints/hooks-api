<?php

/**
 * .
 *
 * @package wordpoints-hooks-api
 * @since   1.
 */

// this is basicaly a router.
class WordPoints_Entity_chang_trigger implements WordPoints_Entity_Change_ListenerI {

	public function created( WordPoints_Entity $entity ) {
		// can multiple entities be created at once? not using insert() but via query()
		// maybe? and how shoudl we handle that, all at once or one by one?

		/** @var WordPoints_Entity_Change_ListenerI[] $listeners */
		$listeners = wordpoints_apps()->sub_apps->get( 'entity_change_listeners' );
		foreach ( $listeners as $listener ) {
			$listener->created( $entity );
		}
	}


	public function updated( WordPoints_Entity $before, WordPoints_Entity $after ) {

		// what if multiple entities are updated at once? do we run each one separatately?
		// mayb ethat should be left up to the listenter.
		// and likewise wherther we handle the entity modifications attribute by attribute.
	}


	public function deleted( WordPoints_Entity $entity ) {

		// what if we need information about this entitie's relationships, etc., that
		// isn't included inthe entity object?
		// this is a problem for the hooks api, but not for the possession api.
		// but hwat about other potential apis?
		// of course, there is no guarantee that the relationships will be deleted
		// first, but there is no guarantee that they won't, either.
		// but i suppose that our excuse is that any API should be listening to
		// entity changes as well if it needs to know about such things.
		// but when something is deleted, then such apis will be triggered many times
		// right in a row. not ideal, but it isn't as if things are constantly being
		// deleted. but if we had a retroactive api, we could just temporarily
		// suspend running the listeners and then trigger things after all of the
		// queries were done. for a single entity it wouldn't be worth it, but maybe
		// for a bunch of entities deleted at once. but that isn't a cross-api think,
		// just mainly for the hooks/possession apis. though really is suppose that
		// it sis a part of the entity api, in terms of the queries, but we have to
		// have some sor of logs in order to be able to use it idempotently.

		// just running before delte wouldn't work either, because the relatinships
		// would have alrady been severed then if they are severed before deletion.
	}

}

// this is basically a reactor
interface WordPoints_Entity_Change_ListenerI {
	public function created( WordPoints_Entity $entity );
	public function updated( WordPoints_Entity $before, WordPoints_Entity $after );
	public function deleted( WordPoints_Entity $entity );
}

class WordPoints_Entity_Change_Listener_Hooks implements WordPoints_Entity_Change_ListenerI {

	public function created( WordPoints_Entity $entity ) {

		// maybe we would have multuiple events with requirements for a single entity?
		// like comment author and post commentn author targets for the same hook.
		// but I guess taht is just one event.
		// what about user register vs user create on MS?
		if ( $this->matches_requirements( $entity ) ) {
			$this->fire_event( 'add', $entity );
		}
	}

	public function updated( WordPoints_Entity $before, WordPoints_Entity $after ) {

		if ( $this->matches_requirements( $after ) ) {
			if ( ! $this->matches_requirements( $before ) ) {
				$this->fire_event( 'add', $after );
			}
		} else {
			if ( $this->matches_requirements( $before ) ) {
				$this->fire_event( 'remove', $after );
			}
		}
	}

	public function deleted( WordPoints_Entity $entity ) {

		if ( $this->matches_requirements( $entity ) ) {
			$this->fire_event( 'remove', $entity );
		}
	}

	// ideally we would actually check the conditions for each reaction here.
	// however, that will likely just have to be a bug/edge-case until the new
	// api is introduced.
	// ahve we ever considered what happens when an entity has been modified to not
	// match the conditions anymore before it is deleted? but that doesn't pose a
	// problem in teh hooks api usually, because before we were just reversing based
	// on the hook logs. so we didnt' check if the entity matched teh conditions at
	// all.
	// so the whole issue with relationships being deleted before the hooks api was
	// called into play when the entity itself is deleted is moot, because the
	// reltionships, etc., aren't even taken into account when toggle-off is called,
	// in the points reactor. other reators might, i guess.
	protected function matches_requirements( WordPoints_Entity $entity ) {

		/** @var WordPoints_Class_Registry $defaults */
		$defaults = wordpoints_apps()->sub_apps->get( 'entity_possession_defaults' );

		$defaults = $defaults->get( $entity->get_slug() );

		if ( ! $defaults ) {
			return false;
		}

		// use conditions here?
		foreach ( $defaults as $child => $value ) {
			if ( $entity->get_child( $child ) !== $value ) {
				return false;
			}
		}

		return true;
	}

	protected function fire_event( $type, WordPoints_Entity $entity ) {

		$args = new WordPoints_Hook_Event_Args( array() );
		$args->add_entity( $entity );

		wordpoints_hooks()->fire(
			$type . '_entity_' . $entity->get_slug(),
			$args,
			'toggle_on'
		);
	}
}

class WordPoints_Entity_Change_Listener_Points implements WordPoints_Entity_Change_ListenerI {

	public function created( WordPoints_Entity $entity ) {

		$this->process_entityish(
			$entity
			, $this->get_settings_for_entity( $entity )
		);
	}

	protected function process_entityish( WordPoints_EntityishI $entity, $settings ) {

		// possibly make this more like extension handling
		if ( ! $this->meets_conditions( $entity, $settings['conditions'] ) ) {
			return;
		}

		// possibly make this more like reactor handling.
		$this->award_points( $settings );

		// only proces the attributes taht have changed.
		// acutally, in this case, the entity was just created.
		if ( $entity instanceof WordPoints_Entity_ParentI ) {

			// this check runs on attributes only.
			foreach ( $settings['children'] as $child_slug => $child_settings ) {
				$this->process_entityish( $entity->get_child( $child_slug ), $child_settings );
			}

			// we also need to check for any children, like relationships, that have
			// the settings stored separately.
			// for this we need a list of relationships.
			// if we do two-way relationships, we may need to have infinite-loop
			// halding here, depending.
			foreach ( $this->get_related_entities( $entity ) as $child_entity ) {
				// If a comment was just created, for example, and we have conditions
				// on the post entity, that affect the comment author, this allows
				// us to handle those by pulling up the settings for the post entity
				// and looping through them too.
				// however, we need to limit this to only awarding the comment author
				// don't we? But maybe not, because what about when the post awards
				// are conditioned on the post's comments? but then the data would
				// just be on the comment but with the post author as the target.
				// But we still need to have some way of limiting this to only
				// targets relating to the comment.
				// I suppose taht maybe it is as simple as continuing to pass the
				// entity hierarchy with the comment at the top around, which when
				// we attempt to get the target with get_from_hierarchy() will only
				// return a value if the target is the comment author. That would be
				// even better/easier if the settings were indexed by target somehow
				// as we've proposed...
				$this->process_entityish(
					$child_entity
					, $this->get_entity_settings( $child_entity )
				);
			}
		}
	}

	// do we also listen for relationship changes, or is that a separate api?
	// I guess we do listen for some, but only because they happen to be defined on
	// entity attributes.

	// and are these only whole entities, or can they be just atts? I guess taht
	// doesn't really make sense.
	// I think relationships can be created and deleted, but not really updated.
	// so maybe what we need is a separate api?
	public function updated( WordPoints_Entity $before, WordPoints_Entity $after ) {

		$settings = $this->get_settings_for_entity( $before );

		/** @var WordPoints_Class_Registry_ChildrenI $children */
		$children = wordpoints_entities()->children;
		foreach ( $children->get_children_slugs( $before->get_slug() ) as $child_slug ) {
			
			if ( ! isset( $settings['children'][ $child_slug ] ) ) {
				continue;
			}
			
			// which ones are attributes and which are not?
			// I guess we just check the atts.
			// but it isn't as simple as that, because the atts don't necessarilly
			// match the names of the children.
			// so we could just pull up the child and check if it is an instanceof
			// the correct class. that seems expensive, but we need them anyway (see
			// below), and maybe if we have a list of relatinoships we could check
			// that too.
			// but then we'll not be checking even the relationships taht are defined
			// on the atts. so we have to decide whether those should be handled by
			// a separate api or not.
			if ( $before->get_the_attr_value( $child_slug ) === $after->get_the_attr_value( $child_slug ) ) {
				continue;
			}

			$this->process_modified_entityish(
				$before->get_child( $child_slug )
				, $after->get_child( $child_slug )
				, $settings['children'][ $child_slug ]
			);
		}
	}

	protected function process_modified_entityish( WordPoints_EntityishI $before, WordPoints_EntityishI $after, $settings ) {

		if ( ! $this->meets_conditions( $before, $settings['conditions'] ) ) {
			if ( $this->meets_conditions( $after, $settings['conditions'] ) ) {
				$this->award_points( $settings );

				// also need to process other eneities that are affected by this
				// change, which may have conditions on this, i.e., that could be
				// parents of this enity.
				// that's actually only true for relationship/cascading handling.
				// otherwise we are actually fine here, since parent/child entities
				// will have points awarded by the targets.
				// but say taht this is a post, and on the user entity there is a
				// condition that the comment authors are to be awarded based on a
				// certain attribute of the post author. if the post author has
				// changed, that needs to be processed. however, that is a
				// relationship change, so maybe it will come thorugh a different
				// api. if not, we need to check if this is a relationship here and
				// then we do indeed need to run through the children/parents.
			}
		} else {
			if ( ! $this->meets_conditions( $after, $settings['conditions'] ) ) {
				$this->remove_points( $settings );
			}
		}
	}

	public function deleted( WordPoints_Entity $entity ) {
		// basically the opposite of created().
		$this->process_entityish_reverse( $entity );
	}

	protected function meets_conditions( WordPoints_EntityishI $entityish, $conditions ) {
		// use conditions api

		return false;
	}

	private function award_points( $settings ) {

		$hierarchy = new WordPoints_Entity_Hierarchy( $this->eitnty );
		// todo we'll need to introudce entity array targets, possibly.
		// how will we know how to reverse the target reltinoship chains in the UI
		// before saving? i guess we'll need to either have a dedicated index fo taht
		// or else juust look it up by looping through the relationships (though we'd
		// have to remove the entity array {} part from any one-to-many relationships
		// and maybe we'd have to add it to others?)
		$targets = $hierarchy->get_from_hierarchy( $settings['target'] );

		// maybe just one target, maybe several, depending.
	}

	private function get_related_entities( WordPoints_Entity $entity ) {

		// what we need is parental conditions. Conditions that go up the chain,
		// and look back at parent entities. This way we can handle the info about
		// comments on a particuar post when the post entity is modified, since the
		// conditions can be on the post
		// we'd need inverse relationships, get all comements for the post, and then
		// run the "paretn" conditions on each of them.
		// or just not allow conditions/reactions on relationships
		// but just registering relationships for both entities is not enough,
		// because we can't tell the slug of the entity form the slug of the entity
		// child, unless we just loop thorugh them and attempt to get that entity
		// from each relationship.

		return array();
	}
}

// EOF
