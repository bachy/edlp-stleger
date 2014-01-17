$(document).ready(function() {
  
  function init(){
    loadCorpus();
  }

  function loadCorpus(){
    $.ajax({
      url: 'ajax.php',
      // type: 'default GET (Other values: POST)',
      dataType: 'json',
      // data: {param1: 'value1'},
    })
    .done(function(data) {
      console.log("success", data);
    })
    .fail(function(e) {
      console.log("error", e);
    })
    .always(function() {
      console.log("complete");
    });
    
  }




  init();
});