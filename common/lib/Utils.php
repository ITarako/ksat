<?php

namespace common\lib;

class Utils
{
    static public function guidv4() :string
    {
        if (function_exists('com_create_guid') === true)
            return trim(com_create_guid(), '{}');

        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    static public function getRoleById(int $id) :string
    {
        $role = \Yii::$app->authManager->getRolesByUser($id);
        $role = array_pop($role);
        return ($role instanceof \yii\rbac\Role) ? $role->name : '';
    }

    static public function penniesToInt(string $value) :int
    {
        $value = number_format((float)$value, 2, '.', '');
        $arr = explode('.', $value);
        $value = implode($arr);
        return (int)$value;
    }

    static public function intToPennies(int $value) :string
    {
        $strValue = (string)$value;
        $start = substr($strValue, 0, -2);
        $end = substr($strValue, -2);
        $value = "$start.$end";
        return number_format((float)$value, 2, '.', ' ');
    }
}