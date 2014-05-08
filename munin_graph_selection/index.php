<?php
$display_server = "";
$display_monitor = "";
$display_monitor_pulldown = "";

// Set Select Server function
function GetServers($domain){
  $cmd =  "cat ../index.html | grep host | grep " . $domain . " | cut -d\> -f 4 | cut -d\< -f1";
  exec ( $cmd , $server_list , $ret );
  return $server_list;
}

// Set Server
exec ( 'cat ../index.html | grep -e domain | cut -d\> -f 4 | cut -d\< -f 1', $domain_list , $ret );
foreach ($domain_list as $domain) {
  $display_server = $display_server . "<div class='domain-box'>\n";
  $display_server = $display_server . "<h3>" . $domain . "</h3>\n";
  $servers = GetServers($domain);
  $display_server = $display_server . "<select name='" . $domain ."_SelectServer[]' size='10' multiple>";
  foreach($servers as $server) {
    $display_server = $display_server. "<option value='" . $server ."'>". $server."</option>";
  }
  $display_server = $display_server . "</select>\n";
  $display_server = $display_server . "</div>\n";
}

// Set Monitor
$monitor_list = array('cpu', 'df', 'memory', 'mysql_queries', 'mysql_slowqueries', 'qmailsend', 'apache_accesses');
foreach ($monitor_list as $monitor) { 
  $display_monitor = $display_monitor . "<input type='checkbox' name='SelectMonitor[]' value=" . $monitor . "> " . $monitor . " </br> ";
}

// Set pulldown list
exec ( 'find ../ -name "*.png" | cut -d\/ -f 4 | cut -d- -f1 | sort -u', $select_monitor_list , $ret );
foreach ($select_monitor_list as $selet_monitor){
  $display_monitor_pulldown = $display_monitor_pulldown . "<option value='" . $selet_monitor . "'>" . $selet_monitor . "</option>";
}

// Set HTML
$html =<<<EOD
<html>
  <head>
  <link rel="stylesheet" type="text/css" href="style.css">

<script type="text/javascript">
  function fchk(obj, target){
    var frm=document.form1;
    if(!obj.checked){
      /* 入力値をクリア */
      frm.elements[target].value="";
      /* チェックされたら、テキストボックスを有効化 */
      frm.elements[target].disabled=true;
    }else{
      /* チェックが外されたら、テキストボックスを無効化 */
      frm.elements[target].disabled=false;
    }
  }
</script>

  </head>
  <body>
    <h1>munin グラフ選択</h1>
    <form method="get" action="result.php" name="form1">
      <h2>Select Domain</h2>
      {$display_server}
      <h2 class='clear-fix'>Select Monitors</h2>
      {$display_monitor}
      <input type="checkbox" onclick="fchk(this,'SelectMonitorPulldown');"><select name="SelectMonitorPulldown" disabled="disabled">
      {$display_monitor_pulldown}
      </select></br>
      <input type="checkbox" onclick="fchk(this,'SelectMonitorText');"><input type="text" name="SelectMonitorText" disabled="disabled"> : free text
      <h2>Select Term</h2>
      <input type="checkbox" name="SelectTerm[]" value="day"> day </br>
      <input type="checkbox" name="SelectTerm[]" value="week"> week </br>
      <input type="checkbox" name="SelectTerm[]" value="month"> month </br>
      <input type="checkbox" name="SelectTerm[]" value="year"> year </br>
      <h2>option</h2>
      <p>画像の大きさ(パーセンテージ) <input type="text" name="ImageSize" value="100">% </p>
      <input type="checkbox" name="ImageHorizon" value="true" checked>画像の横並び
      <h2>Submit</h2>
      <p><input type="submit" value="作成"></p>
    </form>
  </body>
</html>
EOD
;

// Output result
echo $html;

?>
