// Invite form for User
import $ from 'jquery';

if ($('#inviterBtn').length) {
	$('#inviterBtn').on('click', (evt) => {
		evt.preventDefault();
		$('#inviterForm').fadeIn();
		$('#inviterBtn').hide();
	});

	$('#closeInviter').on('click', (evt) => {
		evt.preventDefault();
		$('#inviterBtn').fadeIn();
		$('#inviterForm').hide();
	});
}
