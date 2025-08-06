<?php

return [
    'StateMachine' => [
        'handlers' => [
        ],
        'graphAdapter' => \StateMachine\Graph\Adapter\PhpDocumentorGraphAdapter::class,
        'maxEventRepeats' => 10,
        'maxLookupInPersistence' => false, // @deprecated: Deprecated, not functional
        'eventRepeatAction' => 0, // Modulo value for triggering this action, e.g. 20 ( => every 20)
        'pathToXml' => null,
    ],
    'IdeHelper' => [
        'illuminatorTasks' => [
            \StateMachine\Illuminator\Task\StateTask::class,
        ],
    ],
];
