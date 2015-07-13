<?php

include_once(realpath(dirname(__DIR__)).'/lib/classes/DB.php');
include_once(realpath(dirname(__DIR__)).'/lib/classes/bfLookup.php');
include_once(realpath(dirname(__DIR__)).'/lib/classes/advEmail.php');

$bfLookup = new bfLookup();
$db = new DB();

/*
	APK CRON JOB
*/

$create = $bfLookup->checkProcessing();

if($create > 0)
{
	echo "Processing";
}
else
{
	$process_apk = true;
}	

/*
	Check if file is processing
*/

	if($process_apk == true)
	{


		$sql = "SELECT * FROM apk_cron WHERE status NOT IN('done', 'processing') ORDER BY id asc LIMIT 1";

		$r = $db->query($sql);


		if(empty($r))
		{
			echo "Nothing in Queue";
			exit;
		}
		else
		{
			$row = $r[0];
			$sql = "UPDATE apk_cron SET status='processing' WHERE id = ".$row['id'];
			$db->apply($sql);



			shell_exec("sh /var/www/html/create_apk.sh ".$row['file_name']." " . $row['user_name']);


			$sql = "INSERT INTO apk_done(apk_link,file_name, user_name, email, cron_id) VALUES('".$row['user_name'].".gumi.bravefrontier_1.2.4.2_11014020.apk','".$row['file_name']."', '".$row['user_name']."', '".$row['email']."', '".$row['id']."')";
			$db->apply($sql);

			$sql = "UPDATE apk_cron SET status='done' WHERE id = ".$row['id'];
			$db->apply($sql);
			
			$sql = "SELECT * FROM apk_done WHERE id = ". $row['id'];
			$r = $db->query($sql);

			$res = $r[0];
		 /*
		 	Send APK File
		 */

		 	$message 	= 	"Hello " . $res['user_name'] . "! <br />";
		 	$message 	.=	"Your Modded Brave Frontier APK is now ready for you to download!!<br /><br />";
		 	$message 	.=	"Please click on the below link to Download your Modded APK.<br /><br />";
		 	$message 	.=	"Please Note that APK files are removed from the server after 3 hours. If you have not downloaded it by then, then you will have to create a new one :(<br /><br />";
		 	$message 	.=	"Thanks Again For Using the guyver4.co.uk Online Brave Frontier Modder!<br /><br />";
		 	$message 	.=	'<a href="http://www.guyver4.co.uk/apks/'. $res['apk_link'] . '"> ' . $res['user_name'] . '\'s Modded APK</a><br /><br />';
		 	$message 	.=	"Regards,<br /><br />";
		 	$message 	.=	'Guyver4mk<br /> <br /> <img src="http://www.guyver4.co.uk/img/guyver4.jpeg" height="240" width="220" />';

			$advEmail = new advEmail();
			
			$advEmail->setMailType('html');
			
			$advEmail->from('noreply@guyver4.co.uk', 'Guyver4mk');
			
			$advEmail->to($res['email']);
			//$advEmail->cc('matt@mattclements.co.uk');
			//$advEmail->bcc('matt@mattclements.co.uk');
			
			$advEmail->subject('Your Modded Brave Frontier APK');
			
			$advEmail->message($message);
			//$advEmail->set_alt_message('Text alternative email - manually set');
			
			//$advEmail->attach($apk['apk_outputted']);
			//$advEmail->attach('/home/matt/file2.zip');


			if (!$advEmail->send())
			{
			  $errors = $advEmail->getDebugger();
			  print_r($errors);
			}	

			if(isset($_SERVER['SHELL'])&& $_SERVER['SHELL']=='/bin/bash')
			{
				echo "done";
			}
			else
			{
				header('Location:http://www.guyver4.co.uk/bf_modder.php?apk=done');
			}

		}


	}
	else
	{
		echo "Processing";
	}

