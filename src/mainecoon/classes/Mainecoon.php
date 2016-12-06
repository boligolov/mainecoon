<?php

namespace Mainecoon;

class Mainecoon {

    private $dir = '.';
    private $headers;
    private $file = ''; // временный файл со структурой каталогов
    private $errors = [];
    private $logs = [];
    //private $is_ready = false;

    private $method = 'GET';
    private $is_ajax = false;
    private $domain;
    private $subdomain;

    public $fileList = [];
    public $dirList = [];

    /**
     *  Переключатель определяет, загружена ли конфигурация.
     *  Если нет - показываем окно с настройками "по-умолчанию".
     */
    public $display_settings_page = true;

    /**
     *   Поля для объектов-хранилищ
     */
    public $config;
    public $request;
    public $cookie;
    public $lang;
    public $view;


    public function __construct()
    {

    }



    public function error($message)
    {
        $this->errors[] = $message;
        return false;
    }



    public function log($message)
    {
        if ($this->config->get('debug'))
        {
            $this->logs[] = $message;
        }
    }



    public function test()
    {
        // Тестируем необходимый набор параметров
        $done = true;

        if (($this->config->get('test.requirements') && !$this->request->is_ajax)
            OR
            ($this->request->is_ajax && $this->config->get('test.on_ajax')))
        {

            $version = explode('.', PHP_VERSION); // 0.1.2

            if ($version[0] < 5)
            {
                $this->error(Lang::t('php/outdated'));
                $done = false;
            }

            if ($version[0] >= 5 && $version[1] < 3)
            {
                $this->error(Lang::t('php/outdated'));
                $done = false;
            }

            // Проверка на существование временного каталога
            if (!file_exists($this->config->get('temp.dir')))
            {
                if ($this->config->get('temp.create'))
                {
                    // Если не существует и в настройках указано создавать - пытаемся создать
                    if (mkdir($this->config->get('temp.dir'), 0755))
                    {
                        $this->log(Lang::t('debug/temp_created'));
                    }
                    else
                    {
                        $this->view->error(Lang::t('temp/cannot_create'));
                        $done = false;
                    }
                }
                else
                {
                    $this->error(Lang::t('temp/not_exists'));
                    $done = false;
                }
            }

            // Проверяем временный каталог на запись
            if (!is_writable($this->config->get('temp.dir')))
            {
                $this->error(Lang::t('temp/cannot_write'));
                $done = false;
            }
            $this->log(Lang::t('debug/temp_writable'));

            //TODO: очищаем временный каталог

            //TODO: проверка ZIP-библиотеки

            if(!$done)
            {
                $this->view->error($this->errors);
            }
        }



    }



    public function actionSnapshot()
    {
        if (!$this->request->folder)
        {
            // Собираем список каталогов
            $this->getDirList();

            // Пишем его во временный файл
            $this->writeTemp($this->dirList);

            // Разворачиваем список каталогов во временные файлы
            $this->deployFolders();

            // Подгатавливаем массив ответа
            $response = [
                'message' => 'snapshot_summary',
                'count' => count($this->dirList),
                'html' => $this->view->render('folder_list', ['list' => $this->dirList], true),
                'data' => $this->dirList,
                'logs' => $this->logs,
                'errors' => $this->errors
            ];
        }
        elseif($this->request->folder)
        {
            // Подгружаем временный файл, чтобы обновить его
            $this->dirList = $this->loadTemp();

            // Получаем путь, по которому идем
            if (!empty($this->dirList[$this->request->folder]))
            {
                // Собираем информацию о файлах
                $this->fileList = $this->getFiles($this->dirList[$this->request->folder]['name']);

                // Пишем во временные файлы
                $this->writeTemp($this->fileList, $this->request->folder.'.temp');
                $this->dirList[$this->request->folder]['files'] = $this->fileList;
                $this->writeTemp($this->dirList);

                $response = [
                    'message' => 'snapshot_folder',
                    'count' => $this->fileList['count'],
                    'skipped' => $this->fileList['skipped'],
                    'status' => '<i class="fa fa-file-text-o" aria-hidden="true" title="Всего файлов"></i> '.$this->fileList['count'].' <i class="fa fa-file-o" aria-hidden="true" title="Пропущено файлов"></i> '.$this->fileList['skipped'],
                    'html' => $this->view->render(
                        'file_list',
                        array(
                            'list' => $this->fileList['files'],
                            'attributes' => $this->config->get('check')),
                        true),
                    'logs' => $this->logs,
                    'errors' => $this->errors
                ];
            }
            else
            {
                // Нет такого хеша
            }
        }

        // Возвращаем пользователю объест статус и объект с каталогами
        $this->view->json($response);
    }

    public function actionComparsion()
    {
        // Проверка загруженного файла
        // Очистка временных файлов (кроме загруженного)
        // Разворачивание загруженного файла
        // Отрисовка развернутого файла в браузере - каталогов и файлов
        // Получение реальных
    }

    public function actionResult()
    {
        if ($this->request->method == 'POST')
        {
            $file = $this->config->get('temp.dir').DS.$this->config->get('temp.file');
            $filename = date('Y-m-d').'_'.$this->request->domain.'_'.'mainecoon.data';
            $md5 = md5_file($file);

            if (file_exists($file))
            {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($filename).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                exit;
            }
        }
    }

    public function actionSettings()
    {
        if ($this->request->method == 'POST')
        {
            $where = 'cookie';
            $input = $this->request->post;


            if (!empty($input))
            {
                if (isset($input['config_write']) && $input['config_write'])
                {
                    $where = 'file';
                }

                $this->config->save($input, $where);
            }
        }
    }


    public function route()
    {
        $this->view->addHeader('title', MAINECOON.' '.VERSION.' -- '.$this->request->domain);

        if ($this->display_settings_page)
        {
            $this->view->page('settings');
        }

        if ($this->request->action)
        {
            $action = 'action'.ucfirst($this->request->action);

            if (is_callable(array($this, $action)))
            {
                $this->$action();
            }
        }
        else
        {
            $this->view->page('index');
        }
    }



    private function deployFolders()
    {
        foreach ($this->dirList as $hash => $item)
        {
            $this->writeTemp('', $hash.'.temp');
        }
    }



    public function getDirList()
    {
        $dirList = $this->getDirs(); // плоский список

        sort($dirList);

        foreach ($dirList as $item)
        {
            $hash = md5($item);
            $this->dirList[$hash] = [
                'name' => $item,
                'count' => 0,
                'files' => []
            ];
        }
    }



    public function writeTemp($data = '', $file = '')
    {
        if (!$file)
        {
            $file = $this->config->get('temp.file');
        }

        $file = $this->config->get('temp.dir').DIRECTORY_SEPARATOR.$file;

        file_put_contents($file, serialize($data));
    }



    private function loadTemp($file = '')
    {
        if (!$file)
        {
            $file = $this->config->get('temp.dir').DIRECTORY_SEPARATOR.$this->config->get('temp.file');
        }

        $content = file_get_contents($file);
        return unserialize($content);
    }



    public function stop()
    {
        if ($this->config->get('debug'))
        {
            foreach ($this->logs as $message)
            {
                echo $message."<br />";
            }
        }
        exit();
    }



    public function md5array($array)
    {

    }



    public function simpleDirectoryIteratoion($directory)
    {
        $list = [];

        $dir = opendir($directory);

        while(($file = readdir($dir)) !== false)
        {
            if ($file == '.' || $file == '..') continue;

            $path = $directory.DIRECTORY_SEPARATOR.$file;

            if (!is_dir($path)) continue;

            //echo $directory."<br >";

            if (is_dir($path))
            {
                $list[$path] = $this->simpleDirectoryIteratoion($path);
            }
            /*		    else
                        {
                            $list[] = $file;
                        }*/

        }

        closedir($directory);

        return $list;
    }



    public function getDirs($directory = "*")
    {
        $list = glob($directory, GLOB_ONLYDIR | GLOB_NOSORT);

        foreach ($list as $dir) {
            $list = array_merge($list, $this->getDirs($dir.DIRECTORY_SEPARATOR.basename($directory)));
        }

        return $list;
    }



    public function getFiles($directory)
    {
        $list = array(
            'count' => 0,
            'skipped' => 0,
            'files' => array()
        );

        //TODO: исключение директории по маске пути

        $iterator = new \DirectoryIterator ($directory);

        foreach ($iterator as $info)
        {
            $filename = $info->__toString();
            $path = $directory.DIRECTORY_SEPARATOR.$filename;

            if ($info->isFile())
            {
                $list['files'][$filename] = array(
                    'comment'   => '',
                    'size'      => '-',
                    'mime'      => '-',
                    'ctime'     => '-',
                    'mtime'     => '-',
                    'atime'     => '-',
                    'ext'       => '-',
                    'md5'       => '-',
                    'rights'    => '-'
                );

                if ($this->config->get('check.size'))
                {
                    $list['files'][$filename]['size'] = $info->getSize();

                    if ($list['files'][$filename]['size'] >  $this->config->get('exclude.size.more'))
                    {
                        $list['files'][$filename]['comment'] = 'Size more then '.$this->config->get('exclude.size.more').' bytes. Skipped.';
                        $list['skipped']++;
                        continue;
                    }

                    if ($list['files'][$filename]['size'] < $this->config->get('exclude.size.less'))
                    {
                        $list['files'][$filename]['comment'] = 'Size less then '.$this->config->get('exclude.size.less').' bytes. Skipped.';
                        $list['skipped']++;
                        continue;
                    }
                }


                if ($this->config->get('check.ext'))
                {
                    $list['files'][$filename]['ext'] = $info->getExtension();

                    if (in_array($list['files'][$filename]['ext'], $this->config->get('exclude.ext')))
                    {
                        $list['files'][$filename]['comment'] = 'Extension '.$list['files'][$filename]['ext'].' in exclude list. Skipped.';
                        $list['skipped']++;
                        continue;
                    }
                }

                if ($this->config->get('check.mime'))
                {
                    $list['files'][$filename]['mime'] = mime_content_type($path);
                }

                if ($this->config->get('check.ctime'))
                {
                    $list['files'][$filename]['ctime'] = $info->getCTime();
                }
                if ($this->config->get('check.mtime'))
                {
                    $list['files'][$filename]['mtime'] = $info->getMTime();
                }
                if ($this->config->get('check.atime'))
                {
                    $list['files'][$filename]['atime'] = $info->getATime();
                }
                if ($this->config->get('check.md5'))
                {
                    $list['files'][$filename]['md5'] = md5_file($path);
                }
                if ($this->config->get('check.rights'))
                {
                    $list['files'][$filename]['rights'] = $info->getPerms();
                }

/*                $list[$filename] = [
                    'comment' => '',
                    'size' => $info->getSize(),
                    'mime' => mime_content_type($path),
                    'ctime' => $info->getCTime(),
                    'mtime' => $info->getMTime(),
                    'atime' => $info->getATime(),
                    'ext' => $info->getExtension(),
                    'md5' => md5_file($path),
                    'rights' => $info->getPerms()
                ];*/
                //echo $info->__toString()."<br />";
            }
/*            elseif (!$info->isDot ())
            {
                $list[$filename] = $this->directoryIteratoion($path);
            }*/
        }

        $list['count'] = count($list['files']);

        return $list;
    }





    /*	public function directoryIteratoion($directory)
        {
            $list = [];

            $iterator = new DirectoryIterator ($directory);

            foreach ($iterator as $info)
            {
                $filename = $info->__toString();
                $path = $directory.DIRECTORY_SEPARATOR.$filename;

                if ($info->isFile())
                {
                    $list[$filename] = [
                        'size' => $info->getSize(),
                        'mime' => mime_content_type($path),
                        'ctime' => $info->getCTime(),
                        'mtime' => $info->getMTime(),
                        'atime' => $info->getATime(),
                        'ext' => $info->getExtension(),
                        'md5' => md5_file($path),
                    ];
                    //echo $info->__toString()."<br />";
                }
                elseif (!$info->isDot ())
                {
                    $list[$filename] = $this->directoryIteratoion($path);
                }
            }

            return $list;
        }*/
}