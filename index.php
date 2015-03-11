<?php
  function relativeTime($time) {

    //$d[0] = array(1,"second");
    $d[1] = array(60,"minute");
    $d[2] = array(3600,"hour");
    $d[3] = array(86400,"day");
    $d[4] = array(604800,"week");
    $d[5] = array(2592000,"month");
    $d[6] = array(31104000,"year");

    $w = array();

    $return = "";
    $now = time();
    $diff = ($now-$time);
    $secondsLeft = $diff;

    for($i=6;$i>-1;$i--)
    {
         $w[$i] = intval($secondsLeft/$d[$i][0]);
         $secondsLeft -= ($w[$i]*$d[$i][0]);
         if($w[$i]!=0)
         {
            $return.= abs($w[$i]) . " " . $d[$i][1] . (($w[$i]>1)?'s':'') ." ";
         }

    }

    $return .= ($diff>0)?"ago":"left";
    return $return;
}


  ini_set('memory_limit','-1');
  $error = NULL;
  
  /*
  if (isset($_GET['method']) && isset($_GET['url'])) {
    $method = $_GET['method'];
    $url    = $_GET['url'];
    
    if (isset($_GET['desc'])) {$description = $_GET['desc'];}
    
    $url_content = json_encode(json_decode(file_get_contents($url), TRUE), JSON_PRETTY_PRINT);;
   
  } else {
     $error = "Must pass method and url";
  } 
  */
  
  $host = "https://fedapi.io/";
  $catalog = $_GET['c'];
  $resource = $_GET['r'];
  $type = $_GET['t'];
  $id = $_GET['id'];
  
  $url = $host."/api/catalog/".$catalog."/".$resource."/".$type."/record/".$id."?event_history=true";

  $record = file_get_contents($url);
  $record_array = json_decode($record, TRUE);
  
  $catalog_definition = json_decode(file_get_contents($host."/api/catalogdefinition/".$catalog), TRUE);
  
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>540.co // FedAPI Record Viewer</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <style type="text/css" media="screen">
    #editor { 
        position: absolute;
        top: 70px;
        right: 0;
        bottom: 0;
        left: 0;
    }
    </style>
    
      
    <?php
    foreach ($record_array['event_history']['event'] as $event) {
    ?>
      <style type="text/css" media="screen">
      #historyevent<?php echo $event['id'];?> { 
          position: relative;
          height:350px;
      }
      </style>
    <?php
    }
    ?>
 
  </head>
  
  <body>
 
     <?php if ($error != NULL) {
       echo "<div class='alert alert-success' role='alert'>$error</div>";
     }  else {
       
     
     ?>
     <div class='row'  style=''>
      <style>.ace-monokai {background-color: #222222; color: #F8F8F2}</style>
      
      <div id="editor" class='col-md-8'><?php echo json_encode($record_array, JSON_PRETTY_PRINT); ?></div>
       
      <div class='col-md-8'>
        <a href='<?php echo $host."www/overview/".$resource; ?>' target='_blank'><img class='pull-left' src='<?php echo $catalog_definition['image'];?>' style='width:50px; margin-top:8px; margin-left:5px; margin-right:10px'/></a>
        <div style='margin-left:10px'>
            <h4><span class='label label-success'><?php echo $host; ?></span> <?php echo $_GET['r']; ?> / <?php echo $_GET['t']; ?> / <?php echo $_GET['c']; ?></h4><p><?php echo $id; ?> <small class='badge'>version <?php echo $record_array['version'];?></small></p>
            <p></p>
        </div>
      </div>
       
       <div class='col-md-4  style=''>
         <a href='<?php echo $host; ?>' target='_blank'><img class='pull-right' src='https://s3.amazonaws.com/fedapi_io/global/fedapi_logo_black_100x100.png' style='width:50px; margin-top:3px; margin-right:3px'/></a>
         <div style='margin-top:80px'>
           <h4>Version History <small><?php echo $record_array['event_history']['event_total_count']; ?> events</small></h4>
           
           <?php foreach ($record_array['event_history']['event'] as $event) {
             ?>
              <div class='id' class='pull-right' style='padding-right:20px'>
              <hr />
              <span class='badge pull-right'><?php echo $event['event']['type']; ?></span><br /><small class='pull-right' style='padding-right:10px'><?php echo relativeTime($event['meta']['timestamp']); ?></small>
              <br /><div id="historyevent<?php echo $event['id'];?>" style="position:relative"><?php echo json_encode($event, JSON_PRETTY_PRINT); ?></div>
              </div>
             <?php
           }
           ?>
         </div>
       </div>
  
     <?php
      }
     ?>
    </div>
 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.1.8/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.1.8/theme-monokai.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.1.8/theme-chrome.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.1.8/mode-json.js"></script>

    <script>
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/monokai");
    editor.setReadOnly(true);
    editor.getSession().setMode("ace/mode/json");
    editor.setShowPrintMargin(false);
    </script>
    
    <?php
    foreach ($record_array['event_history']['event'] as $event) {
    ?>
    <script>
    var editor = ace.edit("historyevent<?php echo $event['id'];?>");
    editor.setTheme("ace/theme/chrome");
    editor.setReadOnly(true);
    editor.getSession().setMode("ace/mode/json");
    editor.setShowPrintMargin(false);
    </script>
    
    <?php
    }
    ?>
    
    <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-59964793-2', 'auto');
  ga('send', 'pageview');

</script>
  </body>
</html>


