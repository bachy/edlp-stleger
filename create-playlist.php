<?php 


// if(!ini_set('default_socket_timeout',    15)) 
//   $ret['errors'][] = "unable to change socket timeout";

# if we have internet and we can connect to drive spreadsheet
// sheet 1
// https://docs.google.com/spreadsheet/pub?key=0AouWOA7wSzR-dG14ZUFuc2t5RlR4dk1DdjZKZUJtR1E&single=true&gid=0&output=csv
// sheet 2
// https://docs.google.com/spreadsheet/pub?key=0AouWOA7wSzR-dG14ZUFuc2t5RlR4dk1DdjZKZUJtR1E&single=true&gid=1&output=csv

// $drive_csv_url_s1 = "https://docs.google.com/spreadsheet/pub?key=0AouWOA7wSzR-dG14ZUFuc2t5RlR4dk1DdjZKZUJtR1E&single=true&gid=0&output=csv";
// $drive_csv_url_s2 = "https://docs.google.com/spreadsheet/pub?key=0AouWOA7wSzR-dG14ZUFuc2t5RlR4dk1DdjZKZUJtR1E&single=true&gid=1&output=csv";

$drive_csv_url_s1 = "assets/data/export-nodes.csv";
$drive_csv_url_s2 = "assets/data/export-entrees.csv";

if (($nodes_file_csv = fopen($drive_csv_url_s1, "r")) !== FALSE && ($entrees_file_csv = fopen($drive_csv_url_s2, "r")) !== FALSE){
  $n_keys = false;

  while ( ($n = fgetcsv($nodes_file_csv, 5000, ",")) !== FALSE) {
      # csv column names
      if(!$n_keys){
        $n_keys = $n;
      }
      # csv content
      else{
        for ($i=0; $i < count($n_keys) ; $i++) { 
          $node[$n_keys[$i]] = $n[$i];
        }
        $nodes[$n[0]] = $node;
      }
  }
  while ( ($e = fgetcsv($entrees_file_csv, 5000, ",")) !== FALSE) {
      $entries[$e[1]][$e[2]] = $e;
  }
  
  fclose($nodes_file_csv);
  fclose($entrees_file_csv);
  
  // $ret['nodes'] = $nodes;
  // $ret['entries'] = $entries;

  # TODO :  i'll have to sort the entries order

  foreach ($entries as $tid => $list) {
    ksort($list);
    foreach ($list as $wit => $n) {
      if(isset($nodes[$n[0]])){
        $track = $nodes[$n[0]];
        $track['tid'] = $n[1];
        $track['order'] = $n[2];
        $track['entry_name'] = $n[3];
        $playlist[] = $track;  
      }
    }
  }

  // $ret['count'] = count($playlist);

  $playlist_str = serialize($playlist);
  $playlist_files = fopen('assets/data/playlist.txt', 'r+');
  $fwrite = fwrite($playlist_files, $playlist_str);
  if($fwrite){
    $ret['messages'][] = 'playlist stored in playlist.txt';
  }else{
    $ret['errors'][] = 'problem occure while stored playlist.txt';
  }
  fclose($playlist_files);
}


?>