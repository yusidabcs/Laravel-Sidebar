<?php

namespace Maatwebsite\Sidebar\Domain;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;
use Maatwebsite\Sidebar\Exceptions\LogicException;
use Maatwebsite\Sidebar\Group;
use Maatwebsite\Sidebar\Traits\AuthorizableTrait;
use Maatwebsite\Sidebar\Traits\CacheableTrait;
use Maatwebsite\Sidebar\Traits\CallableTrait;
use Maatwebsite\Sidebar\Traits\ItemableTrait;
use Serializable;

class DefaultGroup implements Group, Serializable
{
    use CallableTrait, CacheableTrait, ItemableTrait, AuthorizableTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $weight = 0;

    /**
     * @var bool
     */
    protected $heading = true;

    /**
     * @var Container
     */
    protected $container;

    protected $module;

    protected $subscription;
    protected $dropdown = true;

    protected $route='';

    protected $icon='';


    /**
     * Data that should be cached
     * @var array
     */
    protected $cacheables = [
        'name',
        'items',
        'weight',
        'heading'
    ];

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->items     = new Collection();
    }

    /**
     * @param string $name
     *
     * @return Group
     */
    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $weight
     *
     * @return Group
     */
    public function weight($weight)
    {
        if (!is_int($weight)) {
            throw new LogicException('Weight should be an integer');
        }

        $this->weight = $weight;

        return $this;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param bool $hide
     *
     * @return Group
     */
    public function hideHeading($hide = true)
    {
        $this->heading = !$hide;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldShowHeading()
    {
        return $this->heading ? true : false;
    }

    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;

        return $this;
    }

    public function subscription()
    {
        return $this->subscription;
    }

    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    public function module()
    {
        return $this->module;
    }


    public function setDropdown($dropdown)
    {
        $this->dropdown = $dropdown;
        return $this;
    }

    public function dropdown()
    {
        return $this->dropdown;
    }

    public function setRoute($route) {
        $this->route = $route;
    }

    public function setIcon($icon) {
        $this->icon = $icon;

    }

    public function getRoute() {
        return $this->route;
    }

    public function getIcon() {
        return $this->icon;
    }
}
