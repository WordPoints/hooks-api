# APIs

This document describes all of the components of the proposed Hooks API.

The APIs must do the following things:

- [ ] Store the settings in the database.
- [ ] Get a list of available hook types.
- [ ] Get a list of hook instances in a group.
- [ ] Generate a general description of an instance.
- [ ] Hook into user actions.
- [ ] Perform action when hook fires.
- [ ] Checking that instance settings match current user action.
- [ ] Reversing a firing action when the user action is reversed.
- [ ] Generating a description of the user action.
- [ ] Checking the proper visibility for a current hook action based on current user caps.
- [ ] Validating the settings of an instance.

## Storage

### Purpose

CRUD hook instances' settings in the database.

### Constraints

- [ ] It must not be tied to one way of storing the hooks. It should be possible to store them as options or as posts, 
for example.
- [ ] It must be decoupled from the class name of a hook handler.
- [ ] It must be possible to create, update, and delete instances individually.
- [ ] It must be possible to get a list of instances in a group.
- [ ] It must be possible to get a list of instances in a group of a certain hook type.
- [ ] It must support network-wide instances.

