/*
 * @Author Alexandre Santos
 */

$(document).ready(function(){
    
    $.getJSON("email.action.php", {act: 'select'}, function(data){
        $('#listEmail').text(data[3].id + " - " + data[3].nome + " - " + data[3].email);
    }) // END > $.getJSON
    
}); // END > document.ready()