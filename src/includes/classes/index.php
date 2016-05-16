<?php

/**
 * Manually loads all class files.
 *
 * This file is only loaded when autoloading isn't available.
 *
 * @package wordpoints-hooks-api
 * @since 1.0.0
 */

// auto-generated {
require_once( dirname( __FILE__ ) . 'app.php' );
require_once( dirname( __FILE__ ) . 'app/registry.php' );
require_once( dirname( __FILE__ ) . 'class/autoloader.php' );
require_once( dirname( __FILE__ ) . 'class/registry.php' );
require_once( dirname( __FILE__ ) . 'class/registry/children.php' );
require_once( dirname( __FILE__ ) . 'class/registry/childreni.php' );
require_once( dirname( __FILE__ ) . 'class/registry/persistent.php' );
require_once( dirname( __FILE__ ) . 'class/registryi.php' );
require_once( dirname( __FILE__ ) . 'data/type.php' );
require_once( dirname( __FILE__ ) . 'data/type/integer.php' );
require_once( dirname( __FILE__ ) . 'data/type/text.php' );
require_once( dirname( __FILE__ ) . 'data/typei.php' );
require_once( dirname( __FILE__ ) . 'db/query.php' );
require_once( dirname( __FILE__ ) . 'entity.php' );
require_once( dirname( __FILE__ ) . 'entity/array.php' );
require_once( dirname( __FILE__ ) . 'entity/attr.php' );
require_once( dirname( __FILE__ ) . 'entity/attr/field.php' );
require_once( dirname( __FILE__ ) . 'entity/childi.php' );
require_once( dirname( __FILE__ ) . 'entity/comment.php' );
require_once( dirname( __FILE__ ) . 'entity/comment/author.php' );
require_once( dirname( __FILE__ ) . 'entity/comment/post.php' );
require_once( dirname( __FILE__ ) . 'entity/context.php' );
require_once( dirname( __FILE__ ) . 'entity/context/network.php' );
require_once( dirname( __FILE__ ) . 'entity/context/site.php' );
require_once( dirname( __FILE__ ) . 'entity/enumerablei.php' );
require_once( dirname( __FILE__ ) . 'entity/hierarchy.php' );
require_once( dirname( __FILE__ ) . 'entity/hierarchyi.php' );
require_once( dirname( __FILE__ ) . 'entity/parenti.php' );
require_once( dirname( __FILE__ ) . 'entity/post.php' );
require_once( dirname( __FILE__ ) . 'entity/post/author.php' );
require_once( dirname( __FILE__ ) . 'entity/post/content.php' );
require_once( dirname( __FILE__ ) . 'entity/post/terms.php' );
require_once( dirname( __FILE__ ) . 'entity/post/type.php' );
require_once( dirname( __FILE__ ) . 'entity/post/type/name.php' );
require_once( dirname( __FILE__ ) . 'entity/post/type/relationship.php' );
require_once( dirname( __FILE__ ) . 'entity/relationship.php' );
require_once( dirname( __FILE__ ) . 'entity/relationship/dynamic.php' );
require_once( dirname( __FILE__ ) . 'entity/relationship/stored/field.php' );
require_once( dirname( __FILE__ ) . 'entity/restricted/visibilityi.php' );
require_once( dirname( __FILE__ ) . 'entity/site.php' );
require_once( dirname( __FILE__ ) . 'entity/stored/array.php' );
require_once( dirname( __FILE__ ) . 'entity/stored/db/table.php' );
require_once( dirname( __FILE__ ) . 'entity/term.php' );
require_once( dirname( __FILE__ ) . 'entity/term/id.php' );
require_once( dirname( __FILE__ ) . 'entity/user.php' );
require_once( dirname( __FILE__ ) . 'entity/user/role.php' );
require_once( dirname( __FILE__ ) . 'entity/user/role/name.php' );
require_once( dirname( __FILE__ ) . 'entity/user/roles.php' );
require_once( dirname( __FILE__ ) . 'entityish.php' );
require_once( dirname( __FILE__ ) . 'entityish/storedi.php' );
require_once( dirname( __FILE__ ) . 'entityishi.php' );
require_once( dirname( __FILE__ ) . 'hierarchy.php' );
require_once( dirname( __FILE__ ) . 'hook/action.php' );
require_once( dirname( __FILE__ ) . 'hook/action/comment/new.php' );
require_once( dirname( __FILE__ ) . 'hook/action/post/publish.php' );
require_once( dirname( __FILE__ ) . 'hook/actioni.php' );
require_once( dirname( __FILE__ ) . 'hook/actions.php' );
require_once( dirname( __FILE__ ) . 'hook/arg.php' );
require_once( dirname( __FILE__ ) . 'hook/arg/current/post.php' );
require_once( dirname( __FILE__ ) . 'hook/arg/current/site.php' );
require_once( dirname( __FILE__ ) . 'hook/arg/current/user.php' );
require_once( dirname( __FILE__ ) . 'hook/arg/dynamic.php' );
require_once( dirname( __FILE__ ) . 'hook/condition.php' );
require_once( dirname( __FILE__ ) . 'hook/condition/entity/array/contains.php' );
require_once( dirname( __FILE__ ) . 'hook/condition/equals.php' );
require_once( dirname( __FILE__ ) . 'hook/condition/string/contains.php' );
require_once( dirname( __FILE__ ) . 'hook/conditioni.php' );
require_once( dirname( __FILE__ ) . 'hook/event.php' );
require_once( dirname( __FILE__ ) . 'hook/event/args.php' );
require_once( dirname( __FILE__ ) . 'hook/event/comment/leave.php' );
require_once( dirname( __FILE__ ) . 'hook/event/dynamic.php' );
require_once( dirname( __FILE__ ) . 'hook/event/media/upload.php' );
require_once( dirname( __FILE__ ) . 'hook/event/post/publish.php' );
require_once( dirname( __FILE__ ) . 'hook/event/user/register.php' );
require_once( dirname( __FILE__ ) . 'hook/event/user/visit.php' );
require_once( dirname( __FILE__ ) . 'hook/eventi.php' );
require_once( dirname( __FILE__ ) . 'hook/events.php' );
require_once( dirname( __FILE__ ) . 'hook/extension.php' );
require_once( dirname( __FILE__ ) . 'hook/extension/blocker.php' );
require_once( dirname( __FILE__ ) . 'hook/extension/conditions.php' );
require_once( dirname( __FILE__ ) . 'hook/extension/hit/listeneri.php' );
require_once( dirname( __FILE__ ) . 'hook/extension/miss/listeneri.php' );
require_once( dirname( __FILE__ ) . 'hook/extension/periods.php' );
require_once( dirname( __FILE__ ) . 'hook/extension/repeat/blocker.php' );
require_once( dirname( __FILE__ ) . 'hook/fire.php' );
require_once( dirname( __FILE__ ) . 'hook/hit/logger.php' );
require_once( dirname( __FILE__ ) . 'hook/hit/query.php' );
require_once( dirname( __FILE__ ) . 'hook/reaction.php' );
require_once( dirname( __FILE__ ) . 'hook/reaction/options.php' );
require_once( dirname( __FILE__ ) . 'hook/reaction/store.php' );
require_once( dirname( __FILE__ ) . 'hook/reaction/store/options.php' );
require_once( dirname( __FILE__ ) . 'hook/reaction/store/options/network.php' );
require_once( dirname( __FILE__ ) . 'hook/reaction/storei.php' );
require_once( dirname( __FILE__ ) . 'hook/reaction/validator.php' );
require_once( dirname( __FILE__ ) . 'hook/reactioni.php' );
require_once( dirname( __FILE__ ) . 'hook/reactor.php' );
require_once( dirname( __FILE__ ) . 'hook/reactor/points.php' );
require_once( dirname( __FILE__ ) . 'hook/retroactive/conditions.php' );
require_once( dirname( __FILE__ ) . 'hook/retroactive/query.php' );
require_once( dirname( __FILE__ ) . 'hook/retroactive/query/modifieri.php' );
require_once( dirname( __FILE__ ) . 'hook/retroactive/queryable.php' );
require_once( dirname( __FILE__ ) . 'hook/retroactive/queryi.php' );
require_once( dirname( __FILE__ ) . 'hook/router.php' );
require_once( dirname( __FILE__ ) . 'hook/settings.php' );
require_once( dirname( __FILE__ ) . 'hook/settingsi.php' );
require_once( dirname( __FILE__ ) . 'hook/validator/exception.php' );
require_once( dirname( __FILE__ ) . 'hooks.php' );
require_once( dirname( __FILE__ ) . 'query/builder/db/mysql.php' );
require_once( dirname( __FILE__ ) . 'spec.php' );
require_once( dirname( __FILE__ ) . 'specedi.php' );
// }

// EOF
