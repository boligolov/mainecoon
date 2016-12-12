<?php

namespace Mainecoon;

class Mainecoon {

    private $state = array(
        'salt'              => '',
        'operation'         => '',
        'operation_start'   => '',
        'operation_error'   => 0,
        'operation_end'     => '',
        'uploaded_file'     => '',
        'uploaded_checked'  => false
    );


    public $errors = array();
    public $logs = array();
    public $fileList = array();
    public $dirList = array();

    /**
     *  Переключатель определяет, загружена ли конфигурация.
     *  Если нет - показываем окно с настройками "по-умолчанию".
     */
    private $settings_page = true;


    /**
     *   Поля для объектов-хранилищ
     */
    public $config;
    public $request;
    public $cookie;
    public $view;
    public $temp;


    public function __construct()
    {

    }

    public function disableSettingsPage()
    {
        $this->settings_page = false;
    }

    public function loadState()
    {
        $state = $this->temp->load();

        if (!$state)
        {
            $this->state['salt'] = Functions::salt();

            $this->saveState();

            return false;
        }

        $this->state = array_merge($this->state, $state);

        return true;
    }

    public function saveState()
    {
        $this->temp->write($this->state);
    }


    public function updateState($input = array())
    {
        foreach ($input as $key => $value)
        {
            $this->state[$key] = $value;
        }

        $this->saveState();
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
                $this->error(Lang::t('php.outdated'));
                $done = false;
            }

            if ($version[0] >= 5 && $version[1] < 3)
            {
                $this->error(Lang::t('php.outdated'));
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
                        $this->log(Lang::t('debug.temp_created'));
                    }
                    else
                    {
                        $this->view->error(Lang::t('temp.cannot_create'));
                        $done = false;
                    }
                }
                else
                {
                    $this->error(Lang::t('temp.not_exists'));
                    $done = false;
                }
            }

            // Проверяем временный каталог на запись
            if (!is_writable($this->config->get('temp.dir')))
            {
                $this->error(Lang::t('temp.cannot_write'));
                $done = false;
            }
            $this->log(Lang::t('debug.temp_writable'));

            if ($this->config->get('temp.clean') && !$this->state['operation'])
            {
                $this->temp->clean(array(
                    $this->config->get('temp.file'),
                    $this->config->get('temp.uploaded'),
                    $this->config->get('temp.snapshot')
                ));
            }

            //TODO: проверка ZIP-библиотеки

            if(!$done)
            {
                $this->view->error($this->errors);
            }
        }

        return $done;
    }






    public function actionSnapshot()
    {
        if (!$this->request->folder)
        {
            $this->updateState(array(
                'operation' => 'snapshot',
                'operation_start' => time(),
            ));

            // Собираем список каталогов
            $this->getDirList();

            // Пишем его во временный файл
            $this->temp->write($this->dirList, $this->config->get('temp.snapshot'));

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
            $this->updateState(array(
                'operation' => 'snapshot_folder',
                'operation_start' => time(),
            ));

            // Подгружаем временный файл, чтобы обновить его
            $this->dirList = $this->temp->load($this->config->get('temp.snapshot'));

            // Получаем путь, по которому идем
            if (!empty($this->dirList[$this->request->folder]))
            {
                // Собираем информацию о файлах
                $this->fileList = $this->getFiles($this->dirList[$this->request->folder]['name']);

                // Пишем во временные файлы
                $this->temp->write($this->fileList, $this->request->folder.'.temp');
                $this->dirList[$this->request->folder]['files'] = $this->fileList;
                $this->temp->write($this->dirList, $this->config->get('temp.snapshot'));

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

        $this->updateState(array(
            'operation_end' => time(),
        ));

        // Возвращаем пользователю объест статус и объект с каталогами
        $this->view->json($response);
    }

    public function actionComparsion()
    {
        if ($this->request->file['uploaded'])
        {
            // Проверка загруженного файла
            $this->dirList = $this->temp->load($this->config->get('temp.uploaded'));

            if (!$this->dirList)
            {
                // Отправляем JSON с ошибкой
            }

            // Очистка временных файлов (кроме загруженного)
            $this->temp->clean(array(
                $this->config->get('temp.file'),
                $this->config->get('temp.uploaded')
            ));

            // Разворачивание загруженного файла
            $this->deployFolders('compare-');

            // Отрисовка развернутого файла в браузере - каталогов и файлов
            $response = [
                'message' => 'snapshot_summary',
                'count' => count($this->dirList),
                'html' => $this->view->render('folder_list', ['list' => $this->dirList], true),
                'data' => $this->dirList,
                'logs' => $this->logs,
                'errors' => $this->errors
            ];

            // Получение реальных реальнх каталогов
            // Сканирование реальных каталогов

            $this->view->json($response);
        }
        elseif($this->request->folder)
        {

        }
    }

    public function actionResult()
    {
        if ($this->request->method == 'POST')
        {
            $file = DIR_TEMP.$this->config->get('temp.snapshot');
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



    private function deployFolders($prefix = '')
    {
        foreach ($this->dirList as $hash => $item)
        {
            $this->temp->write('', $prefix.$hash.'.temp');
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

            $path = $directory.DS.$file;

            if (!is_dir($path)) continue;

            //echo $directory."<br >";

            if (is_dir($path))
            {
                $list[$path] = $this->simpleDirectoryIteratoion($path);
            }

        }

        closedir($directory);

        return $list;
    }



    public function getDirs($directory = "*")
    {
        $list = glob($directory, GLOB_ONLYDIR | GLOB_NOSORT);

        $excluded = $this->config->get('exclude.path');

        foreach ($list as $key => $dir)
        {
            if (in_array($dir, $excluded))
            {
                unset($list[$key]);
                continue;
            }

            $list = array_merge($list, $this->getDirs($dir.DS.basename($directory)));
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

        $iterator = new \DirectoryIterator ($directory);

        foreach ($iterator as $info)
        {
            $filename = $info->__toString();
            $path = $directory.DS.$filename;

            if ($info->isFile())
            {
                
                $_comment   = '';
                $_size      = '-';
                $_mime      = '-';
                $_ctime     = '-';
                $_mtime     = '-';
                $_atime     = '-';
                $_ext       = '-';
                $_md5       = '-';
                $_rights    = '-';
                
                
                if ($this->config->get('check.ext'))
                {
                    $_ext = $info->getExtension();

                    if (in_array($_ext, $this->config->get('exclude.ext')))
                    {
                        //$_comment = 'Extension '.$_ext.' in exclude list. Skipped.';
                        $list['skipped']++;
                        continue;
                    }
                }

                if ($this->config->get('check.size'))
                {
                    $_size = $info->getSize();

                    if ($_size >  $this->config->get('exclude.size.more'))
                    {
                        //$_comment = 'Size more then '.$this->config->get('exclude.size.more').' bytes. Skipped.';
                        $list['skipped']++;
                        continue;
                    }

                    if ($_size < $this->config->get('exclude.size.less'))
                    {
                        //$_comment = 'Size less then '.$this->config->get('exclude.size.less').' bytes. Skipped.';
                        $list['skipped']++;
                        continue;
                    }
                }

                if ($this->config->get('check.mime'))
                {
                    $_mime = mime_content_type($path);
                }

                if ($this->config->get('check.ctime'))
                {
                    $_ctime = $info->getCTime();
                }
                if ($this->config->get('check.mtime'))
                {
                    $_mtime = $info->getMTime();
                }
                if ($this->config->get('check.atime'))
                {
                    $_atime = $info->getATime();
                }
                if ($this->config->get('check.md5'))
                {
                    $_md5 = md5_file($path);
                }
                if ($this->config->get('check.rights'))
                {
                    $_rights = $info->getPerms();
                }

                $list['files'][$filename] = array(
                    'comment'   => $_comment,
                    'size'      => $_size,
                    'mime'      => $_mime,
                    'ctime'     => $_ctime,
                    'mtime'     => $_mtime,
                    'atime'     => $_atime,
                    'ext'       => $_ext,
                    'md5'       => $_md5,
                    'rights'    => $_rights
                );
            }
        }

        $list['count'] = count($list['files']);

        return $list;
    }
}