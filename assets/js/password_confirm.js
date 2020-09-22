// call jquery
import $ from "jquery";

let allowsubmit = false;

$(function(){
    //on keypress
    $('#confpass').keyup(function(e){
        //get values
        let pass = $('#pass').val();
        let confpass = $(this).val();

        //check the strings
        if(pass === confpass){
            //if both are same remove the error and allow to submit
            $('.error').text('');
            allowsubmit = true;
        }else{
            //if not matching show error and not allow to submit
            $('.error').text('Password not matching');
        }
    });

    //jquery form submit
    $('#form').submit(function(){

        let pass = $('#pass').val();
        let confpass = $('#confpass').val();

        //just to make sure once again during submit
        //if both are true then only allow submit
        if(pass === confpass){
            allowsubmit = true;
        } else return allowsubmit;
    });
});