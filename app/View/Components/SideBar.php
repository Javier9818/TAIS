<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SideBar extends Component
{
    public $tab;
    public $selected;
    public $empresa;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($tab, $selected, $empresa)
    {
        $this->selected = $selected;
        $this->tab = $tab;
        $this->empresa = $empresa;
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
        return view('components.side-bar');
    }
}
