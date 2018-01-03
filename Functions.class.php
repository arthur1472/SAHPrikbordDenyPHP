<?php
/**
 * Created by PhpStorm.
 * User: Arthur
 * Date: 27/11/2017
 * Time: 14:44
 */

class Functions
{
    private $firstRun;
    private $API;
    private $loginTries;

    public function __construct(API $API)
    {
        $this->API = $API;
        $this->firstRun = true;
        $this->loginTries = 0;
    }

    public function debugMessage($message) {
        echo "(".date("d-m-Y H:i:s", time()).") (Debug) $message".PHP_EOL;
    }

    public function loginCheck($username, $password) {
        if ($this->API->loggedIn()) {
            $this->debugMessage("Still logged in.");
            return true;
        } else {
            $successful = $this->API->login($username, $password);
            if ($successful) {
                $this->loginTries = 0;
                $token = $this->API->getToken();
                $this->tokenWrite($token);
                $this->debugMessage("Login successful.");
                return true;
            } else {
                $this->loginTries++;
                $this->debugMessage("Login unsuccessful, web response:" . $this->API->raw);
                return false;
            }
        }
    }

    public function tokenCreate() {
        try {
            $file = fopen("token.txt", "a+");
            fclose($file);
        } catch (Exception $e) {
            $this->debugMessage("Token.txt either doesn't exist or permissions are incorrect. | ".$e->getMessage());
            sleep(10);
            die();
        }
    }

    public function tokenExists() {
        if (file_exists("token.txt")) {
            return true;
        } else {
            return false;
        }
    }

    public function tokenRead() {
        try {
            $token = file_get_contents("token.txt");
            return $token;
        } catch (Exception $e) {
            $this->debugMessage("Token.txt either doesn't exist or permissions are incorrect. | ".$e->getMessage());
            sleep(10);
            die();
        }
    }

    public function tokenWrite($token) {
        try {
            $file = fopen("token.txt", "w+");
            fwrite($file, $token);
            fclose($file);
        } catch (Exception $e) {
            $this->debugMessage("Token.txt either doesn't exist or permissions are incorrect. | ".$e->getMessage());
            sleep(10);
            die();
        }
    }

    public function getLoginTries()
    {
        return $this->loginTries;
    }



}