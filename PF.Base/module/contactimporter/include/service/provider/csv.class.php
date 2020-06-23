<?php

/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development
 * @package          Module_Contactimporter
 * @version          2.06
 *
 */
defined('PHPFOX') or exit('NO DICE!');

require_once 'email_abstract.class.php';
require_once dirname(dirname(__FILE__)) . '/libs/VcardReader.php';

class Contactimporter_Service_Provider_CSV extends Contactimporter_Service_Provider_Email_Abstract
{

	protected $_name = 'csv';

	function endsWith($FullStr, $EndStr)
	{
		// Get the length of the end string
		$StrLen = strlen($EndStr);
		// Look at the end of FullStr for the substring the size of EndStr
		$FullStrEnd = substr($FullStr, strlen($FullStr) - $StrLen);
		// If it matches, it does end with EndStr
		return $FullStrEnd == $EndStr;
	}

	function isSupportedFileType($filetype)
	{

		// list the permitted file type
		$allowFileTypes = array(
			'text/csv' => 'csv',
			'text/x-csv' => 'csv',
			'text/comma-separated-values' => 'csv',
			'application/csv' => 'csv',
			'application/excel' => 'csv',
			'application/vnd.ms-excel' => 'csv',
			'application/vnd.msexcel' => 'csv',
			'text/anytext' => 'csv',
			'text/x-vcard' => 'vcf',
			'application/vcard' => 'vcf',
			'text/anytext' => 'vcf',
			'text/directory' => 'vcf',
			'text/x-vcalendar' => 'vcf',
			'application/x-versit' => 'vcf',
			'text/x-versit' => 'vcf',
			'application/octet-stream' => 'ldif',
		);

		return array_key_exists($filetype, $allowFileTypes);
	}

	public function processUploadFile()
	{
		$aContacts = $aJoineds = $aInvalids = $aErrors = array();
		$friends = array();
		$is_error = 0;
		$message = '';
		$ci_contacts = array();

		for (; ; )
		{
			$uploaded_file = $_FILES['csvfile']['tmp_name'];
			$filetype = $_FILES['csvfile']["type"];

			$filename = $_FILES['csvfile']['name'];

			if ($this -> isSupportedFileType($filetype) == FALSE)
			{
				$is_error = 1;
				$message = _p('invalid_file_type');
				break;
			}

			// Check file types

			if (is_uploaded_file($uploaded_file))
			{
				$fh = fopen($uploaded_file, "r");
				if ($this -> EndsWith(mb_strtolower($filename), 'csv'))
				{
					// Process CSV file type
					$i = 0;
					$row = fgetcsv($fh, 1024, ',');
					$first_name_pos = -1;
					$email_pos = -1;
					$first_display_name = -1;
					$count = count($row);

					for ($i = 0; $i < $count; $i = $i + 1)
					{
						if ($row[$i] == "E-mail Display Name" || $row[$i] == "First" || $row[$i] == "First Name")
						{
							$first_name_pos = $i;
						}
						elseif ($row[$i] == "E-mail Address" || $row[$i] == "Email" || $row[$i] == "E-mail Address")
						{
							$email_pos = $i;
						}
						elseif ($row[$i] == "First Name" || $row[$i] == "First")//yahoo format oulook
						{
							$first_display_name = $i;
						}
						else
						{
							// do nothing
						}
					}

					if (($email_pos == -1) || ($first_name_pos == -1))
					{
						$is_error = 1;
						$message = _p('invalid_file_format');
						break;
					}
					else
					{
						if ($first_display_name == -1)
							$first_display_name = $first_name_pos;
					}

					while (($row = fgetcsv($fh, 1024, ',')) != false)
					{
						if (isset($row[$email_pos]) && !empty($row[$email_pos]))
						{
							$contacts[] = array(
								'email' => $row[$email_pos],
								'name' => empty($row[$first_name_pos]) ? $row[$first_display_name] : $row[$first_name_pos]
							);
						}
					}
					fclose($fh);
				}
				elseif ($this -> EndsWith(mb_strtolower($filename), 'vcf'))
				{
					// Process VCF file type
					$file_size = filesize($uploaded_file);
					if ($file_size == 0)
					{
						$is_error = 1;
						$message = _p('empty_file');
						break;
					}

					$vcf = fread($fh, filesize($uploaded_file));
					fclose($fh);
					$vCard = new VCardTokenizer($vcf);

					$contacts = array();
					$result = $vCard -> next();
					$contact = array();

					while ($result)
					{
						if (mb_strtolower($result -> name) == 'email')
						{
							$contact['email'] = $result -> getStringValue();
						}
						elseif (mb_strtolower($result -> name) == 'n')
						{

							$name = $result -> getStringValue();
							$parts = explode(";", $name, 2);
							if ($parts[1] == '')
							{
								$contact['first_name'] = $parts[0];
								$contact['name'] = $contact['first_name'];
							}
							else
							{
								$contact['last_name'] = $parts[0];
								$contact['first_name'] = $parts[1];

								$contact['name'] = $contact['first_name'] . ' ' . $contact['last_name'];
							}
						}
						else
						if (mb_strtolower($result -> name) == 'org')
						{
							$contact['company'] = $result -> getStringValue();
						}
						elseif (mb_strtolower($result -> name) == 'title')
						{
							$contact['position'] = $result -> getStringValue();
						}
						$result = $vCard -> next();
					}

					if ((!isset($contact['email'])) || (!isset($contact['name'])))
					{
						$is_error = 1;
						$message = _p('invalid_file_format');
						break;
					}

					if (isset($contact['email']))
					{
						if ($this -> validateEmail($contact['email']))
						{
							$contacts[] = array(
								'email' => $contact['email'],
								'name' => $contact['name']
							);
						}
						else
						{
							$is_error = 0;
							$message = _p('there_s_some_error_in_your_contact_file');
						}
					}
				}
				elseif ($this -> EndsWith(mb_strtolower($filename), 'ldif'))//thunderbirth
				{
					$thunder_data = fread($fh, filesize($uploaded_file));
					$rows = explode(PHP_EOL, $thunder_data);
					$name = "";
					$email = "";
					$contacts = array();

					foreach ($rows as $index => $row)
					{
						try
						{
							@list($key, $data) = @explode(':', $row);
							
							if ($key == 'cn'){
								$name = trim($data);
							}
							
							if ($key == 'mail'){
								$email = trim($data);
							}

							if ($email != "")
							{
								$contacts[] = array(
									'email' => $email,
									'name' => $name
								);
								$name = $email = "";
							}
							
						}
						catch (Exception $ex)
						{

						}
					}
				}
				else
				{
					$is_error = 1;
					$message = _p('unknown_file_type');
				}
			}

			if (empty($contacts))
			{
				$is_error = 1;
				$message = _p('there_is_no_contact_in_your_address_book');
				break;
			}

			foreach ($contacts as $value)
			{
				$ci_contacts["{$value["email"]}"] = $value["name"];
			}
			break;
		}
		$aErrors = array();
		if($is_error == 1)
		{
			$aErrors['contacts'] = $message;
		}

		$iCnt = count($ci_contacts);
		$aContacts = Phpfox::getService('contactimporter') -> processEmailRows($contacts);

		return array(
			'iCnt' => $iCnt,
			'aInviteLists' => $aContacts,
			'aErrors' => $aErrors,
			'aJoineds' => $aJoineds,
			'aInvalids' => $aInvalids,
		);


	}

	public function getContacts($iPage = 1, $iLimit = 50)
	{
		return $this -> processUploadFile();
	}

}
