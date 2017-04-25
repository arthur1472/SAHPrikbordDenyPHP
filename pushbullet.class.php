<?php
	/**
	* Class voor pushbullet
	*/
	class Pushbullet
	{
		
		private $accessToken,$deviceIden;
        private $i;

		function __construct()
		{
		}

		public function sendPush($title,$note,$type) {
			$url = "https://api.pushbullet.com/v2/pushes";
            $timeout = 5;
            if ($this->deviceIden != null) {
				$data = "type=$type&title=$title&body=$note&device_iden=" . $this->deviceIden;
			} else {
				$data = "type=$type&title=$title&body=$note";
			}
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            curl_setopt($ch, CURLOPT_USERPWD, $this->accessToken);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }

        public function getDevices() {
        	$url = "https://api.pushbullet.com/v2/devices";
        	$timeout = 20;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

            curl_setopt($ch, CURLOPT_USERPWD, $this->accessToken.":");

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }

        public function getPushes() {
            $url = "https://api.pushbullet.com/v2/pushes";
            $timeout = 20;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

            curl_setopt($ch, CURLOPT_USERPWD, $this->accessToken.":");

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }

        public function addQueue($title,$note,$type) {
            $this->i++;
            $url = "https://api.pushbullet.com/v2/pushes";
            $timeout = 5;
            $data = "type=$type&title=$title&body=$note";
            eval('$this->ch'.$this->i.' = curl_init();');
            eval('curl_setopt($this->ch'.$this->i.',CURLOPT_URL,$url);');
            eval('curl_setopt($this->ch'.$this->i.', CURLOPT_TIMEOUT, $timeout);');
            eval('curl_setopt($this->ch'.$this->i.', CURLOPT_POST, true);');
            eval('curl_setopt($this->ch'.$this->i.', CURLOPT_POSTFIELDS, $data);');
            eval('curl_setopt($this->ch'.$this->i.', CURLOPT_USERPWD, $this->accessToken);');
            eval('curl_setopt($this->ch'.$this->i.', CURLOPT_SSL_VERIFYPEER, 0);');
            eval('curl_setopt($this->ch'.$this->i.', CURLOPT_FORBID_REUSE, 1);');
            eval('curl_setopt($this->ch'.$this->i.', CURLOPT_FRESH_CONNECT, 1);');
            eval('curl_setopt($this->ch'.$this->i.', CURLOPT_RETURNTRANSFER, true);');
            
        }

        public function sendPushImproved() {
            $mh = curl_multi_init();

            for ($i = 1; $i <= $this->i; $i++) {
                eval('curl_multi_add_handle($mh,$this->ch'.$i.');');
            }

            $active = null;
            //execute the handles
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);

            while ($active && $mrc == CURLM_OK) {
                if (curl_multi_select($mh) != -1) {
                    do {
                        $mrc = curl_multi_exec($mh, $active);
                    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                }
            }

            for ($i = 1; $i <= $this->i; $i++) {
                eval('curl_multi_remove_handle($mh, $this->ch'.$i.');');
                eval('$this->ch'.$i.' = null;');
            }
            curl_multi_close($mh);
            $this->i = 0;
        }

        public function setDevice($device) {
        	$this->deviceIden = $device;
        }

        public function setToken($accessToken) {
        	$this->accessToken = $accessToken;
        }

        public function getDevice() {
            return $this->deviceIden;
        }

        public function getToken() {
            return $this->accessToken;
        }

        public function getI() {
            return $this->i;
        }
	}
