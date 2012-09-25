// enable year and section tabs
$('#pyear a').click(function (e) {
  e.preventDefault();
  $(this).tab('show');
})
$('#section a').click(function (e) {
  e.preventDefault();
  $(this).tab('show');
})

// pyear and section button events
$(function($) {
	var pyear = '';
	var section = '';
	$('button', '#pyear-buttons').each(function(){
		var button = $(this);
		button.live('click', function(){
			var pyear = $(this).val();
			var section = $('#section-buttons .active').val();
			console.log(pyear + ' -- ' + section);	
		});
	});
	$('button', '#section-buttons').each(function(){
		var button = $(this);
		button.live('click', function(){
			var pyear = $('#pyear-buttons .active').val();
			var section = $(this).val();
			console.log(pyear + ' -- ' + section);	
		});
	});

	$('.edit').editable('http://www.example.com/save.php', {
         indicator : 'Saving...',
         tooltip   : 'Click to edit...'
     });

	$(function() {
		$( ".tabs" ).tabs();
	});


});

$(document).ready(function() {
	var showChar = 50;
	var ellipsestext = "...";
	var moretext = "more";
	var lesstext = "less";
	$('.more').each(function() {
		var content = $(this).html();

		if(content.length > showChar) {

			var c = content.substr(0, showChar);
			var h = content.substr(showChar-1, content.length - showChar);

			var html = c + '<br/><span class="morecontent"><span>' + h + '</span><a href="" class="morelink">'+moretext+'</a></span>';

			$(this).html(html);
		}

	});

	$(".morelink").click(function(){
		if($(this).hasClass("less")) {
			$(this).removeClass("less");
			$(this).html(moretext);
		} else {
			$(this).addClass("less");
			$(this).html(lesstext);
		}
		$(this).parent().prev().toggle();
		$(this).prev().toggle();
		return false;
	});
});


