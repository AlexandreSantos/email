/*
 * @Author Alexandre Santos
 */

$(document).ready(function(){
    
    $.getJSON("email.action.php", {act: 'select'}, function(data){
        $.each(data, function(i){
            $('#listEmail').append(i + " -> " + data[i].id + " - " + data[i].nome + " - " + data[i].email + "<br />");
        }) // END > $.each()
    }) // END > $.getJSON
    
}); // END > document.ready()