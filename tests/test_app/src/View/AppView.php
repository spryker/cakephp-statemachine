<?php

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
