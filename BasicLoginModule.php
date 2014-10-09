<?php
namespace Grout\Cyantree\BasicLoginModule;

use Cyantree\Grout\App\Module;
use Cyantree\Grout\App\Route;
use Cyantree\Grout\App\Task;
use Grout\Cyantree\BasicLoginModule\Actions\CheckAuthorizationAction;
use Grout\Cyantree\BasicLoginModule\Types\BasicLoginConfig;

class BasicLoginModule extends Module
{
    /** @var BasicLoginConfig */
    public $moduleConfig;

    public function init()
    {
        $this->app->configs->setDefaultConfig($this->id, new BasicLoginConfig());

        /** @var BasicLoginConfig moduleConfig */
        $this->moduleConfig = $this->app->configs->getConfig($this->id);

        foreach ($this->moduleConfig->urls as $url) {
            $this->secureUrl($url);
        }
    }

    /**
     * @param Task $task
     * @param Route $page
     */
    public function routeRetrieved($task, $page)
    {
        $secured = $page->data->get('secured');
        $whitelisted = $task->data->get('whitelistedByBasicLogin');

        if ($secured) {
            if ($whitelisted) {
                return false;

            } else {
                $a = new CheckAuthorizationAction();
                if ($page->data->get('username')) {
                    $a->username = $page->data->get('username');
                    $a->password = $page->data->get('password');
                    $a->expiration = $page->data->get('expires');
                    $a->name = $page->data->get('name');
                    $a->extendExpiration = $page->data->get('extendExpiration');

                } else {
                    $a->username = $this->moduleConfig->username;
                    $a->password = $this->moduleConfig->password;
                    $a->expiration = $this->moduleConfig->expires;
                    $a->name = $this->moduleConfig->realm;
                    $a->extendExpiration = $this->moduleConfig->extendExpiration;
                }

                $a->task = $task;
                $a->module = $this;
                return !$a->execute();
            }

        } elseif ($secured === false) {
            $task->data->set('whitelistedByBasicLogin', true);

            return false;
        }

        return true;
    }


    public function secureUrl($url, $username = null, $password = null, $name = null, $expires = 86400, $extendExpiration = true)
    {
        $this->addRoute($url, 'Pages\SecuredPage', array('secured' => true, 'username' => $username, 'password' => $password, 'name' => $name, 'expires' => $expires, 'extendExpiration' => $extendExpiration));
    }

    public function whitelistUrl($url)
    {
        $this->addRoute($url, 'Pages\SecuredPage', array('secured' => false), 10);
    }
}
