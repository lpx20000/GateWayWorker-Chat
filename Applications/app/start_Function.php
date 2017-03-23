<?php
/**
 * Created by PhpStorm.
 * User: 雨鱼
 * Date: 2017/3/17
 * Time: 11:56
 */

function fixMessage($message)
{
    if (is_array($message)) {
        $new = [];
        foreach ($message as $item) {
            $new[] = htmlspecialchars(trim($item));
        }
        return $new;
    }
    return htmlspecialchars(trim($message));
}

function removeEmptyClientId($clients)
{
    foreach ($clients as $key=>$client) {
        if (count($client) ==0) {
            unset($clients[$key]);
        }
    }
    return $clients;
}
