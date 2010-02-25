<?php
 /**
  * Este arquivo recebe comandos remotamente e retorna os resultados em JSON
  * para ser tratados em JavaScript.
  *
  * As ações são enviadas via GET ou POST para a variável $act que podem ter os valores:
  *   - INSERT
  *   - SELECT
  *
  * @package email
  * @copyright Copyright (c) Alexandre Santos
  * @license http://www.opensource.org/licenses/gpl-3.0.html GNU Public License 3.0
  * @version 1.0
  * @author Alexandre Santos <alexandre@diariodecodigos.info>
  *
  */

// Cria a conexão com o banco de dados antes de tudo
$dns = "";
$user = "";
$passwd = "";

try{
    $pdopg = new PDO($dns, $user, $passwd,  array(PDO::ATTR_PERSISTENT => true));
}catch (PDOException $pdoE){
    print "erro! -> " . $pdoE->getMessage() . "<br />";
    $resultado = $pdoE->getMessage();
}
 
$act = $_REQUEST['act'];

// Se a ação for 'cadastrar' executa o bloco abaixo
if($act == "cadastrar"){
    $nome = $_REQUEST['nome'];
    $email = $_REQUEST['email'];

    $resultado = "Sucesso!"; 

    try{
        $pdopg->beginTransaction(); // Inicia transação

        $stmt = $pdopg->prepare("INSERT INTO emil(nome, email) VALUES(:nome, :email)"); // Prepara a query sql
        $pdopg->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // adicionar os valores dos parâmetros
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);

        $stmt->execute(); // Executa a query preparada no $stmt
        $pdopg->commit(); // faz commit da transação
    }catch (Exception $e) {
        /**
         * Se algo der errado
         */
        $pdopg->rollBack(); // Faz rollback
        $resultado = $e->getMessage(); // Armazena a mensagem de erro na variável $resultado
        $resultJson = array('resultado' => $resultado); // coloca em JSON
        header('Content-type: application/json'); // formata o cabeçalho para o tipo JSON
        echo json_encode($resultJson); // retorna o JSON com a mensagem de erro
    }

    // Prepara o JSON para ser devolvido
    $resultJson = array('resultado' => $resultado);
    //header('Content-type: application/json');
    echo json_encode($resultJson);
    
}// FIM > act = cadastrar

if($act == "select"){
    try{
        //$arrJSON = array();
        $pdostmt = $pdopg->prepare("SELECT id, nome, email FROM email");
        $pdostmt->execute();

        //echo $pdostmt->rowCount();

       $arrResult = $pdostmt->fetchAll(PDO::FETCH_ASSOC);
       echo json_encode($arrResult);


    }catch (PDOException $e){
        echo "ERro!: " . $e->getMessage();
    }
}


?>