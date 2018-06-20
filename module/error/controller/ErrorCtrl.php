<?php
namespace module\error\controller;

use lying\event\ExceptionEvent;
use lying\service\Controller;

/**
 * Class ErrorCtrl
 * @package module\error\controller
 */
class ErrorCtrl extends Controller
{
    /**
     * 错误处理句柄
     * @param ExceptionEvent $event
     */
    public function index(ExceptionEvent $event)
    {
        $event->stop = true;
        $exception = $event->e;
        $this->assign('msg', $exception->getMessage());
        echo $this->render();
    }
}
