<?php

namespace App\Http\ViewComposers;

use App\Http\Controllers\RightSidebar;
use Illuminate\View\View;

class RightSidebarComposer
{
    protected $rightSidebarController;

    public function __construct()
    {
        $this->rightSidebarController = new RightSidebar;
    }

    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        $sidebarData = $this->rightSidebarController->getSidebarData();
        $view->with('sidebarData', $sidebarData);
    }
}
