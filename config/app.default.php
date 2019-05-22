<?php

use StateMachine\Graph\Adapter\PhpDocumentorGraphAdapter;

return [
    'StateMachine' => [
        'handlers' => [
        ],
        'graphAdapter' => PhpDocumentorGraphAdapter::class,
        'maxEventRepeats' => 10,
        'maxLookupInPersistence' => false,
        'pathToXml' => null,
    ],
];
