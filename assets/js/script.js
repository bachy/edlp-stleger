$(document).ready(function() {
  
  var count = 0, crt_id = -1;
  var curr_corpus;
  var $cartel_wrapper = $('#cartel-wrapper');
  var $last_cartel, $next_cartel;
  var $audio, $progressbar, $duration;

  var curTime, dur = false, 
        cur_mins, cur_secs, 
        dur_mins, dur_secs,
        curtxt, durtxt;
  var prct;

  var debug = false; $debug = $('#debug');//, $index = $('<span>').addClass('index').appendTo($debug);


  function init(){
    if(!debug){
      $debug.remove();
    }

    $('#close').click(function(event){
      open(location, '_self').close();
    });

    initDB();
    // loadCorpus();
  };

  function initDB(){
    $.ajax({
      url: 'ajax.php',
      // type: 'default GET (Other values: POST)',
      dataType: 'json',
      data: {fun:'init'},
    })
    .done(dbInited)
    .fail(dbInitFailed)
    .always(function() {
//console.log("complete");
    });
  };

  function dbInitFailed(event){
    $cartel_wrapper.append($('<div class="error">init function failed !! we will retry in 10 secs</div>'));
    setTimeout(function(){
      $cartel_wrapper.html('');
      initDB();
    }, 10000);
  };

  function dbInited(data){
//console.log('dbInited', data);
    count = data.count;
    loadCorpus();
  };

  function loadCorpus(){
    crt_id ++;

    if(crt_id > 753)
      crt_id = 0;
    
    // if(debug)
      // displayDebug("next cartel index = "+crt_id);

    $('footer .index').text(crt_id);
    
    $.ajax({
      url: 'ajax.php',
      // type: 'default GET (Other values: POST)',
      dataType: 'json',
      data: {fun:'corpus', index: crt_id},
    })
    .done(corpusLoaded)
    .fail(corpusFailed);
//     .always(function() {
// //console.log("complete");
//     });
    
  };

  function corpusFailed(event){
    $cartel_wrapper.append($('<div class="error">corpus loading failed !! we will retry in 10 secs</div>'));
    setTimeout(function(){
      $cartel_wrapper.html('');
      loadCorpus();
    }, 10000);
  };

  function corpusLoaded(data){
  //console.log('corpusLoaded', data);
    
    curr_corpus = data.corpus; 
    //console.log('curr_corpus = ', curr_corpus);

    displayCorpus();
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
      .append($('<h3>').addClass('corpus-entry').html(curr_corpus.entry_name))
      .append($('<div>').addClass('corpus-body').html(curr_corpus.body))
      // .append($('<div>').addClass('corpus-info').html(curr_corpus.body))
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

    dur = curr_corpus.mp3_duration_secs;
    // dur_mins=Math.floor(dur/60);
    // dur_secs= Math.floor(dur-dur_mins * 60);
    // durtxt = dur+typeof dur;
    durtxt =  ' / <span class="duration">'+curr_corpus.mp3_duration_string+'</span>';


    setTimeout(startPlaying, 1000);
  };

  function startPlaying(){
    try{
      $audio
        .on('timeupdate', onSoundTimeUpdate)
        .on('ended', onSoundEnded)
        .on('paused', onSoundPaused)
        .on('error', onSoundError);

      $audio[0].play();
    }catch(e){
      if(debug)
        displayDebug(e);

      startPlayingFailed();
    }
  };

  function startPlayingFailed(){
    $cartel_wrapper.append($('<div class="error">startPlaying function failed !! we will retry in 10 secs</div>'));
    setTimeout(function(){
      $cartel_wrapper.html('');
      loadCorpus();
    }, 10000);
  };


  function onSoundTimeUpdate(event){
      // console.log('timeupdate', dur);
      try{
        curTime = event.currentTarget.currentTime;
        cur_mins=Math.floor(curTime/60);
        cur_secs= Math.floor(curTime-cur_mins * 60);
        curtxt = '<span class="current-time">'+cur_mins+':'+(cur_secs>9?cur_secs:"0"+cur_secs)+'</span>';

        prct = curTime*100/dur;
        $progressbar.width(prct+"%");
        
        $duration.html(curtxt+durtxt);
      }catch(e){
        $cartel_wrapper.append($('<div class="error">on sound time update error !!</div>'));
      }
  }

  function onSoundEnded(event){
    //console.log('onSoundEnded');
     loadCorpus();
  }

  function onSoundPaused(event){
    //console.log('onSoundPaused');
    loadCorpus();
  }
  function onSoundError(event){
    $cartel_wrapper.append($('<div class="error">sound error !! we will retry in 10 secs</div>'));
    setTimeout(function(){
      $cartel_wrapper.html('');
      loadCorpus();
    }, 5000);
  }

  function displayDebug(msg){
    var currentTime = new Date()
    var hours = currentTime.getHours()
    var minutes = currentTime.getMinutes()

    $debug.prepend('<p><b>'+hours+' : '+(minutes < 10 ? "0"+minutes : minutes)+' | </b>'+msg+'</p>');
  }


  init();
});