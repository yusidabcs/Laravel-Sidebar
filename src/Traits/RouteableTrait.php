<?php

namespace Maatwebsite\Sidebar\Traits;

trait RouteableTrait
{
    /**
     * @var string
     */
    protected $url = '#';
    protected $routeName;


    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function getRouteName()
    {
        return $this->routeName;
    }



    /**
     * @param string $url
     *
     * @return $this
     */
    public function url($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param       $route
     * @param array $params
     *
     * @return $this
     */
    public function route($route, $params = [])
    {
        $this->url(
            $this->container->make('url')->route($route, $params)
        );
        $this->routeName = $route;

        return $this;
    }
}
