<?php 

switch ($_GET['fun']) {
  case 'init':
    init();
    break;
  case 'corpus':
    getCorpus();
    break;
}


function init(){

  # array rtet to fill and then return as json
  $ret = array();

  
  if(!ini_set('default_socket_timeout',    15)) 
    $ret['errors'][] = "unable to change socket timeout";

  # if we have internet and we can connect to drive spreadsheet
  // sheet 1
  // https://docs.google.com/spreadsheet/pub?key=0AouWOA7wSzR-dG14ZUFuc2t5RlR4dk1DdjZKZUJtR1E&single=true&gid=0&output=csv
  // sheet 2
  // https://docs.google.com/spreadsheet/pub?key=0AouWOA7wSzR-dG14ZUFuc2t5RlR4dk1DdjZKZUJtR1E&single=true&gid=1&output=csv

  $drive_csv_url_s1 = "https://docs.google.com/spreadsheet/pub?key=0AouWOA7wSzR-dG14ZUFuc2t5RlR4dk1DdjZKZUJtR1E&single=true&gid=0&output=csv";
  $drive_csv_url_s2 = "https://docs.google.com/spreadsheet/pub?key=0AouWOA7wSzR-dG14ZUFuc2t5RlR4dk1DdjZKZUJtR1E&single=true&gid=1&output=csv";
  if (($fh1 = fopen($drive_csv_url_s1, "r")) !== FALSE && ($fh2 = fopen($drive_csv_url_s2, "r")) !== FALSE){
    $n_keys = false;

    while ( ($n = fgetcsv($fh1, 5000, ",")) !== FALSE) {
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
    while ( ($e = fgetcsv($fh2, 5000, ",")) !== FALSE) {
        $entries[$e[1]][$e[2]] = $e;
    }
    
    fclose($fh1);
    fclose($fh2);
    
    $ret['nodes'] = $nodes;
    $ret['entries'] = $entries;

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

    $playlist_str = serialize($playlist);
    $playlist_files = fopen('assets/data/playlist.txt', 'r+');
    $fwrite = fwrite($playlist_files, $playlist_str);
    if($fwrite){
      $ret['messages'][] = 'playlist stored in playlist.txt';
    }else{
      $ret['errors'][] = 'problem occure while stored playlist.txt';
    }
    fclose($playlist_files);

    # we record it as a local serialized array backup in case of we wont have internet connection any more
    // $data_str = serialize($data);
    // $fh_txt = fopen('assets/data/data.txt', 'r+');
    // $fwrite = fwrite($fh_txt, $data_str);
    // if($fwrite){
    //   $ret['messages'][] = 'data stored in data.txt from google drive';
    // }else{
    //   $ret['errors'][] = 'problem occure while stored data from google drive to data.txt';
    // }
    // fclose($fh_txt);


  }
  # without connection load the serialized array
  // else{
  //   $ret['errors'][] = "Problem reading google drive csv, you may check your internet connection";
  //   # load data from local serialized array
  //   $playlist = unserialize(file_get_contents("assets/data/playlist.txt"));
  // }
  // $ret['playlist'] = $playlist;

  # return json
  returnJson($ret);

  # record session
  // $session_str = serialize($session);
  // $session_txt = fopen('assets/session.txt', 'r+');
  // $session_write = fwrite($session_txt, $session_str);
  // fclose($session_txt);

}


function getCorpus(){
  # load data from local serialized array
  $playlist = unserialize(file_get_contents("assets/data/playlist.txt"));

  # get next sound
  $index = $_GET['index'];

  $ret['index'] = $index;
  $ret['corpus'] = $playlist[$index];

  # return json
  returnJson($ret);

}


/**
* Helpers
*/

function returnJson($array){
  header('Content-Type: application/json');
  echo json_encode($array);
}

?>