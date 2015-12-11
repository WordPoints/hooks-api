<?php

/**
 * Class for the hooks app.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

/**
 * Hooks app.
 *
 * The hooks API consists primarily of actions, events, reactors, args, and
 * other extensions. Events are "fired" at various reactors when actions occur.
 * The args that the event relates to is passed to any extensions, along with
 * the list of predefined reactions. The extensions can then analyse the args and
 * the reaction specifications to determine whether the reactor should "hit" or
 * "miss" the target entity.
 *
 * @since 1.0.0
 *
 * @property-read WordPoints_Hook_Router               $router     The hook action router.
 * @property-read WordPoints_Hook_Actions              $actions    The actions registry.
 * @property-read WordPoints_Hook_Events               $events     The events registry.
 * @property-read WordPoints_Class_Registry_Persistent $firers     The firers registry.
 * @property-read WordPoints_Class_Registry_Persistent $reactors   The reactors registry.
 * @property-read WordPoints_Class_Registry_Persistent $extensions The extensions registry.
 * @property-read WordPoints_Class_Registry_Children   $conditions The conditions registry.
 */
class WordPoints_Hooks extends WordPoints_App {

	/**
	 * Whether network-wide mode is turned on.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $network_mode = null;

	/**
	 * Register the sub apps when the app is constructed.
	 *
	 * @since 1.0.0
	 */
	protected function init() {

		$sub_apps = $this->sub_apps;
		$sub_apps->register( 'router', 'WordPoints_Hook_Router' );
		$sub_apps->register( 'actions', 'WordPoints_Hook_Actions' );
		$sub_apps->register( 'events', 'WordPoints_Hook_Events' );
		$sub_apps->register( 'firers', 'WordPoints_Class_Registry_Persistent' );
		$sub_apps->register( 'reactors', 'WordPoints_Class_Registry_Persistent' );
		$sub_apps->register( 'extensions', 'WordPoints_Class_Registry_Persistent' );
		$sub_apps->register( 'conditions', 'WordPoints_Class_Registry_Children' );

		parent::init();
	}

	/**
	 * Gets whether network-wide mode is on.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether network-wide mode is on.
	 */
	public function get_network_mode() {

		if ( ! isset( $this->network_mode ) ) {
			$this->network_mode = is_network_admin();
		}

		return $this->network_mode;
	}

	/**
	 * Sets whether network-wide mode is on.
	 *
	 * This function should not be used, especially to turn network mode off when
	 * WordPoints has turned it on. I won't even say, "Unless you really know what
	 * you're doing," because trust me, if you're trying to do that, you don't. Here
	 * be dragons!
	 *
	 * For those of you that are specifically looking for something to break, here is
	 * how to do it.
	 *
	 * What network mode does is tell the hooks API that it is in the context of the
	 * network itself, and not of any particular site. This feature is what allows us
	 * to have network-wide reactions in addition to the standard, per-site
	 * reactions. When network mode is on, it means that we don't want to do anything
	 * with standard reactions at all, because even though there may be a "current
	 * site" that we could pull them from, we're actually supposed to be pretending
	 * that we aren't on a particular site on the network, but in network-wide
	 * context. So when network mode is turned on, you won't be able to do anything
	 * with standard reactions, only network-wide ones.
	 *
	 * This is why turning it off is so dangerous—it would allow you to accidentally
	 * do things with standard reactions when you probably aren't expected to. The
	 * user is in the context of the network, for example in the network admin, so
	 * they're only expecting network-wide reactions to be affected. If you did
	 * something with standard reactions, you'd end up affecting a particular site,
	 * when the user doesn't think he is.
	 *
	 * @since 1.0.0
	 *
	 * @internal
	 *
	 * @param bool $on Whether network-wide mode should be on (or off).
	 */
	public function _set_network_mode( $on = true ) {
		$this->network_mode = (bool) $on;
	}
}

// EOF
