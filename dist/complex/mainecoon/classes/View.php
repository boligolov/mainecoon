<?php

class View extends General
{
    private $headers = array();
    private $js = array();
    private $css = array();



    public function __construct($data = array())
    {
        parent::__construct($data);

        #{layout}#
        $this->layout = 'layout';
        #{/layout}#

        // Мы не предполагаем разные темы оформления и всякое цветовое безумие, поэтому
        // подключим всё, что нам надо сразу в конструкторе
        $this->addCSS('https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic');
        $this->addCSS('app.css');

        // Тоже самое и со скриптами
        $this->addJS(array(
            'src'=> '//code.jquery.com/jquery-1.12.4.min.js',
            'integrity' => 'sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=',
            'crossorigin' => 'anonymous'
        ));
        $this->addJS('mainecoon.js');
        $this->addJS(array(
            'src'=>'https://use.fontawesome.com/84747d4f89.js'
        ));
    }



    public function addCSS($style)
    {
        if (strpos($style, 'http:') === false && strpos($style, 'https:') === false)
        {
            $style = WEB_CSS.$style;
        }

        $this->css[] = $style;
    }



    public function addJS($js)
    {
        if (!is_array($js)) $js = WEB_JS.$js;

        $this->js[] = $js;
    }



    public function addHeader($title, $data)
    {
        $this->headers[$title] = $data;
    }



    public function logs($data = array())
    {
        return $this->render('list', array('list' => $data, 'class' => 'logs'), true);
    }



    public function errors($data = array())
    {
        return $this->render('list', array('list' => $data, 'class' => 'errors'), true);
    }



    public function error($data = array())
    {
        $errors = $this->errors($data);
        $this->page('error', array('errors' => $errors));
    }



    public function page($template, $data = array())
    {
        $header = $this->render('header',  array(
            'css' => $this->render('css', array('list' => $this->css), true),
            'title' => $this->headers['title']
        ), true);

        $footer = $this->render('footer', array(
            'js' => $this->render('js', array('list' => $this->js), true),
            'copyright' => MAINECOON.' '.VERSION,
            'link' => HOME_LINK
        ), true);

        $content = $this->render($template, $data, true);

        if (is_file(DIR_VIEW.$this->layout.'.php'))
        {
            $data['content'] = $content;
            $data['header'] = $header;
            $data['footer'] = $footer;

            $this->render($this->layout, $data);
        }

        echo $header;
        echo $content;
        echo $footer;
        exit(EXIT_OK);
    }



    public function render($template, $data = [], $return = false)
    {
        #{view}#
        $template .= '.php';

        if (is_file(DIR_VIEW.$template))
        {
            extract($data);

            ob_start();
            ob_implicit_flush(false);

            require DIR_VIEW.$template;

            $content = ob_get_clean();

            if ($return)
            {
                return $content;
            }

            echo $content;
            exit(EXIT_OK);
        }

        echo 'template '.$template.' not found in '.DIR_VIEW;
        exit(EXIT_TEMPLATE_NOT_FOUND);
        #{/view}#
    }



    public function json($data, $return = false)
    {
        $output = json_encode($data, JSON_UNESCAPED_UNICODE);

        if ($return) return $output;

        echo $output;
        die();
    }
}