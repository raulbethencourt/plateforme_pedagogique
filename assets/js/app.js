/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import '../css/app.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import $ from "jquery";

let data = null;

import './chartJS';
import './question_create';
import './password_confirm';

// Invite form for User
$('#inviterBtn').click(function (e) {
    e.preventDefault();
    $('#inviteForm').fadeIn();
    $('#inviterBtn').hide();
});