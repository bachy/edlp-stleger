$(document).ready(function() {
  
  var crt_id = 0;
  var keys, curr_corpus = {};
  var $cartel_wrapper = $('#cartel-wrapper');
  var $last_cartel, $next_cartel;
  var $audio, $progressbar, $duration;

  function init(){
    loadKeys();
    // loadCorpus();
  };

  function loadKeys(){
    $.ajax({
      url: 'ajax.php',
      // type: 'default GET (Other values: POST)',
      dataType: 'json',
      data: {index: 0},
    })
    .done(keysLoaded)
    .fail(function(e) {
      console.log("error", e);
    })
    .always(function() {
      console.log("complete");
    });
  };

  function keysLoaded(data){
    keys = data.corpus;
    console.log('keys = ', keys);
    loadCorpus();
  };

  function loadCorpus(){
    crt_id ++;
    $.ajax({
      url: 'ajax.php',
      // type: 'default GET (Other values: POST)',
      dataType: 'json',
      data: {index: crt_id},
    })
    .done(corpusLoaded)
    .fail(function(e) {
      console.log("error", e);
    })
    .always(function() {
      console.log("complete");
    });
    
  };

  function corpusLoaded(data){
    console.log('corpusLoaded', data);
    
    // var i = 0;
    for(key in keys){
      // console.log('key = '+keys[key]);
      curr_corpus[keys[key]] = data.corpus[key];
      // i++;
    }
    console.log('curr_corpus = ', curr_corpus);

    displayCorpus();    

    // setTimeout(function(){
    //   loadCorpus();
    // }, 6000);
  };

  function displayCorpus(){
    $last_cartel = $next_cartel;
    $next_cartel = $('<div>')
      .addClass('cartel')//.addClass('standby')
      .attr('id', 'cartel-'+crt_id)
      .appendTo($cartel_wrapper);
    
    var mp3path = '/assets/audio/'+curr_corpus.mp3_filename;
    var oggpath = mp3path.replace('.mp3', '.ogg');
    $audio = $('<audio>')//.attr('autoplay', 'autoplay')
                    .append($('<source>').attr('src', mp3path))
                    .append($('<source>').attr('src', oggpath));

    var $progress = $('<div>').addClass('progress');
    $progressbar = $('<div>').addClass('progress-barre').appendTo($progress);

    $duration = $('<div>').addClass('duration');

    $next_cartel
      .append($('<h2>').addClass('corpus-title').text(curr_corpus.title))
      .append($('<div>').addClass('corpus-body').html(curr_corpus.body))
      .append($duration)
      .append($progress)
      .append($audio);

    setTimeout(function(){
      if($last_cartel)
        $last_cartel.removeClass('in').addClass('out');
      $next_cartel.addClass('in');
    }, 5);
    
    setTimeout(function(){
      if($last_cartel)
        $last_cartel.remove();
    }, 1500);

    setTimeout(startPlaying, 1000);
  };

  function startPlaying(){
    $audio
      .on('timeupdate', onSoundTimeUpdate)
      .on('ended', onSoundEnded)
      .on('paused', onSoundPaused)
      .on('error', onSoundError);

    $audio[0].play();

  };


  var curTime, dur, 
        cur_mins, cur_secs, 
        dur_mins, dur_secs;
  var prct;
  function onSoundTimeUpdate(event){
      // console.log('timeupdate', event);
      curTime = event.currentTarget.currentTime;
      dur = event.currentTarget.duration;

      cur_mins=Math.floor(curTime/60);
      cur_secs= Math.floor(curTime-cur_mins * 60);

      dur_mins=Math.floor(dur/60);
      dur_secs= Math.floor(dur-dur_mins * 60);

      $duration.html('<span class="current-time">'+(cur_mins>9?cur_mins:"0"+cur_mins)+':'+(cur_secs>9?cur_secs:"0"+cur_secs)+'</span> / <span class="duration">'+(dur_mins>9?dur_mins:"0"+dur_mins)+':'+(dur_secs>9?dur_secs:"0"+dur_secs)+'</span>')
      
      prct = curTime*100/dur;
      $progressbar.width(prct+"%");
  }

  function onSoundEnded(event){
     console.log('onSoundEnded');
     loadCorpus();
  }

  function onSoundPaused(event){
    console.log('onSoundPaused');
    loadCorpus();
  }
  function onSoundError(event){
    console.log('onSoundError');
    loadCorpus();
  }


  init();
});