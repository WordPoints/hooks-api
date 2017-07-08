# WordPoints Hooks API [![Build Status](https://travis-ci.org/WordPoints/hooks-api.svg?branch=master)](https://travis-ci.org/WordPoints/hooks-api) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/WordPoints/hooks-api/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/WordPoints/hooks-api/?branch=master) [![Coverage Status](https://coveralls.io/repos/WordPoints/hooks-api/badge.svg?branch=master&service=github)](https://coveralls.io/github/WordPoints/hooks-api?branch=master)

API for scripting automatic responses to user actions, being developed as a module.

**Notice**: This extension was partly merged into WordPoints core in [#321](https://github.com/WordPoints/wordpoints/issues/321), and now contains mostly remaining experimental code relating more to achievements.

## Purpose

- To provide a basic API for hooking into user actions, that can be utilized accross WordPoints's components.
- To provide feature parity accross the components in terms of what hooks are offered.

## Background

WordPoints has included a Points Hooks API since version 1.0.0, which was based upon the Widgets API. This API was comprised of two main components:

1. Points Hook handlers, in the form of `WordPoints_Points_Hook` child classes.
2. A registery of the available Points Hook handlers, in the form of the static `WordPoints_Points_Hooks` class.

The Points Hooks registry covered the following things:

- Keeping a list of the available types of Points Hooks.
- Interacting with the list of the Points Hook Instances in use on the site (but not their data, stored separately), indexed by points type.
- Displaying the template for the Points Hook settings forms.

The Points Hook handlers also encompassed several APIs:

- Generating a description of an instance of the Points Hook.
- Hooking into user action(s) to award points.
- Checking whether a particular user action matched the settings of a particular Points Hook Instance.
- Reversing any transactions when the user action was reversed.
- Generating a message for the points logs.
- Displaying the form fields for Instances' settings.
- Validating the settings for Instances of that hook.
- Determining to whom the log message could be displayed.

The base Points Hook handler also provided an API for interacting with the Instances of that type of Points Hook.

## Problems

While some amount of bootstrap could be provided via inheritance, and much of the basic API was provided by the base Points Hook class, extensibility and reuseablitily of bits of the API was cramped and complex. Among the problems faced were the following:

1. Because the handlers were required to hook into the user actions, they had to be instantiated on every page load. This made it impossible to lazily load the API using autoloading. It also made it impossible to deregister a handler once it was registered, because it would still be hooked to those actions. [wordpoints#50](https://github.com/WordPoints/wordpoints/issues/50) [wordpoints#258](https://github.com/WordPoints/wordpoints/issues/258)
2. Within the registry the handlers were identified by their class names. This made it difficult to switch out the handler for one type of hook to a custom implementation. It also meant that the name of the class was tied to how the instances were stored in the database. This caused compaitibility issues. [wordpoints#84](https://github.com/WordPoints/wordpoints/issues/84)
3. The type of points that an Instance should award was determined by an API provided by the registry, while the rest of an Instance's settings were handled by the handler. This meant that complex logic was necesary just to determine what type of points to award. It also tied display of the Instances to the registry, and makes it necessary to add an Instance to the list in addition to just saving its settings or else it will not function. [wordpoints#86](https://github.com/WordPoints/wordpoints/issues/86) [wordpoints#304](https://github.com/WordPoints/wordpoints/issues/304)
4. The handlers each had to retrieve all of the Instances inside of their hooked method. Because of this, when network-wide hook support was introduced, the network-wide hooks had to be included along with all of the other hooks. The handlers had to have the ID of each Instance so that they could get the points type for that Instance from the registry. But the network-wide Instances' IDs could conflict with the regular Instances' IDs, so they were prefixed with `network_`. However, this makes them non-numeric strings, when they were previously numeric, which could possibly lead to issues. It also produces an inconsistency in the API when only network Instances are retreived, because then the IDs aren't prefixed. [wordpoints#120](https://github.com/WordPoints/wordpoints/issues/120) [wordpoints#251](https://github.com/WordPoints/wordpoints/issues/251)
5. Although the settings for an Instance are validated, there is no error feedback. This makes it impossible to build a UI that provides useful feedback to the user. [wordpoints#250](https://github.com/WordPoints/wordpoints/issues/250)
6. There is inherent confusion in the API between the Points Hook handlers and the Points Hook Instances. Both are loosely called Points Hooks.
7. The API wasn't reuseable beyond the Points component.[wordpoints#321](https://github.com/WordPoints/wordpoints/issues/321)

## Solutions

This project aims to solve these and other problems by creating an entirely new set of APIs. All of the existing APIs will probably need to be represented, and it's possible that we'll split some of them into new ones as well. However, how the APIs relate to each other will need to change to solve some of the above problems.

1. The Registration and Hooking APIs should be more tightly coupled with the registry, rather than being handled separately.
2. The Registration API should use arbitrary slugs not tied to the currently registered handler.
3. The Settings API should be completely decoupled from the Registration API.
4. The Storage API for the settings should be decoupled from the Awards API.
5. The Settings API should provide more responsive validation.
6. There should be a seperate API for Instances and the Hook handlers.
7. The API needs to be result agnostic. It should handle the firing of an action but allow what occurs when that action is fired to be determined by each component.
 
## Scope

The focus of this project is purely on the APIs, and should be UI agnostic. While it should provide all of the APIs and features necesary to create a UI, the goal isn't to reimagine the UI yet.
