$(document).ready(function() {
	$('textarea[name=status]').keyup(function() {
		updateCount();
	});
});

function updateCount() {
	len = $('#status_update_form textarea[name=status]').val().length;
	addcolor = '';
	if (len > 115){
		addcolor = ' style="color:red;" ';
	}
	if (len > 140){
		$('#tweetbutton').attr("disabled", "disabled");
	} else {
		$('#tweetbutton').removeAttr("disabled");
	}
	lenLeft = 140-len;
	$('#stringlength').html('<strong'+addcolor+'>'+lenLeft+'</strong>');
}
