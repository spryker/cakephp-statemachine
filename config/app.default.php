<?php

return [
    'StateMachine' => [
        'handlers' => [
        ],
        'graphAdapter' => \StateMachine\Graph\Adapter\PhpDocumentorGraphAdapter::class,
        'maxEventRepeats' => 10,
        'maxLookupInPersistence' => false,
        'pathToXml' => null,
    ],
    'IdeHelper' => [
        'illuminatorTasks' => [
            \StateMachine\Illuminator\Task\StateTask::class,
        ],
    ],
];
