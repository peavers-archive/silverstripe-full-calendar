<?php

/**
 * Created by PhpStorm.
 * User: lucy
 * Date: 4/09/15
 * Time: 10:45 AM
 */
class EventDownload
{

	/**
	 * Gets all possible events from database
	 *
	 * @return mixed
	 */
	public function getEvents()
	{
		return FullCalendarEvent::get();
	}
	//
	/**
	 *
	 * Call this function to download the invite.
	 */
	/**
	 *
	 * Downloaded constant
	 *
	 * @var cost
	 *
	 */
	const DOWNLOADED = 100;
	public function download()
	{
		$_SESSION['calander_invite_downloaded'] = self::DOWNLOADED;
		$generate = $this->_generate();



		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"invite.ics\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . strlen($generate));
		print $generate;
	}

	/**
	 *
	 * Save the invite to a file
	 *
	 * @param string $path
	 * @param string $name
	 * @return \Invite
	 *
	 */
	public function save($path = null, $name = null)
	{
		if (null === $path) {
			$path = $this->_savePath;
		}

		if (null === $name) {
			$name = $this->getUID() . '.ics';
		}

		// create path if it doesn't exist
		if (!is_dir($path)) {
			try {
				mkdir($path, 0777, TRUE);
			} catch (Exception $e) {
				die('Unabled to create save path.');
			}
		}

		if (($data = $this->getInviteContent()) == TRUE) {
			try {
				$handler = fopen($path . $name, 'w+');
				$f = fwrite($handler, $data);
				fclose($handler);

				// saving the save name
				$_SESSION['savepath'] = $path . $name;
			} catch (Exception $e) {
				die('Unabled to write invite to file.');
			}
		}

		return $this;
	}

	/**
	 * Get the saved invite path
	 * @return string|boolean
	 */
	public static function getSavedPath()
	{
		if (isset($_SESSION['savepath'])) {
			return $_SESSION['savepath'];
		}

		return false;
	}

	/**
	 *
	 * Check to see if the invite has been downloaded or not
	 *
	 * @return boolean
	 *
	 */
	public static function isDownloaded()
	{
		if ($_SESSION['calander_invite_downloaded'] == self::DOWNLOADED) {
			return true;
		}

		return false;
	}



	/**
	 *
	 * Get the content of for and invite. Returns false if the invite
	 * was unable to be generated.
	 * @return string|boolean
	 */
	public function getInviteContent()
	{
		if (!$this->_generated) {

				if ($this->_generate()) {
					return $this->_generated;
				}


		}

		return $this->_generated;
	}

	/**
	 *
	 * Generate the content for the invite.
	 *
	 * @return \Invite
	 *
	 */
	public function generate()
	{
		$this->_generate();
		return $this;
	}

	/**
	 *
	 * The function generates the actual content of the ICS
	 * file and returns it.
	 *
	 * @return string|bool
	 */
	private function _generate()
	{


			$content = "";
			foreach(FullCalendarEvent::get() as $event) {

				$content = "BEGIN:VCALENDAR\n";
				$content .= "VERSION:2.0\n";
				$content .= "CALSCALE:GREGORIAN\n";
				$content .= "METHOD:REQUEST\n";
				$content .= "BEGIN:VEVENT\n";
				$content .= "DTSTART:{$event->StartDate}\n";
				$content .= "DTEND:{$event->EndDate}\n";
				$content .= "DESCRIPTION:{$event->ShortDescription}\n";
				$content .= "SEQUENCE:0\n";
				$content .= "STATUS:NEEDS-ACTION\n";
				$content .= "TRANSP:OPAQUE\n";
				$content .= "END:VEVENT\n";
				$content .= "END:VCALENDAR";

			}

		var_dump($content);
		die;

			return $content;



	}


}
