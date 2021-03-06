<?php

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

        if (isset($this->file['tmp_name']) && isset($this->file['size']) && file_exists($this->file['tmp_name']))
        {
            $this->uploadFile();
        }
    }

    public function uploadFile()
    {
        $fileTo = DIR_TEMP.$this->config->get('temp.uploaded');

        copy($this->file['tmp_name'], $fileTo);
        chmod($this->file, 0644);
        unlink($this->file['tmp_name']);
        $this->file['uploaded'] = true;
    }
}