// Invite form for User
import $ from 'jquery';

if ($('#inviterBtn').length) {
	$('#inviterBtn').on('click', (e) => {
		console.log(e);
		e.preventDefault();
		$('#inviterForm').fadeIn();
		$('#inviterBtn').hide();
	});
}
