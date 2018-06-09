<?php
namespace module\admin\controller;

use extend\Captcha\Captcha;
use lying\db\Connection;
use lying\service\Controller;
use lying\service\Cookie;
use lying\service\Maker;
use lying\service\Request;
use lying\service\Response;
use lying\service\Session;

/**
 * Class Basic
 * @package module\admin\controller
 */
class Basic extends Controller
{
    /**
     * @var Maker
     */
    protected $maker;

    /**
     * @var Cookie
     */
    protected $cookie;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var Captcha
     */
    protected $captcha;

    /**
     * @inheritdoc
     */
    protected function init()
    {
        parent::init();
        $this->maker = \Lying::$maker;
        $this->cookie = $this->maker->cookie;
        $this->session = $this->maker->session;
        $this->request = $this->maker->request;
        $this->response = $this->maker->response;
        $this->db = $this->maker->db;
        $this->captcha = new Captcha();

        $this->assign('CSRF', $this->request->getCsrfToken());
        $this->assign('HOST', $this->request->host(true));
        $this->assign('STATIC', $this->request->host(true) . '/static');
    }

    /**
     * 渲染返回结果
     * @param int $code 错误码
     * @param string $msg 错误信息
     * @param array $data 返回的数据
     * @return string
     */
    protected function renderJson($code, $msg = '', $data = [])
    {
        return json_encode(['code'=>$code, 'msg'=>$msg, 'data'=>$data]);
    }

    /**
     * 渲染layui数据表格的数据
     * @param array $data 数据数组
     * @param int $count 数据总量
     * @param int $code 错误码
     * @param string $msg 错误信息
     * @return string
     */
    protected function renderTable($data, $count, $code = 0, $msg = '')
    {
        return json_encode(['code'=>$code, 'msg'=>$msg, 'count'=>$count, 'data'=>$data]);
    }
}
