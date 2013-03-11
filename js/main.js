/* Original Author:
  Rene Sijnke
  http://www.krsvrs.nl
*/

var data;
  
var groups = {
  "A-karmahunter": [
    {"text": "Sparer op på karmakontoen. Godt gået!",
    "icon": "★"}
  ],
  "B-goalie": [
    {"text": "Arbejder derudaf, rammer timerne perfekt. Sådan!",
    "icon": "✓"} // "✓" "☯"
  ],
  "C-karmauser": [
    {"text": "Trækker på karmakontoen. Der er jo andet i livet end arbejde :-)",
    "icon": "☂"}
  ],
  "D-slacker": [
    {"text": "Du mangler at registrere *meget* tid! Er du nu på ferie igen?!",
    "icon": "☁"}
  ]         
};
  
$(function(){
	
	$.post("inc/get_ranking.php", data, function(data) {
		
		
		if (data.succes) {
		
      $('.container').addClass('show');
		  $('#loader').addClass('hide');
  		
		  
		  // animate the timer
		  timer_count($('.logged_hours'), Math.round(data.hours_total_registered));
		  
		  $('.hours_togo strong').html(Math.round(data.hours_total_month-data.hours_total_registered));
		  $('#hours_productive h4').html(Math.round((data.hours_total_registered / data.hours_until_today)*100));
		  
		  $('#progress').delay(400).animate({width: ((Math.round(data.hours_total_registered) / (data.hours_total_month)) * 390) + 'px'}, 1000, 'easeOutExpo');
		
		  for(var i = 0; i < data.ranking.length; i++){
  		  
  		  // clone the existing markup
  		  var _item = $('li.hide').clone();
        _item.removeClass('hide');
        _item.addClass('user');
  		  _item.addClass(data.ranking[i].group);
  		  
  		  // add an avatar
  		  _item.find('.user_avatar_holder').html($('<img src="https://assetcache.harvestapp.com/uploads/users/avatar/000/'+data.ranking[i].user_id_first_part+'/'+data.ranking[i].user_id_second_part+'/normal.jpg">'));
        


        
  		  
  		  // Goalies gets the moustache / hat
  		  if(data.ranking[i].group == "B-goalie") _item.find('.user_avatar').prepend($('<figure class="sir"></figure>'));
  		  
  		  // sets the ranking numbers and name
  		  //_item.find('.rank').addClass('bg'+i).html(i+1);
  		  _item.find('.rank').addClass(data.ranking[i].group).html(groups[data.ranking[i].group][0].icon);
        _item.find('h2').html(data.ranking[i].name);
  		  
  		  // winner and loser get a custom text, everybody else the default text
  		  // if(data.ranking[i].group == "B-goalie") _item.find('.hours').html('Like a boss!');
  //		  else if(i == data.ranking.length-1) _item.find('.hours').html('Only <span>'+data.ranking[i].hours_registered+' hours logged</span>. What a whimp!');
  //		  else _item.find('.hours').html('<span>'+data.ranking[i].hours_registered+' hours logged</span>.');
  		  
  		  // get a 'funny' sentence from the lines object
  		  _item.find('.desc').html(groups[data.ranking[i].group][0].text);

  		  // append it
  		  $('#user_ranking ul').append(_item);
  		  
		  }
			
			
		} else {
				
		}
	},"json");
	
});

function timer_count(target, amount) {
  
  var _labelAnimDiv = $('<div />');

	var currentLabel = Number(0);
	_labelAnimDiv.css('text-indent', currentLabel);
	
	_labelAnimDiv.stop();
	
	_labelAnimDiv.animate({
		'text-indent': amount + 'px'
	},
	{
		duration: 2500,
		easing: 'easeInOutCirc',
		step: function(now, fx) {
			target.text(Math.round(now));
		}
	});
  
}
