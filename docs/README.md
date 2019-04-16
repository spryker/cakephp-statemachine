#  CakePHP StateMachine Plugin Documentation

## Creating a new State Machine
You can generate a fresh XML file for a specific process using
```
bin/cake state_machine init MyProcessName
```
It will automatically append `01` as version suffix: `MyProcessName01.xml`

Now you can add your states, transitions and events to it.
Using the XSD provided, the IDE should be able to give you full autocomplete/typehinting here.


## Using it
Once your XML is ready to be validated, check it out as live preview in the backend:
- Go to `/admin/state-machine` and select the process
- Adjust your state machine further and just press F5 to reload the preview until it looks as expected

### Implement commands and conditions
The commands and conditions will most likely be still red, as they are not implemented yet.
Let's hook them up to the PHP counterpart then.

...


### First demo run

...

### Configuration

...


## Contributing


We are looking forward to your contributions.

There are a few guidelines to follow:
* Passing tests (`composer test`) - Travis will also automatically check those for any PR then
* Coding standards (`composer cs-check` to check and `composer cs-fix` to fix)
* PHPStan (`composer phpstan`) -  The higher level, the better

You can check coverage using
```
composer test-coverage
```
and then browse to the index.html in tmp/coverage/ folder.
