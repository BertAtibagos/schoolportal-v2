$(document).ready(function(){
	$('#acadlvl').change(function(){
		var level_id = $(this).val();
		$.ajax({
			type: "POST",
			url: "../../model/class/academicyear-class.php",
			data:{
				level_id : level_id
			},
			success: function(data)
			{
				$("#dropdown-academic-year").show();
				$("#dropdown-academic-year").html(data);
			}
		});
		$.ajax({
			type: "POST",
			url: "../../model/class/academicperiod-class.php",
			data:{
				level_id : level_id
			},
			success: function(data)
			{
				$("#dropdown-academic-period").show();
				$("#dropdown-academic-period").html(data);
			}
		});
});

});
