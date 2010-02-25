/*
 * @Author Alexandre Santos
 */

$(document).ready(function(){

    // RETORNA A LISTA ORDENADA DE E-MAILS ATRÁVES DO BANCO DE DADOS
    $.getJSON("email.action.php", {act: 'select'}, function(data){
        $.each(data, function(i){
            $('#listEmail ul').append("<li id='" + data[i].id + "'>"  + data[i].nome + " - " + data[i].email + "<br />" + "</li>");
        }) // END > $.each()
    }) // END > $.getJSON


    // CADASTRA UM NOVO NOME E SENHA
    $("input[type='submit']").click(function(){
        var nome = $("#nome").val();
        var email = $("#email").val();

        $.post("email.action.php", {act: 'insert', nome: nome, email: email},
        function(data){
            $('#listEmail ul').append("<li id='" + data.id + "' style='display:none'>"  + data.nome + " - " + data.email + "<br />" + "</li>");
            $('#' + data.id).fadeIn("slow");
        }, "json");

    }) // END > $("input[type='sumit']").click()


}); // END > document.ready()