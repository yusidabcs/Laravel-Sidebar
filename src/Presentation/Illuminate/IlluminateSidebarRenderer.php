<?php

namespace Maatwebsite\Sidebar\Presentation\Illuminate;

use Illuminate\Contracts\View\Factory;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Presentation\SidebarRenderer;
use Maatwebsite\Sidebar\Sidebar;

class IlluminateSidebarRenderer implements SidebarRenderer
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var string
     */
    protected $view = 'sidebar::menu';

    /**
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param Sidebar $sidebar
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(Sidebar $sidebar)
    {
        $menu = $sidebar->getMenu();

        if ($menu->isAuthorized()) {
            $groups = [];
            $pinned_items = [];
            foreach ($menu->getGroups() as $group) {
                $groups[] = (new IlluminateGroupRenderer($this->factory))->render($group);

                foreach ($group->getItems() as $item) {
                    if($item->pinnedGroup() && $item->isAuthorized()) {
                        $pinned_items[] = $item;
                    }
                }
            }

            return $this->factory->make($this->view, [
                'groups' => $groups,
                'pinned_items' => $this->sortPinnedItem($pinned_items),
            ]);
        }
    }

    protected function sortPinnedItem($pinned_items)
    {
        $pinned_name = array_map(function(Item $item) {
            return $item->getName();
        } , $pinned_items);

        sort($pinned_name , SORT_NATURAL);

        $sorted = [];
        foreach ($pinned_name as $name) {
            foreach ($pinned_items as $item) {
                if($item->getName() == $name){
                    array_push($sorted , (new IlluminateItemRenderer($this->factory))->render($item));
                    break;
                }
            }
        }

        return $sorted;
    }
}
