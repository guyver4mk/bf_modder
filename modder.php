<?php
session_start();
include('lib/classes/bfLookup.php');
include_once('lib/classes/DB.php');
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

  $bfLookup->setFile($file);


  if(isset($_POST['output'])&&$_POST['output']=='libgame')
  {
    $ret_type = 'libgame';
  }
  elseif(isset($_POST['output'])&&$_POST['output']=='apk')
  {
    $ret_type = 'apk';
    $user = $f[0];
    $email = $_POST['user'];

    $sql = "INSERT INTO apk_cron(file_name, user_name, email, status) VALUES(:file, :user, :email, 'ready')";
      
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
            array(
              'name' => ':email',
              'value' => $email,
              'type' => 'string'
            )
        );

    $r = $db->apply($sql, true, $params);


    define('APK_ID', intval($r));


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


  if(isset($ret_type)&&$ret_type=='libgame')
  {
    echo '<input type="hidden" id="lib_out" value="yes">';
    echo '<input type="hidden" id="lib_name" value="'.$file.'.so">';
    echo '<input type="hidden" id="lib_outputted" value="shells/'.$file.'.so">';
  }
  elseif(isset($ret_type)&&$ret_type=='apk')
  {
    header("Location:http://www.guyver4.co.uk/bf_modder.php?apk=waiting");
  }
}


/*
  THANKS AND MENTIONS
*/

$thanks =  '
    <div id="bmab_out">
        <center><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QGRG8YUDP44ZU" target="_blank"><img src="http://www.guyver4.co.uk/img/buymeabeer.png" border="0" id="bmab"></a><br />
      This service is free, but please feel free to buy me a beer ;)<br />
      Thank You.<br />
      (P.S. I\'m always thirsty!)
      </form>
    </div>
    ';
  

//===========================



  include('includes/header.php');

if(isset($_GET['donation'])&&$_GET['donation']=='accepted')
{
  echo '<center><div class="alert alert-success alert-dismissible" style="width:50% !important"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Thank you for your generosity and chivalry in these dark times.<br />Please accept <a href="APKS/infinite_zel.bravefrontier.apk">THIS</a> as a thank you.<br />Just install it, play any quest, and sell the dropped items :)</div></center>';
}


$version = $bfLookup->getVersion();

?>

<style type="text/css">
.bmab {
  z-index: 100;
  display: block;
  color: #ffffff !important;
  width:280px;
  position: fixed;
  top: 20px;
  right: 10px;
  text-align:center;
}

.bmab-mob {
  display: block;
  color: #ffffff !important;
  width:280px;
  text-align:center;
}
</style>


  <!-- top navbar -->
<!--    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="navbar-header">
           <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target=".sidebar-nav">
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
           </button>
           <a class="navbar-brand" href="#">Brave Frontier Online Modder</a>
      </div>
    </nav>
-->      
    <div class="container-fluid">
        <div class="row row-offcanvas row-offcanvas-left">
        
        <!--sidebar-->
        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">

        </div><!--/sidebar-->
    
        <!--/main-->
        <div class="col-xs-12 col-sm-9" data-spy="scroll" data-target="#sidebar-nav">
          <div class="row">
             <div class="col-sm-6">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                  <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                      <h4 class="panel-title">
                        <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseTwo">
                          Updates - Click to expand 
                        </a>
                        <span style="float:right"> 27th January 2015</span>
                      </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                      <div class="panel-body">
                        Hello All!<br />

                        And we're back! With all of the mods needed to not only get your hands on Cardes, but also continue on your quest through the grand gaia. ALL credit for the new mods is given to <a href="http://elementevil.com/forum/members/jx1002.html">jx1002 @ ee.com</a> for finding the offsets for v1.2.4.<br /><br />

                        More mods will be coming soon, but there are enough to keep you going for now.<br /><br />

                        As always, all feedback would be greatly appreciated. Just add a reply to <a href="http://androidgamehacks.net/forums/showthread.php?tid=32925&action=lastpost" target="_blank">THIS</a> Thread on the AGH.net forum, or leave a comment below, and I will pick up any issues as soon as I can!
                      </div>
                    </div>
                  </div>
                </div>
<?php
  $form = '
          <div class="panel panel-default">
            <div class="panel-heading"><h4>Choose your Mods</h4></div>
             <div class="panel-body">
              <center>
                <img src="images/bflogo.png" style="width:300px; height: 150px"/><br />
                Game Version '.$version['version'].'<br />
              <form class="form-horizontal top-padded" role="form" id="bf_modder" name="bf_modder" method="POST">
                <select name="output" id="output" style="font-size: 14px">
                    <option value="no_sel">Please select output type</option>
                    <option value="libgame">libgame.so</option>
                    <!--<option value="apk">APK</option>-->
                  </select>
                <div id="link_output"></div>
                </center>
                <div class="form-group">
                '.$bfLookup->getMods('global').'
                </div>
              </form>
              <!-- End of Form -->  
          </div><!--/panel-body-->';


                    $maintenance = 1;

                    if($maintenance == 1)
                    {
                      echo '<center style="color: white !important;"><h2>MAINTENANCE!</h2><br /> Due to issues with Red Skulls being reported, I am taking the modder offline for a short while I troubleshoot the issues.<br /> 
                            In the meantime, here are a couple of links to keep you going.<br /><br />
                            <a href="files/libgame.so"> libgame.so File - No Damage & 0 Energy Mods<br />
                            <a href="files/guyver4mk_BF_1.2.4.2_mod.apk"> APK File - No Damage & 0 Energy Mods (ONLY USE IF YOU DONT MIND HAVING THE REDSKULL!)<br /><br />
                            Please bear with me and check back soon for Updates.</center>';
                    }
                    else
                    {
                      echo $form;  
                    }

                ?>
                </div><!--/panel-->

            </div><!--/col-->
      <div class="row row-offcanvas row-offcanvas-right">

      </div><!--/row-->
          
      </div><!--/.row-->
    </div>
  </div><!--/.container-->
</div><!--/.page-container-->
<!--sidebar-->



        
<?php

      echo $thanks;

echo '<div style="height:40px">&nbsp;</div>';
echo '<div class="col-md-offset-4 col-md-10 row-col ">';
$cmtx_identifier = '1';
$cmtx_reference = 'Page One';
$cmtx_path = 'comments/';



include('includes/footer.php');

