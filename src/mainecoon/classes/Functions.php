<?php

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
}