<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SideBarSuper extends Component
{
    public $tab;
    public $selected;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($tab, $selected)
    {
        $this->selected = $selected;
        $this->tab = $tab;
    }

    public function isSelected($tab, $option)
    {
        return ($option === $this->selected && $tab === $this->tab);
    }

    public function isTab($tab)
    {
        return $tab === $this->tab;
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.side-bar-super');
    }
}
