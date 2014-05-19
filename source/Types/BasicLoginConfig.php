<?php
namespace Grout\Cyantree\BasicLoginModule\Types;

class BasicLoginConfig
{
    public $username;
    public $password;

    public $realm;

    public $urls = array();

    public $expires = 86400;

    public $extendExpiration = true;
}