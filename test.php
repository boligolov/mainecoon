<?php

$iterator = new \DirectoryIterator (dirname(__FILE__));

foreach ($iterator as $info)
{
    $filename = $info->__toString();
    //echo $filename;

    $path = dirname(__FILE__).DIRECTORY_SEPARATOR.'make.php';

    if ($info->isFile())
    {
        if ($filename != 'make.php') { continue; }

        echo 'size';
        echo "<br />";
        $start = microtime(true);
        for($i = 0; $i < 100000; $i++)
        {
            $size = $info->getSize();
        }
        $end = microtime(true);
        echo ($end - $start);

        echo "<br />";
        echo "<br />";
        echo "<br />";

        echo 'ext';
        echo "<br />";
        $start = microtime(true);
        for($i = 0; $i < 100000; $i++)
        {
            $ext = $info->getExtension();
        }
        $end = microtime(true);
        echo ($end - $start);


        echo "<br />";
        echo "<br />";
        echo "<br />";

        echo 'mime';
        echo "<br />";
        $start = microtime(true);
        for($i = 0; $i < 100000; $i++)
        {
            //$mime = mime_content_type($path);
            $result = new finfo();
            $mime =  $result->file($path, FILEINFO_MIME_TYPE);
        }
        $end = microtime(true);
        echo ($end - $start);

        echo "<br />";
        echo "<br />";
        echo "<br />";

        echo 'md5';
        echo "<br />";
        $start = microtime(true);
        for($i = 0; $i < 100000; $i++)
        {
            $md5 = md5_file($path);
        }
        $end = microtime(true);
        echo ($end - $start);

        echo "<br />";
        echo "<br />";
        echo "<br />";

        echo 'perms';
        echo "<br />";
        $start = microtime(true);
        for($i = 0; $i < 100000; $i++)
        {
            $perms = $info->getPerms();
        }
        $end = microtime(true);
        echo ($end - $start);


        echo "<br />";
        echo "<br />";
        echo "<br />";

        echo 'ctime';
        echo "<br />";
        $start = microtime(true);
        for($i = 0; $i < 100000; $i++)
        {
            $ctime = $info->getCTime();
        }
        $end = microtime(true);
        echo ($end - $start);

        echo "<br />";
        echo "<br />";
        echo "<br />";

        echo 'mtime';
        echo "<br />";
        $start = microtime(true);
        for($i = 0; $i < 100000; $i++)
        {
            $ctime = $info->getMTime();
        }
        $end = microtime(true);
        echo ($end - $start);

        echo "<br />";
        echo "<br />";
        echo "<br />";

        echo 'atime';
        echo "<br />";
        $start = microtime(true);
        for($i = 0; $i < 100000; $i++)
        {
            $ctime = $info->getATime();
        }
        $end = microtime(true);
        echo ($end - $start);

        /*
                $mime = mime_content_type($path);
                $md5 = md5_file($path);
                $perms = $info->getPerms();
                $ctime = $info->getCTime();
                $mtime = $info->getMTime();
                $atime = $info->getATime();
                */
    }

    //break;
}



