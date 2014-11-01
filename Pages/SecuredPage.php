<?php
namespace Grout\Cyantree\BasicLoginModule\Pages;

use Cyantree\Grout\App\Page;
use Cyantree\Grout\App\Types\ResponseCode;
use Cyantree\Grout\Tools\StringTools;
use Grout\Cyantree\BasicLoginModule\BasicLoginModule;

class SecuredPage extends Page
{
    public function parseTask()
    {
        /** @var BasicLoginModule $module */
        $module = $this->task->module;

        $name = StringTools::escapeHtml($this->task->route->data->get('name', $module->moduleConfig->realm));

        $url = $this->task->url;

        $content = <<<HTML
<!doctype html>
<html>
<body>
<h1>Access denied</h1>
<p>Login to access “{$name}”.</p>
<form action="{$url}" method="post">
Username: <input type="text" name="username"><br>
Password: <input type="password" name="password"><br>
<input type="submit" name="login" value="Login">
</form>
</body>
</html>
HTML;

        $this->setResult($content, null, ResponseCode::CODE_401);
    }
}
