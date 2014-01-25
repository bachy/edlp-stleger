<?php 
echo "#Start exporting nodes order by terms\n";

# connect to drupal mysql db
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'Lhip2L2Mysql!';

$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');

$dbname = 'edlp';
mysql_select_db($dbname);


$nodes_query = sprintf("SELECT tn.nid, tn.tid, tn.weight_in_tid, td.name FROM term_node tn INNER JOIN term_data td
  WHERE td.tid=tn.tid   
  AND td.vid=%d", mysql_real_escape_string(2));
// Perform Query
$nodes_results = mysql_query($nodes_query);

$entrees_csv = fopen('assets/data/entrees.csv', 'w');

$keys = array('nid', 'tid', 'weight_in_tid', "name");

fputcsv($entrees_csv, $keys);

while ($row = mysql_fetch_array($nodes_results)) {
  // $node_orders[] = $node;
  // $node_orders[$node['tid']]['name'] = $node['name'];
  // $node_orders[$node['tid']][$node['weight_in_tid']] = $node['nid'];
  foreach ($keys as $key) {
    $node[$key] = $row[$key];
  }
  fputcsv($entrees_csv, $node); 
}

fclose($entrees_csv);
// echo print_r($node_orders);



/*


// Formulate Query
// This is the best way to perform an SQL query
// For more examples, see mysql_real_escape_string()
$terms_query = sprintf("SELECT tid, name FROM term_data WHERE vid=%d", mysql_real_escape_string(2));
// Perform Query
$terms_results = mysql_query($terms_query);

//fetch tha data from the database
while ($term = mysql_fetch_array($terms_results)) {
  // echo print_r($row)."\n";
  echo "-- ". $term["name"]."\n";

  $nodes_query = sprintf("SELECT nid, tid, weight_in_tid FROM term_node 
    WHERE tid=%d",
    mysql_real_escape_string($term['tid']));
  
  // Perform Query
  $nodes_results = mysql_query($nodes_query);

  while ($node = mysql_fetch_array($nodes_results)) {
    $node_orders[] = $node; 
  }

}

echo print_r($node_orders);
*/

mysql_close($conn);
 ?>