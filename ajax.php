<?php 

# array rtet to fill and then return as json
$ret = array(
  // "test" => "hello!"
);

$drive_csv_url = "https://docs.google.com/spreadsheet/pub?key=0AouWOA7wSzR-dG14ZUFuc2t5RlR4dk1DdjZKZUJtR1E&single=true&gid=0&output=csv";

if(!ini_set('default_socket_timeout',    15)) 
  $ret['errors'][] = "unable to change socket timeout";

$data = array();

# if we have internet and we can connect to drive spreadsheet
if (($fh = fopen($drive_csv_url, "r")) !== FALSE){
  while ( ($row = fgetcsv($fh, 5000, ",")) !== FALSE) {
      $data[] = $row;
  }
  fclose($fh);
  
  # we record it as a local serialized array backup in case of we wont have internet connection any more
  $data_str = serialize($data);
  $fh_txt = fopen('assets/data/data.txt', 'r+');
  $fwrite = fwrite($fh_txt, $data_str);
  if($fwrite){
    $ret['messages'][] = 'data stored in data.txt from google drive';
  }else{
    $ret['errors'][] = 'problem occure while stored data from google drive to data.txt';
  }
  fclose($fh_txt);
  
}
# without connection load the serialized array
else{
  $ret['errors'][] = "Problem reading google drive csv, you may check your internet connection";
  # load data from local serialized array
  $data = unserialize(file_get_contents("assets/data/data.txt"));
}
// $ret['alldb'] = $data;

# get next sound
$index = $_GET['index'];

$ret['index'] = $index;
$ret['corpus'] = $data[$index];

# check time


# return json
returnJson($ret);


/**
* Helpers
*/

function returnJson($array){
  header('Content-Type: application/json');
  echo json_encode($array);
}

?>