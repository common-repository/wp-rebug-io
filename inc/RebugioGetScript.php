<?php

class RebugioGetScript {

	private $strToReplaceProjectKey = '/%PROJECT_KEY%/';
	private $projectKey             = '';
	private $scriptUrl              = '';
	private $expireTime             = '';
	private $dayToUpdate            = '';

	public function __construct()
	{
		$this->projectKey = get_option(WPREBUG_PROJECT_KEY_DB);
		$this->scriptUrl = get_option(WPREBUG_URL_SCRIPT_DB);
		$this->expireTime = get_option(WPREBUG_LAST_UPDATE_SCRIPT_DB);
		$this->dayToUpdate = get_option(WPREBUG_DAY_TO_UPDATE_SCRIPT_DB);

		if ($this->dayToUpdate === '0') {
		    $this->dayToUpdate = WPREBUG_DAY_TO_UPDATE_SCRIPT;
        }

        $this->dayToUpdate = '+' . $this->dayToUpdate . ' day';

        $this->setExpireTime();
	}

	public function getScript()
	{
		if ($this->projectKey === '') {
			return false;
		}

		$scriptCode = get_option(WPREBUG_SCRIPT_CONTENT);

		if ($this->isUpdateScript() || $scriptCode === '') {
		    $scriptCode = file_get_contents($this->scriptUrl);

		    if (!$scriptCode) {
		        return false;
            }

            $this->expireTime->modify($this->dayToUpdate);
            update_option(WPREBUG_SCRIPT_CONTENT, $scriptCode);
            update_option(WPREBUG_LAST_UPDATE_SCRIPT_DB, $this->expireTime->getTimestamp());
        }

		$scriptCode = preg_replace($this->strToReplaceProjectKey, $this->projectKey, $scriptCode);

		return $scriptCode;
	}

	public function isUpdateScript()
	{
		$now = new DateTime('now');

		if ($now->format('Y-m-d') >= $this->expireTime->format('Y-m-d')) {
			return true;
		}

		return false;
	}

	public function setExpireTime()
	{
	    $date = new \DateTime();

	    if ($this->expireTime === '') {
	        $date->setTimestamp(time());
        } else {
	        $date->setTimestamp($this->expireTime);
        }

		$this->expireTime = $date;
	}
}
