<?php

namespace Maatwebsite\Sidebar\Presentation\Illuminate;

use Illuminate\Contracts\View\Factory;
use Maatwebsite\Sidebar\Item;
use Maatwebsite\Sidebar\Presentation\SidebarRenderer;
use Maatwebsite\Sidebar\Sidebar;
use Illuminate\Support\Facades\Log;

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
     * @var string
     */
    protected $isDrawer = false;
    protected $isReturnData = false;

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
                if($this->isDrawer) {
                    if ($group->isAuthorized()) {
                        $module = $group->module();
                        $groupModule=null;
                        if($module) {
                            $groupModule = [
                                'name' => $module->name,
                                'type' => $module->type,
                                'folder_name' => $module->folder_name,
                                'url' => $module->url
                            ];
                        }
                        $groups[] = [
                            'show_heading' => $group->shouldShowHeading(),
                            'name' => $group->getName(),
                            'module' => $groupModule,
                            'icon' => $group->getIcon(),
                            'route' => '',

                
                        ];
                    }
                } else {
                    $groups[] = (new IlluminateGroupRenderer($this->factory, $this->isDrawer))->render($group);
                }
                
                $routeGroupFromItem = false;
                foreach ($group->getItems() as $item) {
                    if($this->isDrawer && !$routeGroupFromItem && count($groups)>0 && $group->isAuthorized() && $item->isAuthorized() && $item->getRouteName() && $item->getRouteName()!='') {
                        $groups[count($groups) - 1]['route'] = $item->getRouteName();
                        $routeGroupFromItem =  true;
                    }
                    if($item->pinnedGroup() && $item->isAuthorized()) {
                        if($this->isDrawer) {
                            $module = $group->module();
                            $groupModule=null;
                            if($module) {
                                $groupModule = [
                                    'name' => $module->name,
                                    'type' => $module->type,
                                    'folder_name' => $module->folder_name,
                                    'url' => $module->url
                                ];
                            }

                            $pinned_items[] = [
                                'route_name' => $item->getRouteName(),
                                'icon' => $item->getIcon(),
                                'name' => $item->getName(),
                                'module' => $groupModule
                            ];
                        } else {
                            $pinned_items[] = $item;
                        }
                    }
                }

            }
            if($this->isDrawer && $this->isReturnData) {
                return [
                    'groups' => $groups,
                    'pinned_items' => $pinned_items,
                ];
            }
            return $this->factory->make($this->view, [
                'groups' => $groups,
                'pinned_items' => $this->isDrawer ? $pinned_items : $this->sortPinnedItem($pinned_items),
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

    // 
    // Custom 
    // 
    public function setView($view) {
        $this->view = $view;
    }

    public function setIsDrawer($drawer) {
        $this->isDrawer = $drawer;
    }

    public function setReturnData($bool) {
        $this->isReturnData = $bool;
    }
}
