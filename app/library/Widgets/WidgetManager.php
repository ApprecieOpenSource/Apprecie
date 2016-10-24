<?php
namespace Apprecie\Library\Widgets;

class WidgetManager
{
    public static function get($widgetClass, $action = 'index', $parameters = null)
    {
        return new $widgetClass($action, $parameters);
    }
}