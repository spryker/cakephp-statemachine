<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the License Agreement. See LICENSE file.
 */

namespace TestApp\View;

use Cake\View\View;

class AppView extends View
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        $this->loadHelper('Tools.Format');
    }
}
