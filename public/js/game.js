$(document).ready(function(){
  get_count_win();

  $('.cell').click(function(e){
    var id = e.target.id;
    if($('#' + id).is(':empty') && $('#status').is(':empty')){
      $('#' + id).append("X");

      $('#step').val(id);
      var url = $('#step_form').attr("action");
      var formData = $('#step_form').serializeArray();
      $.post(url, formData).done(function (data) {
        $('#' + data.step).append('0');

        if(data.message !== undefined){
          $('#status').empty();
          $('#status').append(data.message);
          $('#give_up').addClass('disabled');
          get_count_win();
        }
      });
    }
  });

  $('#new_game').click(function(){
    $('.cell').empty();
    $.get("/new_game").done(function (data) {
      $('#game_id').val(data.game_id);
      $('#status').empty();
      if($('#give_up').hasClass('disabled')){
        $('#give_up').removeClass('disabled');
      }
      $('.cell').each(function(){
        $(this).removeClass('cell-disable');
      });

    });
  });

  $('#give_up').click(function(){
    if($('#game_id').val() != "" && $('#status').is(':empty')){
      $.get("/give_up/" + $('#game_id').val()).done(function (data) {
        get_count_win();
        $('#give_up').addClass('disabled');
        $('#status').append('You Give Up!');
      });
    }
  });

  $('.a_result').click(function(e){
    var id = e.target.id;

    get_list_win(id);

  });

  function get_list_win(type){
    $.get("/get_list_win/" + type).done(function (data) {
      $('#list_step tbody').empty();

      for(i = 0; i < data.result.length; i++){
        //console.log(data.result[i].created_at );
        var steps = "";
        for(j = 0; j < data.result[i].steps.length -1; j++){
          steps = steps + data.result[i].steps[j].step + '->';
        }
        if(data.result[i].steps.length !== 0){
          console.log(data.result[i]);
          steps = steps + data.result[i].steps[data.result[i].steps.length - 1].step;
        }
        $('#list_step tbody').append('<tr>' +
            '<td>' + (i + 1) + '</td>' +
            '<td>' + data.result[i].created_at + '</td>' +
            '<td>' + steps + '</td>' +
            '</tr>');
      }
    });
  }
  function get_count_win(){
    $.get("/get_count_win").done(function (data) {
      $('#user').text(data.count_user);
      $('#pc').text(data.count_pc);
    });

  }
});
