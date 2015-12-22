<?php
// U.S. Department of Commerce Intranet
   // Office of the Secretary - CFO-ASA - OFEQ - Office of Space & Building Management
   // Work Request System (["redacted"])
   //
   // dataRestoreHelper.php
   //
   // 2014-11-28 AJM - Created (originally redacted/when_i_was_testing.php)
/**
 * Don't be a d-bag, write something up here.
 *
 * [here's a start...]
 * So your customer accidentally deleted a workrequest record (and everything that went with it).
 * Fabulous!
 * We can't restore everything, but let's find some workrequest log messages and restore as much as possible.
 *
 * See also:
 *      Haig's instructions for restoring CM/workrequest records:
 *              ["redacted"]
 *      General ["redacted"] info & maintenance doc:
 *              ["redacted"]
 *      Dennis' new doc, intended to be specific research, etc., re: restoring everything associated with workrequest records (?):
 *              ["redacted"]
 */

include_once('common.inc'); ?>

<head>
<title>Queries for everyone! - ["redacted"]</title>
<? if ( $stylesheet ) { ?>
  <link rel="stylesheet" href="<? echo $stylesheet; ?>">
<? }
   if ( $localStylesheet ) { ?>
  <link rel="stylesheet" href="<? echo $localStylesheet; ?>">
<? }
   if ( $stylesheetForPrint ) { ?>
  <link rel="stylesheet" href="<? echo $stylesheetForPrint; ?>" media="print">
<? }
   if ( $jsIncludeDirectly ) {
    if ( is_array( $jsIncludeDirectly ) ) foreach ( $jsIncludeDirectly as $string ) echo $string;
    else echo $jsIncludeDirectly; ?>
<? }
   if ( $jsPhpInclude ) {
    if ( is_array( $jsPhpInclude ) ) foreach ( $jsPhpInclude as $filename ) include_once( $filename );
    else include_once( $jsPhpInclude ); ?>
<? } ?>

<style>
body {
  font-size: 100%;
  line-height:1.3em;
  color: #333;
  margin:0;
  padding:0;
  width:100%;
  height:100%;
}
pre, code {
  display:block;
  padding: 0.75em;
  overflow-x:auto;
  background-color: #f2f2f2;
  border: 1px solid #D9D9D9;
  font-size: 1.1em;
  line-height:1.3em;
}
table {table-layout:fixed;width:100%;;}
table.padMe td {padding:1.25em;}
.wrapMe {text-align:center;width:100%;}
.wrapMe > * {text-align:left; margin:0.75em auto;}
.dbError {
  width:50%;
  padding:0.75em;
  border:red 2px solid;
  background-color:mistyrose;
  color:red;
}
</style>
</head>

<body>
<div style="text-align:center;padding:0.75em 0;font-weight:bold;">For a logout link, and many other nice things, head over to the <a href="index.ph
p">["redacted"] application homepage</a>.</div>

<table cellspacing="0" cellpadding="0" border="0" height="100%" class="padMe"><tbody><tr>
<td class="nonBodyArea">&nbsp;</td>
<td width="94%" valign="top" class="BodyArea">

<?php

// open mysql connection as dbuser wrs, and use db wrs-v3
include_once('redacted/the_name_of_mysql_connect_include.inc'); //probably put this more inside than up here
echo '<p><span style="font-weight:bold;color:red;">FIRST:</span> Please review <a href="['redacted']">these helpful instructions for restoring deleted workrequests (and associated entities)</a>.</p>';
echo '<p><span style="font-weight:bold;color:red;">ALSO:</span> Make sure to follow step #6 in the <a href="['redacted']">aforementioned instructions</a> when running "update" queries on restored entities.</p>
';

$wr_id = $_GET['wr_id'];
$comp_id = $_GET['comp_id'];
$charge_id = $_GET['charge_id'];
$id_is_array = FALSE;
if (isset($wr_id)) { // the parts for exploding comp & charge IDs are ugly, but oh well
  $id = $wr_id;
} elseif (isset($comp_id)) {
  if (strpos($comp_id, ',') === FALSE) {
    $id = $comp_id;
  } else {
    $id = explode(',', $comp_id);
    $comp_id = $id;
    $id_is_array = TRUE;
  }
} elseif (isset($charge_id)) {
  if (strpos($charge_id, ',') === FALSE) {
    $id = $charge_id;
  } else {
    $id = explode(',', $charge_id);
    $charge_id = $id;
    $id_is_array = TRUE;
  }
}
//print $id;
if (isset($id) && !$id_is_array) {
  if (is_numeric($id)) {
    settype($id, 'int');
    if ($id < 100000) {
      echo '<!--Nice *_id!-->';
    } else {
      echo '<p>Error: The provided id is not less than 100000.</p>';
      exit;
    }
  } else {
    echo '<p>Error: The provided id is not numeric.</p>';
    exit;
  }
} elseif (isset($id) && $id_is_array) {
  echo '<!--hmmm should really check out this array...-->';
  //echo '<pre>';
  //var_dump($id);
  //echo '</pre>';
} else {
  echo '<p>FYI, there\'s no *_id param value in your URL (or *_id == 0).</p>';
}
if (isset($id)) {
  echo '<p><strong>Note:</strong> Only the first *_id parameter in your URL will be used.</p>';
  switch ($id) {
    case $wr_id:
      //echo 'wr_id here!<br />';
      $sql = 'SELECT entry FROM log WHERE entry LIKE \'%CD-REDACTED ' . $id . '%\' AND entry NOT LIKE \'%Retrieved%\' AND entry NOT LIKE \'%Delete%\' OR
DER BY timestamp';
      $heading_var = 'wr';
      break;
    case $comp_id:
      $comp_id_sql = '';
      //echo 'comp_id here!<br />';
      if ($id_is_array) {
        $comp_id_lastind = count($id) - 1;
        //echo $comp_id_lastind;
        foreach ($id as $key => $eachID) {
          $comp_id_sql .= 'entry LIKE \'%ed Component ' . $eachID . '%\'';
          if ($key < $comp_id_lastind) {
            $comp_id_sql .= ' OR ';
          }
        }
      } else {
        $comp_id_sql = 'entry LIKE \'%ed Component ' . $id . '%\'';
      }
      $sql = 'SELECT SUBSTRING(entry, INSTR(entry, \'Component\'), 15) AS component_id,entry FROM log WHERE entry NOT LIKE \'%Retrieved%\' AND (' .
 $comp_id_sql . ') ORDER BY component_id';
      //$sql = 'SELECT entry FROM log WHERE entry NOT LIKE \'%Retrieved%\' AND (' . $comp_id_sql . ') ORDER BY timestamp';
      $heading_var = 'comp';
      break;
    case $charge_id:
      //echo 'charge_id here!<br />';
      $charge_id_lastind = count($id) - 1;
      //echo $charge_id_lastind;
      $charge_sql_or = '';
      foreach ($id as $key => $eachID) {
        $charge_sql_or .= 'entry LIKE \'% ' . $eachID . '. (Query: %\'';
        if ($key < $charge_id_lastind) {
          $charge_sql_or .= ' OR ';
        }
      }
      $sql = 'SELECT entry FROM log WHERE (entry LIKE \'%insert+into+charge%\' OR entry LIKE \'%update+charge%\') AND (' . $charge_sql_or . ') ORDE
R BY timestamp';
      $heading_var = 'charge';
      break;
    default:
      //echo 'ummmmm... problem near the switch...<br />';
      break;
  }
} else {
    echo '<br />...still no *_id param value in your URL (or, *_id == 0).';
}

if (isset($sql)) {
  $result = mysql_query($sql);
  if (!$result) {
    echo '<div class="wrapMe"><div class="dbError">DB Error, could not list tables<br />';
    echo 'MySQL Error: ' . mysql_error() . '</div></div>';
    exit;
  } else {
    echo '<p>#winning</p>';
  }
} else {
  echo '<p>No sql query to run :(</p>';
  exit;
}

$output = '<h2>Queries for everyone!</h2><p>Below are the ' . mysql_num_rows($result) . ' queries run on the ' . $heading_var . '_id(s) you provide
d in the URL, excluding "Retrieved" and "Delete" queries where applicable.</p><p><strong>NOTE:</strong> "Component" and "Charge" entries are ordere
d by component/charge ID, so every "insert" entry indicates that the queries pertain to the next component/charge record.</p><pre>';
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
  /*if ($row['component_id']) {
    $comp_id_field = var_export($row['component_id'], TRUE);
    $comp_id_field = substr($comp_id_field, 1, -1); // trip the leading & trailing '
    $comp_id_field .= ': ';
  }*/
  $entry_string = urldecode(var_export($row['entry'], TRUE));
  $query_string_pre = strpos($entry_string, '. (Query: ');
  // query string start = "preface" strpos + num chars in "preface"
  $query_string_start = $query_string_pre + 10;
  $query_string = substr($entry_string, $query_string_start, -2); // -2 to trim ') from end
  $output .= isset($comp_id_field) ? $comp_id_field . $query_string . ";\n" : $query_string . ";\n";
}

$output .= '</pre>';
$output .= '<h5>Query used to generate the results above:</h5><pre>' . $sql . '</pre>';
print $output;

mysql_free_result($result);

// if we're doing wr_id, be a mensch and get the IDs of associated component elements
if (isset($id) && ($id == $wr_id)) {
  $sql = 'SELECT entry FROM log WHERE entry LIKE \'dbDelete(): %\' AND entry LIKE \'%CD-REDACTED ' . $id . '%\' AND entry LIKE \'%where component_id in%
\' LIMIT 1';
  $result = mysql_query($sql);
  if (!$result) {
    echo '<div class="wrapMe"><div class="dbError">DB Error, could not list tables<br />';
    echo 'MySQL Error: ' . mysql_error() . '</div></div>';
    exit;
  } else {
    echo '<!--#winning -- again!-->';
  }
  $outputPartDeux = '<h3>Also!...</h3><p>For you, we\'ll throw in the IDs "Component" elements associated with this workrequest:';
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $entry_string = urldecode(var_export($row['entry'], TRUE));
    $comp_id_string_pre = strpos($entry_string, 'where component_id in ( ');
    // comp_id string start = "preface" strpos + num chars in "preface"
    $comp_id_string_start = $comp_id_string_pre + 24;
    $comp_id_string = substr($entry_string, $comp_id_string_start, -7); // -7 to trim )\').' from end
    $outputPartDeux .= "\n<code>" . $comp_id_string . "</code>\n";
  }
  $outputPartDeux .= '...oh speaking of which, <a href="redacted/another_thing.php?comp_id=' . $comp_id_string . '" title="Query histories for all co
mponents associated with this work request">over here</a> are some other helpful queries...</p>';
  $outputPartDeux .= '<h5>Query used to generate the list of component IDs:</h5><pre>' . $sql . '</pre>';
  print $outputPartDeux;
  mysql_free_result($result);
}

// close mysql connection
include_once('redacted/the_name_of_mysql_disconnect_include.inc'); //see note next to this one's counterpart at the top

?>

<table border="0" cellpadding="0" cellspacing="0" class="suppressOnPrintBodyArea"><tr class="spacerRow"><td>&nbsp;</td></tr></table></td>
<td class="suppressOnPrintBodyArea">&nbsp;</td>
</tr></table>

</body>
