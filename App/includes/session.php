<?php
if (!defined('_CODE')) {
    die('Access denied...');
}

// Glue Session
function setSession($key, $value)
{
    return $_SESSION[$key] = $value;
}

// Read Session
function getSession($key = '')
{
    if (empty($key)) {
        return $_SESSION;
    } else {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }
}

//delete session
function removeSession($key)
{
    if (empty($key)) {
        session_destroy();
        return true;
    } else {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            return true;
        }
    }
}
//set Flash data
function setFlashData($key, $value)
{
    $key = 'flash_' . $key;

    return setSession($key, $value);
}

//get Flash data
function getFlashData($key)
{
    $key = 'flash_'. $key;
    $data = getSession($key);
    removeSession($key);

    return $data;
}
