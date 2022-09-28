<?php

class Ushio
{
    private $route;

    public function run()
    {
        require_once _SYS_PATH.'Route.php';
        $this->route();
        $this->dispatch();


        echo '<br /> <span style="color: lightsalmon"><b>Ushio there</b></span> ';
    }

    /**
     * 路由方法，解析 Request , 将请求与参数分发到对应的 Module/Controller/Action 。
     * @return void
     */
    public function route()
    {
        $this->route = new Route();
        $this->route->init();
    }

    /**
     * 根据请求构造对应的 Handler 并处理。
     * @return void
     * @throws Exception
     */
    public function dispatch()
    {
        $controlName = $this->route->control . 'Controller';
        $actionName = $this->route->action . 'Action';
        $path = _APP.$this->route->group . DIRECTORY_SEPARATOR
            . 'module'. DIRECTORY_SEPARATOR
            . 'controller' . DIRECTORY_SEPARATOR
            . $controlName . '.php';
        require_once $path;
        $methods = get_class_methods($controlName);
        if (! in_array($actionName, $methods, true)) {
            throw new Exception(sprintf('方法名 %s->%s 不存在, 或非 public .',
            $controlName, $actionName));
        }
        $handler = new $controlName;
        $handler->param = $this->route->params;
        $handler->$actionName();
    }
}