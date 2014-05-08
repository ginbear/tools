<?php
// 変数セット
exec ( 'cat ../index.html | grep -e domain | cut -d\> -f 4 | cut -d\< -f 1', $DomainList, $ret );
$result = "";
$GetName = "";
$MuninPath = "http://hoge.fuga.com/munin";
$SelectMonitor = $_GET['SelectMonitor'];
$SelectTerm = $_GET{'SelectTerm'};
$ImageSize = $_GET{'ImageSize'};
$ImageHorizon = $_GET{'ImageHorizon'};
$ServerNum = 0;
$OddCheck = true;
$SelectMonitorText = (isset($_GET['SelectMonitorText'])) ? $_GET['SelectMonitorText']: null;
$SelectMonitorPulldown = (isset($_GET['SelectMonitorPulldown'])) ? $_GET['SelectMonitorPulldown']: null;

if (!$SelectMonitor) $SelectMonitor = array();
if ($SelectMonitorPulldown) array_push($SelectMonitor, $SelectMonitorPulldown);
if ($SelectMonitorText) array_push($SelectMonitor, $SelectMonitorText);

// Graphs
foreach ($DomainList as $domain){
  $GetName = str_replace(".","_",$domain);
  $GetName = $GetName . "_SelectServer";
  $SelectServer = (isset($_GET[$GetName])) ? $_GET[$GetName]: null;
  if ($SelectServer){
    $servers = $SelectServer;
    $result = $result . "<h2>" . $domain. "</h2>\n";
    foreach ($SelectMonitor as $monitor) {
      $result = $result . "<h3>" . $domain . " > " . $monitor . "</h3>\n";
      foreach ($SelectTerm as $term) {
        $result = $result . "<h4>" . $domain . " > " . $monitor . " > " . $term . "</h4>\n";
        $result = $result . "<div class='contents clear-fix'>";
        foreach ($servers as $server) {
          if ($OddCheck) {
            $result = $result . "<div class='odd graph-box section'>";
            $OddCheck = false;
          } else {
            $result = $result . "<div class='even graph-box section'>";
            $OddCheck = true;
          }
          $result = $result . "<p>" . $server . "</p>";
          $result = $result . "<a href='" $MuninPath . "/" . $domain . "/" . $server . "/" . $monitor . ".html'><img src='../" . $domain . "/" . $server . "/". $monitor . "-" . $term . ".png' style='width:" . $ImageSize . "%'></a>\n";
          $result = $result . "</div>";
        }
        if ($ServerNum < count($servers)) { 
          $ServerNum = count($servers);
        }
        $result = $result . "</div>";
      }
    }
  }
}

// Javascript
if($ImageHorizon){
$javac =<<<EOD
$(function() {
        //コンテンツの横サイズ
        var cont = $('.contents');
        var h2 = $('h2');
        var h3 = $('h3');
        var h4 = $('h4');
        //var contW = $('.section').outerWidth(true) * {$ServerNum};
        var contW = 504 * {$ServerNum};
        cont.css('width', contW);
        h2.css('width', contW);
        h3.css('width', contW);
        h4.css('width', contW);
});
EOD
;
} else {
$javac = "";
}

// html セット
$html =<<<EOD
<html>
  <head>
  <link rel="stylesheet" type="text/css" href="style.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script type="text/javascript">
{$javac}
</script>
  </head>
<body>
<div id='result'>
{$result}
</div>
</body>
</html>
EOD
;

// Output result
echo $html;

?>
