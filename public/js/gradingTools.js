$(function($) {

	// enable year and section tabs
	$('#pyear a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	})
	$('#section a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	})


	// set pyear and section buttons
	$('#p' + pyear).addClass('active');	
	$('#s' + section).addClass('active');	

	// pyear and section button events
	//var pyear = '';
	//var section = '';

	var sectionForm = $("<form id='section_filter' action='/grading' method='post'><input type='hidden' id='p_year_id_input' name='p_year_id' value=''/><input type='hidden' id='section_id_input' name='section_id' value=''/></form>");

	$('body').append(sectionForm);

	$('button', '#pyear-buttons').each(function(){
		var button = $(this);
		button.live('click', function(){
			var pyear = $(this).val();
			var section = $('#section-buttons .active').val();
			if(typeof(section) != 'undefined') {
				$("#p_year_id_input").val(pyear);
				$("#section_id_input").val(section);
				console.log($("#p_year_id_input").val() + ' -- ' + $("#section_id_input").val());
				$('form#section_filter').submit();	
			}	
			
		});
	});
	$('button', '#section-buttons').each(function(){
		var button = $(this);
		button.live('click', function(){
			var pyear = $('#pyear-buttons .active').val();
			var section = $(this).val();

			if(typeof(pyear) != 'undefined') {
				$("#p_year_id_input").val(pyear);
				$("#section_id_input").val(section);
				console.log($("#p_year_id_input").val() + ' -- ' + $("#section_id_input").val());
				$('form#section_filter').submit();	
			}
		});
	});

	// init tabs
	$(function() {
		$( ".tabs" ).tabs();
	});

	
	// show/hide survey details
	

	// faculty score submission
	$('.score').submit(function(e){
		$.post(
			"/grading/save-scores", 
			$(this).serialize(),
			function(data) {
				var score = $.parseJSON(data);
				var prepField = '#prep_' + score.presenter_user_section_id;
				var profField = '#prof_' + score.presenter_user_section_id;
				$(prepField).html(score.prepAvg);
				$(profField).html(score.profAvg);
			}
		);	
		e.preventDefault();
	});	


	// survey detail
	$('.survey-row').click(function(){
		var surveyData = eval('survey' + $(this).attr('id'));
		$.post(
			'/grading/format-survey-detail',
			{
				survey_id: $(this).attr('id'),
				survey_data: surveyData
			},
			function(data){
				$('<div></div>').html(data).dialog({modal: true, width: 900, height: 700, title: 'Survey Details'});
			}	
		);
		return false;
	});

	// generate report and insert download link
	$('.report').click(function(data){
		var reportId = $(this).attr('id');
		var reportData = eval('report' + reportId);
		var reportUrlBlock = '#reportUrl' + reportId;
		$.post(
			'grading/report',
			{
				report_data: reportData
			},
			function(url) {
				var reportLink = $('<a></a>').html('Download report').attr('href', url);	
				$(reportUrlBlock).html(reportLink);	
			}
		);
		return false;
	});


	// more/less functionality
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



