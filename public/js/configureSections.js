$("#academicYearId").change(function(){
	$("#section-data").load(
		"/seminars/section/configure-sections",
		{academicYearId: $(this).val()}
	);
});

