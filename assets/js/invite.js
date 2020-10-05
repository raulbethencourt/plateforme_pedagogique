// Invite form for User
import $ from "jquery";

if ($('#inviterBtn').length) {
    $('#inviterBtn').click(function (e) {
        e.preventDefault();
        $('#inviteForm').fadeIn();
        $('#inviterBtn').hide();
    });
}
