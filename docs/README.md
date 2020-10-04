#  CakePHP StateMachine Plugin Documentation

## Terminology

State machines are a model of computation used to automate processes.
The machine can be in one of a finite number of states and it can be only in one state at a time for a specific identifier.

### States

States allow describing in which state a state machine is. They are usually described as adjectives ("What state am I in?").

```xml
<states>
    <state name="new" display="name display value"/>
    <state name="payment pending"/>
    <state name="paid"/>
</states>
```

### Transitions
States can be connected one to another through transitions, similar to a finite graph.
They are defined via "source" and "target" state.
Such a transition is bound to an event, which tells when the item can leave the current state.

```xml
<transitions>
    <transition>
        <source>new</source>
        <target>payment pending</target>
        <event>authorize</event>
    </transition>

    <transition>
        <source>payment pending</source>
        <target>paid</target>
        <event>pay</event>
    </transition>

</transitions>
```

### Events
Events are what can cause transitions to happen.
They are usually described as actions/verbs ("What do I do?").

```xml
<events>
    <event name="authorize" onEnter="true"/>
    <event name="pay" manual="true"/>
</events>
```

#### Manually executable events
In order to be able to trigger an event manually you need to mark it as manually executable.
This means that when an item is in the same state as the source state of a transition that has a manually executable event attached to it,
in we can implement a button that corresponds to that event in the GUI.
By clicking the button, we are triggering the event associated with it.

#### OnEnter Events
As seen above, they can have `"onEnter"=true` to auto-trigger this event when the source state is reached.
If nothing fails the state machine then transitions directly to the target state.

By using the OnEnter events you can model a chain of commands that you want to get executed because
the state machine always looks if there is another thing to do after any transition that gets executed.

You usually want to make your state-machine as fully automated as possible.
As such purely manual events and events without external trigger (for async handling) are usually avoided.

Note that for easier re-trigger in case of failure, it is advised to add `"manual"=true` on top.
This will display a fallback button to manually retry the event if it failed.
Without this you need to have a code-trigger for the event (through own button or facade method call).

#### Timeout events
Events can be triggered after a defined period of time has passed, through a timeout.
The timeout is defined in PHP string timespan.
```xml
<event name="sendFirstReminder" manual="true" timeout="10 days"/>
```

Every time the event is fired (automatically, after timeout), the state machine makes sure the associated command is executed.
If an exception occurs in the command coding, the item stays in the source state.

Note that you can add `"manual"=true` here to skip the timeout manually and directly trigger the event.

### Conditions
A transition can be conditioned: the state machine can move from one state to another
if a certain condition associated with that transition is being satisfied.
```xml
<transition condition="Test/Condition">
```

The map of condition names and classes in code is done in the StateMachineHandler's `getConditions()`.



### Commands
A transition from one state to another has an event associated with it.
The event can have a command hooked up, which is a piece of logic that gets executed when the event is fired.
```xml
<event name="create pdf" command="Test/Command" />
```

The map of command names and classes in code is done in the StateMachineHandler's 's `getCommands()`.


## Creating a new State Machine
You can generate a fresh XML file for a specific process using
```
bin/cake state_machine init MyProcessName
```
It will automatically append `01` as version suffix: `MyProcessName01.xml`

Now you can add your states, transitions and events to it.
Using the XSD provided, the IDE should be able to give you full autocomplete/typehinting here.


## Configuration

Create a handler for your specific state machine which implements `StateMachine\Dependency\StateMachineHandlerInterface` and then define the active processes:
```php
    public function getActiveProcesses(): array
    {
        return [
            'Demo01',
        ];
    }
```

Hook in your state machine handler(s) in Configure:
```php
    'StateMachine' => [
        'handlers' => [
            App\StateMachine\DemoStateMachineHandler::class,
        ],
    ],
```

Use Configure to set other defaults.

You can set a custom graph renderer, for example:

```php
return [
    ...
    'StateMachine' => [
        'graphAdapter' => \App\Graph\Adapter\PhpDocumentorGraphAdapter::class,
    ],
];
```


### Linking your entity to the State Machine
You can either store the id of the state directly in the entity, adding fields here into each entity.
Most of the time, however, you wouldn't want to add those on all entities.
Therefore, you can leverage the state_machine_items pivot table and alias it:
```php
$this->hasOne('ItemStates', [
    'className' => 'StateMachine.StateMachineItems',
    'foreignKey' => 'identifier',
    'conditions' => ['ItemStates.state_machine' => 'MyModelName'],
]);
```
This will add a `->item_state` property into your `$entity` if you contain that relation in your finds.
You can query/read the state on this added property: `$entity->item_state->state`.

### Implement commands and conditions
The commands and conditions will most likely be still red, as they are not implemented yet.
Create your custom commands and conditions now.

```php
namespace App\StateMachine\Command;

use StateMachine\Dependency\StateMachineCommandInterface;

class MyCommand implements StateMachineCommandInterface
{
    ...
}
```
and
```php
namespace App\StateMachine\Condition;

use StateMachine\Dependency\StateMachineCommandInterface;

class MyCondition implements StateMachineConditionInterface
{
    ...
}
```

Tip: You can use bake to quickly generate them for you:
- `bin/cake bake state_machine_command MyCommand`
- `bin/cake bake state_machine_condition MyCondition`

Let's hook them up to the PHP counterpart then:
```php
    public function getCommands(): array
    {
        return [
            'Test/Command' => MyTestCommand::class,
        ];
    }

    public function getConditions(): array
    {
        return [
             'Test/Condition' => MyTestCondition::class,
        ];
    }
```

### Setting up the cronjobs
In order for the conditions and timeouts to be checked, we need to activate the cronjobs for the commands:
```
bin/cake state_machine check_conditions {StateMachineName}
bin/cake state_machine check_timeouts {StateMachineName}
```
should be added to e.g. `crontab` in e.g. 1 min intervals to be executed.

```
bin/cake state_machine clear_locks
```
can be added with a bit bigger interval.

## Using it
Once your XML is ready to be validated, check it out as live preview in the backend:
- Go to `/admin/state-machine` and select the process
- Adjust your state machine further and just press F5 to reload the preview until it looks as expected

### Invoking an Event
Events can be triggered via:

- onEnter
- facade calls
- timeout automatically (bin/cake state_machine check_timeout)
- cronjob (bin/cake state_machine check_conditions)

Note: The process needs to be in a state, where it is actually waiting for the event you are triggering.
Otherwise, the event would not be processed.

### Facade methods
This is typically used if an external or async event is raised.

```php
$stateMachineFacade->triggerForNewStateMachineItem($processDto, $identifier);
```
will initialize a state machine for an item (unique $identifier for a process);

```php
$stateMachineFacade->triggerEvent($eventName, $itemDto)
```
will trigger a specific event for an item.

```php
$stateMachineFacade->triggerEventForItems($eventName, $itemDtos)
```
will trigger a specific event for a collection of items.


### First demo run

From your code call the `StateMachineFacade::triggerForNewStateMachineItem()` method with the identifier of your
record (usually the integer primary key of that DB row).
```php
$stateMachineFacade = new StateMachineFacade();

$processDto = new ProcessDto();
$processDto->setStateMachineName('Test');
$processDto->setProcessName('Test01'); // Optional

$identifier = $myEntity->id;

$stateMachineFacade->triggerForNewStateMachineItem($processDto, $identifier);
```

The process is optional, if you do not supply it, the last one of the list of active
processes will be used.

It should now create the DB records for it and run for this state machine process.

Now start to trigger events where you need to manually transition:
```php
$stateMachineFacade = new StateMachineFacade();

$event = 'do something';

$itemDto = new ItemDto();
$itemDto->setIdentifier($myEntity->id);

$itemDto->setStateMachineName($processDto->getStateMachineNameOrFail());
$itemDto->setProcessName($processDto->getProcessNameOrFail());
$itemDto->setStateName('source state'); // or setIdItemState($int)

$stateMachineFacade->triggerEvent($event, $itemDto);
```
You can use either the state name or the id of that row.

If this was successful, your state should now be the "target_state" of that event transition.

You can also display buttons in your entities' view to advance the process.
For this, get the events that can be manually executed:
```php
$events = $stateMachineFacade->getManualEventsForStateMachineItem($itemDto);
```

Then display your buttons for them:
```php
// $event is a string here
$url = ['prefix' => 'admin', 'plugin' => 'StateMachine',
    'controller' => 'Trigger', 'action' => 'event',
    '?' => [
        'state-machine' => $entity->item_state->state_machine,
        'process' => $entity->item_state->process,
        'state' => $entity->item_state->state,
        'identifier' => $entity->id,
        'event' => $event,
    ],
];
echo $this->Form->postLink($event, $url) . ' ';
```

#### Display state history and transition logs:
In your entities' controller action you can extract the current history and logs:
```php
$this->loadModel('StateMachine.StateMachineTransitionLogs');
$logs = $this->StateMachineTransitionLogs->getLogs($entity->item_state->id);

$this->loadModel('StateMachine.StateMachineItemStateHistory');
$history = $this->StateMachineItemStateHistory->getHistory($entity->item_state);
```

### Build your own custom dashboard
For each entity you will most likely want to have your own dashboard for the state machine, including
the buttons and the specific graph of the current state using `highlight-state` query string:

Here the link/URL to generate the image in that dashboard:
```php
$url = [
    'prefix' => 'Admin',
    'plugin' => 'StateMachine',
    'controller' => 'Graph',
    'action' => 'draw',
    '?' => [
        'state-machine' => $entity->item_state->state_machine,
        'process' => $entity->item_state->process,
        'highlight-state' => $entity->item_state->state,
    ],
];
$image = $this->Html->image($url);
echo $this->Html->link($image, $url, ['escapeTitle' => false, 'target' => '_blank']);
```

### Admin backend

If you want to use the admin CRUD backend, make sure to load the required helpers in your AppView:
```php
$this->loadHelper('Tools.Format');
```


## Versioning the State Machines
The ideal case would be that after designing your state machines and you start using them in production environment,
they stay the same and don’t need any further adjustments.

However, we all know that a software product is subject of change in time.
The state machines that model the order processing touch many critical parts of the system so it’s very likely
to need updates in the future.

When a state machine is changed but there are already orders which use this process, this part becomes important.
Append here a raised count into your process name: `Demo01` becomes `Demo02`.
All items with the old process will try to finish using the old one, while all new items will automatically use the new one.

Note: If you are sure the ones on the old process will continue fine in the new one, you can also manually set them to the new process
to continue here on DB level.


## Advanced Usage

### Repeating events often
If you are using repeating of events heavily (lot of loops can happen), you might want to raise the default limit of 10 repeats of an event.
Configure key `StateMachine.maxEventRepeats` can be used for this.

If you not only want to prevent immediate loops, but also take the whole history into account, you can use the the slightly slower
lookup in the persistence for this: Configure key `StateMachine.maxLookupInPersistence` can be set to `true` here.
This way you can also prevent timeout loops to run forever. Careful to include a manual event then around the loop to escape if that happens.

### Enable detailed error logging
Add the detailed log listening into your Configure config (app.php):
```php
'Log' => [
    ...
    'statemachine' => [
        'className' => '...',
        'type' => 'statemachine',
        'levels' => ['debug'],
        'scopes' => ['statemachine'],
    ],
],
```

### Catching Exceptions on Controller Trigger
If you trigger events via buttons from your backend, and you know that
exceptions can be caught and you can safely redirect back to the page you came from, you can
enable exception-catching:
Add `'catch' => true` into the URL query string array. This will display an error flash message with the exception message then.

### Linking the state machine items to your entities
If you want to have clickable links from the items index/view to the actual records in your backend,
you need to provide a "map".
Use the Configure key `StateMachine.map`:
```
'StateMachine' => [
    'map' => [
        'Foo' => [
            'controller' => 'FooBars',
            ...
        ],
        'MyStateMachine' => 'MyModel',
        'MatchesModelName' => true,
    ],
];
```
You can either use a URL array, or a string map where the value is the controller name.
For the string map the URL needs to follow the CakePHP conventions for `view` URLs.
You can also use boolean `true` if the state machine name matches the controller name.

If no map can be found, the identifiers will be just displayed as string.

## Illuminator StateTask

If you installed the optional [IdeHelper](https://github.com/dereuromark/cakephp-ide-helper) plugin,
you can use its Illuminator to auto-add class constants to the handler classes for all your states.

Activate the task in your config as documented in IdeHelper:
```php
    'IdeHelper' => [
        'illuminatorTasks' => [
            \StateMachine\Illuminator\Task\StateTask::class,
        ],
    ],
```
Then run it over your StateMachineHandlers:
```
bin/cake illuminator illuminate src/StateMachine/
```
Tip: Add `-v -d` to dry-run it first.

Now it is convienient and DRY to use the class constants instead of magic strings in your code:
```php
if ($entity->state === MyStateMachineHandler::STATE_DONE) {
    ...
}
```


## Contributing

We are looking forward to your contributions.

To rebuild DTOs after updating the XML definitions execute this from your app:
```
bin/cake dto generate -p StateMachine
```

There are a few guidelines to follow:
* Passing tests (`composer test`) - Travis will also automatically check those for any PR then
* Coding standards (`composer cs-check` to check and `composer cs-fix` to fix)
* PHPStan (`composer stan`) -  The higher level, the better

You can check coverage using
```
composer test-coverage
```
and then browse to the index.html in tmp/coverage/ folder.
