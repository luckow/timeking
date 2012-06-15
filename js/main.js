/* Author:
  Rene Sijnke
  http://www.krsvrs.nl
*/

var data;
  
var lines = {
  0: [
    {"text": "Damn! This guy is practically running the studio on his own!"},
    {"text": "Hard work makes it easy. That's my secret. That's why I win."}
  ],
  1: [
    {"text": "Doing good. But you're not number one!"},
    {"text": "Running the hours like a sir"}
  ],
  2: [
    {"text": "You are doing something right. Almost there."},
    {"text": "Working hard, having fun. Making a difference!"}
  ],
  3: [
    {"text": "Love conquers all, but if love doesn't do it, try hard work."},
    {"text": "Code more, talk less! Boss is watching you!"}
  ],
  4: [
    {"text": "Work faster, strong, harder."},
    {"text": "There is no substitute for hard work."}
  ],
  5: [
    {"text": "Fatboys are for fridays. Bring your A-game!"},
    {"text": "Code. Relax. Code. Relax. CODE CODE CODE!"}
  ],
  6: [
    {"text": "Less talk, more work"},
    {"text": "I CAN HAZ VACATION? NO? Get a move on!"}
  ],
  7: [
    {"text": "Y U NO WORK HARDER? Need coffee?"},
    {"text": "Bitch please, do you even work?"}
  ],
  8: [
    {"text": "If hard work were really a virtue, then mules would be saints."},
    {"text": "Auch, suxx to be last. Did you forget to put in hours?!"}
  ]          
};
  
$(function(){
	
	$.post("inc/get_ranking.php", data, function(data) {
		
		
		if (data.succes) {
		
		  $('#container').addClass('in');
  		$('#preloader').stop().animate({opacity: 0}, 300);
		  
		  // animate the timer
		  timer_count($('.logged_hours'), Math.round(data.total));
		  
		  $('.hours_togo strong').html(Math.round((totalHours*data.ranking.length)-Math.round(data.total)));
		  $('#hours_productive h4').html(Math.round((Math.round(data.total) / (workedHours*data.ranking.length))*100));
		  
		  $('#progress').delay(400).animate({width: ((Math.round(data.total) / (totalHours*data.ranking.length)) * 390) + 'px'}, 1000, 'easeOutExpo');
		
		  for(var i = 0; i < data.ranking.length; i++){
  		  
  		  // clone the existing markup
  		  var _item = $('li.hide').clone();
  		  _item.removeClass('hide');
  		  
  		  // add an avatar
  		  _item.find('.user_avatar_holder').html($('<img src="img/avatar-'+data.ranking[i].name.toLowerCase()+'.png">'));
  		  
  		  // winner gets the moustache 
  		  if(i == 0) _item.find('.user_avatar').prepend($('<figure class="sir"></figure>'));
  		  
  		  // sets the ranking numbers and name
  		  _item.find('.rank').addClass('bg'+i).html(i+1);
  		  _item.find('h2').html(data.ranking[i].name+'.');
  		  
  		  // winner and loser get a custom text, everybody else the default text
  		  if(i == 0) _item.find('.hours').html('Already <span>'+data.ranking[i].hours+' hours logged</span>. Like a boss!');
  		  else if(i == data.ranking.length-1) _item.find('.hours').html('Only <span>'+data.ranking[i].hours+' hours logged</span>. What a whimp!');
  		  else _item.find('.hours').html('<span>'+data.ranking[i].hours+' hours logged</span>.');
  		  
  		  // get a 'funny' sentence from the lines object
  		  _item.find('.desc').html(lines[i][Math.floor((Math.random()*lines[i].length))].text);
  		  
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
