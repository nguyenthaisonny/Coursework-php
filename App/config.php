<?php

const _MODULE = 'home';
const _ACTION = 'dashboard';
const _CODE = true;
//DB data
const _HOST = 'localhost';
const _DB = 'app';
const _USER = 'root';
const _PASS = '';
// config host
define('_WEB_HOST', 'http://'. $_SERVER['HTTP_HOST']. '/coursework/App');
define('_WEB_HOST_TEMPLATES', _WEB_HOST. '/templates');
// config path
define('_WEB_PATH', __DIR__);
define('_WEB_PATH_TEMPLATES', _WEB_PATH. '/templates');