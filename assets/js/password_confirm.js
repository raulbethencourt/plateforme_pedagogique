// call jquery
import $ from 'jquery';

let allowsubmit = false;
//on keypress

$('#confpass').on('keyup', function (e) {
	//get values
	let pass = $('#registration_form_plainPassword').val();
	let confpass = $(this).val();

	//check the strings
	if (pass === confpass) {
		//if both are same remove the error and allow to submit
		$('.error').text('');
		allowsubmit = true;
	} else {
		//if not matching show error and not allow to submit
		$('.error').text('Mot de passe invalide');
	}
});

//jquery form submit
$('.regisForm').on('click', function () {
	let pass = $('#registration_form_plainPassword').val();
	let confpass = $('#confpass').val();

	//just to make sure once again during submit
	//if both are true then only allow submit
	if (pass === confpass) {
		allowsubmit = true;
	} else return allowsubmit;
});
