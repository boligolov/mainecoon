<?php

namespace Mainecoon;

class Functions {

    public static function dumper($object) {
        echo "<pre>";
        print_r($object);
        echo "</pre>";
    }

    public static function salt()
    {
        return substr(sha1(mt_rand()),0,22);
    }

    public static function hash($password, $salt) {
        return crypt($password, '$2a$10$'.$salt); // $2a$ - blowfish
    }

    public static function uploadFile($file)
    {
        $config = \Mainecoon\Config::getInstance();

        $fileTo = $config->get('temp.dir').DS.$config->get('temp.uploaded');

        copy($file['tmp_name'], $fileTo);
        chmod($file, 0644);
        unlink($file['tmp_name']);
    }
}