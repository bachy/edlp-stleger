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

  $playlist = unserialize(file_get_contents("assets/data/playlist.txt"));

  $ret['count'] = count($playlist);

  # return json
  returnJson($ret);
}


function getCorpus(){
  # load data from local serialized array
  $playlist = unserialize(file_get_contents("assets/data/playlist.txt"));

  # get next sound
  $index = $_GET['index'];
  // $ret['count'] = count($playlist);
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