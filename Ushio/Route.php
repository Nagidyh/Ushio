<?php

/**
 * 将 URL 解析为 Route 对象。
 */
class Route
{
    public $group;
    public $control;
    public $action;
    public $params;

    public function __construct()
    {
        
    }

    public function init()
    {
        $route = $this->getRequest();
        $this->group = $route['group'];
        $this->control = $route['control'];
        $this->action = $route['action'];
        ! empty($route['param']) && $this->params = $route['param'];
    }

    /**
     * 解析 URL
     * @return array
     */
    public function getRequest()
    {
        $filter_param = array('<','>','"',"'",'% 3C','% 3E','% 22','% 27','% 3c','% 3e');
        $uri = str_replace($filter_param, '', $_SERVER['REQUEST_URI']);
        $path = parse_url($uri);
        if (strpos($path['path'], 'index.php') == 0) {
            $urlR0 = $path['path'];
        } else {
            $urlR0 = substr($path['path'], strlen('index.php') + 1);
        }
        $urlR = ltrim($urlR0, '/');
        if ($urlR == '') {
            $route = $this->parseTradition();
            return $route;
        }
        $regArr = explode('/',$urlR);
        foreach ($regArr as $key => $value) {
            if (empty($value)) {
                unset($regArr[$key]);
            }
        }
        $cnt = count($regArr);
        if (empty($regArr) || empty($regArr[0])) {
            $cnt = 0;
        }
        switch ($cnt) {
            case 0:
                $route['group'] = $GLOBALS['_config']['defaultApp'];
                $route['control'] = $GLOBALS['_config']['defaultController'];
                $route['action'] = $GLOBALS['_config']['defaultAction'];
                break;
            case 1:
                if(stripos($regArr[0], ':')) {
                    $gc = explode(':', $regArr[0]);
                    $route['group'] = $gc[0];
                    $route['control'] = $gc[1];
                    $route['action'] = $GLOBALS['_config']['defaultAction'];
                }
                break;
            default:
                if (stripos($regArr[0], ':')) {
                    $gc = explode(':', $regArr[0]);
                    $route['group'] = $gc[0];
                    $route['control'] = $gc[1];
                    $route['action'] = $regArr[1];
                } else {
                    $route['group'] = $GLOBALS['_config']['defaultApp'];
                    $route['control'] = $regArr[0];
                    $route['action'] = $regArr[1];
                }
                for($i = 2; $i < $cnt; $i ++) {
                    $route['param'][$regArr[$i]] = isset($regArr[++$i]) ? $regArr[$i] : '';
                }
                break;
        }
        // 处理 query 字符
        if (! empty($path['query'])) {
            parse_str($path['query'], $routeQ);
            if (empty($route['param'])) {
                $route['param'] = array();
            }
            $route['param'] += $routeQ;
        }
        return $route;
    }

    /**
     * 解析传统形式的 URL
     * @return array
     */
    public function parseTradition()
    {
        $route = [];
        if (! isset($_GET[$GLOBALS['_config']['UrlGroupName']])) {
            $_GET[$GLOBALS['_config']['UrlGroupName']] = '';
        }
        if (! isset($_GET[$GLOBALS['_config']['UrlControllerName']])) {
            $_GET[$GLOBALS['_config']['UrlControllerName']] = '';
        }
        if (! isset($_GET[$GLOBALS['_config']['UrlActionName']])) {
            $_GET[$GLOBALS['_config']['UrlActionName']] = '';
        }
        $route['group'] = $_GET[$GLOBALS['_config']['UrlGroupName']];
        $route['control'] = $_GET[$GLOBALS['_config']['UrlControllerName']];
        $route['action'] = $_GET[$GLOBALS['_config']['UrlActionName']];
        unset($_GET[$GLOBALS['_config']['UrlGroupName']]);
        unset($_GET[$GLOBALS['_config']['UrlControllerName']]);
        unset($_GET[$GLOBALS['_config']['UrlActionName']]);
        $route['param'] = $_GET;
        if (null == $route['group']) {
            $route['group'] = $GLOBALS['_config']['defaultApp'];
        }
        if (null == $route['control']) {
            $route['control'] = $GLOBALS['_config']['defaultController'];
        }
        if (null == $route['action']) {
            $route['action'] = $GLOBALS['_config']['defaultAction'];
        }

        return $route;
    }
}