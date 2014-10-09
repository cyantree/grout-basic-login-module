<?php
namespace Grout\Cyantree\BasicLoginModule\Actions;

use Cyantree\Grout\App\Task;

class CheckAuthorizationAction
{
    /** @var Task */
    public $task;
    public $module;

    public $username;
    public $password;
    public $name;
    public $expiration;
    public $extendExpiration;

    public function execute()
    {
        if ($this->username === null || $this->password === null) {
            return false;

        } else {
            $setCookie = false;
            $cookieName = 'grout_login_' . substr(md5($this->name . $this->username), 0, 8);
            $success = false;

            // Check post
            if ($this->task->request->post->get('login')) {
                if ($this->task->request->post->get('username') != $this->username
                    || $this->task->request->post->get('password') != $this->password
                ) {
                    return false;
                }

                $setCookie = true;
                $success = true;
            }

            // Check authorization cookie
            if (!$success) {
                $data = explode('_', $this->task->request->cookies->get($cookieName));

                if (count($data) == 2) {
                    if (time() - $data[0] > $this->expiration) {
                        return false;
                    }

                    if (md5($data[0] . $this->username . $this->password . $this->name) != $data[1]) {
                        return false;
                    }

                    if ($this->extendExpiration) {
                        $setCookie = true;
                    }

                    $success = true;
                }
            }

            if ($success) {
                if ($setCookie) {
                    $t = time();
                    setcookie($cookieName, $t . '_' . md5($t . $this->username . $this->password . $this->name), $t + $this->expiration, '/', null, null, true);
                }

                return true;

            } else {
                return false;
            }
        }
    }
}
