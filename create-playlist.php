<?php 

require_once('getid3/getid3.php');


$max_duration = 0;

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

        $mp3file = 'assets/audio/'.$track['mp3_filename'];

        if(file_exists($mp3file)){
          $getID3 = new getID3;
          $ThisFileInfo = $getID3->analyze($mp3file);
          getid3_lib::CopyTagsToComments($ThisFileInfo);
          $track['mp3_duration_secs'] = isset($ThisFileInfo['playtime_seconds']) ? $ThisFileInfo['playtime_seconds'] : 0;
          $track['mp3_duration_string'] = isset($ThisFileInfo['playtime_string']) ? $ThisFileInfo['playtime_string'] : 0;

          $track['tid'] = $n[1];
          $track['order'] = $n[2];
          $track['entry_name'] = $n[3];
          
          $playlist[] = $track;

          $max_duration = max($max_duration, $track['mp3_duration_secs']);  
        }else{
          echo $track['title']." | file ".$mp3file." does not exists !\n";          
        } 
          
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

echo "playlist created \n";
echo "max_duration = ".$max_duration."\n";

?>