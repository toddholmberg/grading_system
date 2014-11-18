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

	var sectionForm = $("<form id='section_filter' action='/seminars/grading' method='post'><input type='hidden' id='p_year_id_input' name='p_year_id' value=''/><input type='hidden' id='section_id_input' name='section_id' value=''/></form>");

	$('body').append(sectionForm);

	$('button', '#pyear-buttons').each(function(){
		var button = $(this);
		button.live('click', function(){
			var pyear = $(this).val();
			var section = $('#section-buttons .active').val();
			if(typeof(section) != 'undefined') {
				$("#p_year_id_input").val(pyear);
				$("#section_id_input").val(section);
				//console.log($("#p_year_id_input").val() + ' -- ' + $("#section_id_input").val());
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
				//console.log($("#p_year_id_input").val() + ' -- ' + $("#section_id_input").val());
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
			"/seminars/grading/save-scores", 
			$(this).serialize(),
			function(data) {
				//console.log(data);
				var score = $.parseJSON(data);
				var prepField = '#prep_' + score.presenter_user_section_id;
				var profField = '#prof_' + score.presenter_user_section_id;
				var finalField = '#final_' + score.presenter_user_section_id;
				$(prepField).html(score.prepAvg);
				$(profField).html(score.profAvg);
				$(finalField).html(score.finalScore.letter + ' (' + score.finalScore.number + ')');
			}
		);	
		e.preventDefault();
	});	


	// survey detail
	$('.survey-row').click(function(){
		var surveyData = eval('survey' + $(this).attr('id'));
		$.post(
			'/seminars/grading/format-survey-detail',
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
	// allow one click on button per page load
	$('.faculty-report').one('click', function(data){
		$(this).addClass('disabled');
		var valArray = $(this).attr('id').split('seminar_');
		var seminarId = valArray[1];
		var reportUrlBlock = '#facultyReportUrl' + seminarId;
		var spinner = $('<img/>').attr('src', '/seminars/img/progress.gif');
		$(reportUrlBlock).html(spinner);
		$.post(
			'/seminars/grading/report',
			{
				seminarId: seminarId,
				reportType: 'faculty'
			},
			/*function(data){
				$('<div></div>').html(data).dialog({modal: true, width: 900, height: 700, title: 'Survey Details'});
			}*/

			function(url) {
				var reportLink = $('<a></a>').html('Download report').attr('href', url);	
				$(reportUrlBlock).html(reportLink);	
			}
		);
		return false;
	});

	$('.student-report').one('click', function(data){
		$(this).addClass('disabled');
		var valArray = $(this).attr('id').split('seminar_');
		var seminarId = valArray[1];
		var reportUrlBlock = '#studentReportUrl' + seminarId;
		var spinner = $('<img/>').attr('src', '/seminars/img/progress.gif');
		$(reportUrlBlock).html(spinner);
		$.post(
			'/seminars/grading/report',
			{
				seminarId: seminarId,
				reportType: 'student' 
			},
			/*function(data){
				$('<div></div>').html(data).dialog({modal: true, width: 900, height: 700, title: 'Survey Details'});
			}*/
			function(url) {
				var reportLink = $('<a></a>').html('Download report').attr('href', url);	
				$(reportUrlBlock).html(reportLink);	
			}
		);
		return false;
	});

	$('.full-report').one('click', function(data){
		$(this).addClass('disabled');
		var valArray = $(this).attr('id').split('seminar_');
		var seminarId = valArray[1];
		var reportUrlBlock = '#fullReportUrl' + seminarId;
		var spinner = $('<img/>').attr('src', '/seminars/img/progress.gif');
		$(reportUrlBlock).html(spinner);
		$.post(
			'/seminars/grading/report',
			{
				seminarId: seminarId,
				reportType: 'full' 
			},
			function(url) {
				var reportLink = $('<a></a>').html('Download report').attr('href', url);	
				$(reportUrlBlock).html(reportLink);	
			}
		);
		return false;
	});

	// roster
	$('.roster').on('click', function(data){
		var valArray = $(this).attr('id').split('_');
		var pyear = valArray[1];
		var section = valArray[2];

		$.post(
			'/seminars/grading/roster',
			{
				pyear: pyear,
				section: section
			},
			function(url){
				$("#secretIFrame").attr("src",url);
			}
		);
		return false;
	});

		
	$('.presenter').hover(
		function(){
			//$(this).children().show();
		},
		function(){
			//$(this).children().hide();
		}
	);	


});



