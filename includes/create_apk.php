<?php 

include_once(realpath(dirname(__DIR__)).'/lib/classes/DB.php');
include_once(realpath(dirname(__DIR__)).'/lib/classes/bfLookup.php');
include_once(realpath(dirname(__DIR__)).'/lib/classes/advEmail.php');
session_start();
$bfLookup = new bfLookup();
$db = new DB();

if(isset($_POST) && !empty($_POST))
{

	$post = array();
	foreach($_POST as $key => $val)
	{
		$post[$key] = $val;
	}

	$f = explode('@', $_POST['user']);
	$file = $f[0].'.libgame';
	$user = $f[0];
	$email = $_POST['user'];

	$sql = "INSERT INTO apk_cron(file_name, user_name, status) VALUES(:file, :user, 'ready')";
		
	$file_name = $file.".so";

	$params = array(
					array(
						'name' => ':file',
						'value' => $file_name,
						'type' => 'string'
					),
					array(
						'name' => ':user',
						'value' => $f[0],
						'type' => 'string'
					),
			);

	$r = $db->apply($sql, $params, true);

	$_SESSION['apk_id'] = intval($r);

	$bfLookup->setFile($file);


	if(isset($_POST['output'])&&$_POST['output']=='libgame')
	{
		$ret_type = 'libgame';
	}
	elseif(isset($_POST['output'])&&$_POST['output']=='apk')
	{
		$ret_type = 'apk';
	}
	else
	{
		echo '<div class="alert alert-block alert-error">
		            <h4>Output Error:</h4>
		            Please select an output type. 
		        </div>';
	}

	unset($_POST['user']);
	unset($_POST['output']);
	unset($post['user']);
	unset($post['output']);

	if(isset($_POST['sphere'])&&$_POST['sphere']=='no_sel')
	{
		unset($_POST['sphere']);
	}
	if(isset($post['sphere'])&&$post['sphere']=='no_sel')
	{
		unset($post['sphere']);
	}

 	$output = "#!/bin/bash \n";
  


	foreach($post as $key => $val)
	{
		$output .= $bfLookup->getString($key, $val) . "\n";
	}


	file_put_contents("/var/www/html/shells/".$file.'.sh', $output);
	shell_exec('cp /var/www/html/libgame.so /var/www/html/shells/'.$file.'.so');

	chmod("/var/www/html/shells/". $file .".so", 0777);
	chmod("/var/www/html/shells/". $file .".sh", 0777);

	shell_exec("sh /var/www/html/shells/". $file .".sh");

	$sql = "SELECT status FROM apk_cron WHERE id = '".$_SESSION['apk_id']."'";
	$s = $db->query($sql);
	$status = $s[0];

	$create = $bfLookup->checkProcessing();


	while($create >0)
	{
		$create = $bfLookup->checkProcessing();
		sleep(5);
	}

	$apk = $bfLookup->processApk($status, $file);
	
 /*
 	Send APK File
 */

 	$message 	= 	"Hello " . $user . "! <br />";
 	$message 	.=	"Your Modded Brave Frontier APK is now ready for you to download!!<br /><br />";
 	$message 	.=	"Please click on the below link to Download your Modded APK.<br /><br />";
 	$message 	.=	"Please Note that APK files are removed from the server after 3 hours. If you have not downloaded it by then, then you will have to create a new one :(<br /><br />";
 	$message 	.=	"Thanks Again For Using the guyver4.co.uk Online Brave Frontier Modder!<br /><br />";
 	$message 	.=	'<a href="'. $apk['apk_outputted'] . '"> ' . $user . '\'s Modded APK</a><br /><br />';
 	$message 	.=	"Regards,<br /><br />";
 	$message 	.=	'Guyver4mk<br /> <br /> <img src="http://www.guyver4.co.uk/img/guyver4.jpeg" height="240" width="220" />';

	$advEmail = new advEmail();
	
	$advEmail->setMailType('html');
	
	$advEmail->from('noreply@guyver4.co.uk', 'Guyver4mk');
	
	$advEmail->to($email);
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

}
