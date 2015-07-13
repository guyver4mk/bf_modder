<!-- script references -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/scripts.js"></script>
<!-- Add fancyBox main JS and CSS files -->
<script type="text/javascript" src="js/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.fancybox.css?v=2.1.5" media="screen" />
<?php 
  require $cmtx_path . 'includes/commentics.php'; //don't edit this line
echo '</div>';
?>
<script>
  function postAPK()
  {
    console.log('in postAPK');

      var postData = $('#bf_modder').serializeArray();

      console.log(postData);

      $.ajax({
          type        : "POST",
          cache       : false,
          data        : postData,
          url         : "cron/apk_cron_job.php",
          dataType    : "HTML",
              success: function (htmlStr) {
                  console.log(htmlStr);

          }
      });
      
  }

  function checkBrowser()
  {
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) )
    {
      $('#bmab_out').addClass('bmab-mob');
      console.log($('#bmab_out'));
    }
    else
    {
      $('#bmab_out').addClass('bmab'); 
      console.log($('#bmab_out'));
    }
  }

  function apkStatus()
  {

    console.log($('#apk_status').val());
    var status = $('#apk_status').val();

    if(status=='ready' || 'processing')
    {
      $('#apk_outputted').val($('#apk_status').val());
    }
    else
    {
      $('#apk_outputted').val($('#apk_outputted_f').val());
    }

      if(typeof $('#apk_out') != 'undefined' && $('#apk_out').val() == 'yes' )
      {
        if(typeof $('#apk_status') != 'undefined' && $('#apk_status').val() != 'done')
        {
          var link = $('#apk_status').val();
          $('#link_output').html(link);          
        }
        else
        {
          var link = '<a href="' + $('#apk_outputted').val() + '">'+$('#apk_name').val()+'</a>';
          $('#link_output').html(link);           
          
        }
      }    
  }

  function IsEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
  }

    $(document).ready(function() {
      checkBrowser();


      $('#poll_other_txt').hide('fast');

      $('input[name=poll]').on('change',function() {
        if($('#poll_other').is(':checked')) 
        { 
          $('#poll_other_txt').show('slow');
        }
        else
        {
          $('#poll_other_txt').hide('slow');
        }
      });


       $(".fancybox-thumb").fancybox({
			helpers	: {
				title	: {
					type: 'inside'
            },
			overlay : {
				css : {
					'background' : 'rgba(1,1,1,0.65)'
				}
            }
          }
      });
    

       $('#popover').popover();

       $('#apk_status').on('change', function(e){
        if($('#apk_status').val()=='done')
        {
          var link = '<a href="' + $('#apk_outputted').val() + '">'+$('#apk_name').val()+'</a>';
          $('#link_output').html(link);      
        }
       });

      if(typeof $('#lib_out') != 'undefined' && $('#lib_out').val() == 'yes' )
      {
      	var link = '<a href="' + $('#lib_outputted').val() + '" style=\"color:red !important\">'+$('#lib_name').val()+'</a>';
        $('#link_output').html(link);          	
      }


      $('#create_file').click(function(e){
      		e.preventDefault();
      		if($('#user').val()=="")
      		{
      			alert('Please Enter an Email Address to create your file');
      		}
      		else if($('#output').val()=="no_sel")
      		{
      			alert('Please select your preferred file type');
      		}
          else if($('#output').val()=="apk")
          {
            var email = IsEmail($('#user').val());

            if(email==false)
            {
              alert('Please enter a valid email address for APK Creation');
            }
            else
            {
              $('#bf_modder').attr('action', 'bf_modder.php').submit();
            }

          }
      		else
      		{
      			$('#bf_modder').attr('action', 'bf_modder.php').submit();
      		}
      });

      $('img[alt="Commentics"]').hide('fast');
  });
</script>
</body>
<html>
