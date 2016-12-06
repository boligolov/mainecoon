<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 21.11.2016
 * Time: 1:36
 */

namespace Mainecoon;


class Request
{
    public $method = 'GET';
    public $is_ajax = false;
    public $domain;
    public $subdomain;
    public $action;
    public $folder;
    public $post;
    public $file;

    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
        $this->is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
        $this->subdomain = count($hh = explode('.', $_SERVER['HTTP_HOST'])) > 2 ? array_slice($hh, 0, -2) : null;
        $this->domain = empty($this->subdomain) ? $_SERVER['HTTP_HOST'] : substr($_SERVER['HTTP_HOST'], strlen(implode('.', $this->subdomain)) + 1);
        $this->action = isset($_POST['action']) ? trim($_POST['action']) : '';
        $this->folder = isset($_POST['folder']) ? trim($_POST['folder']) : '';
        $this->post = isset($_POST) ? $_POST['folder'] : array();
        $this->file = isset($_FILES) ? $_FILES['file'] : array();

        if ($this->file['tmp_name'] && $this->file['size'] && file_exists($this->file['tmp_name']))
        {
            Functions::uploadFile($this->file);
        }
    }
}