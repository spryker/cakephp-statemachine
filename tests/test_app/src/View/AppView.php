<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace App\View;

use Cake\View\View;

class AppView extends View
{
    /**
     * @return void
     */
    public function initialize()
    {
        $this->loadHelper('Tools.Format');
    }
}
