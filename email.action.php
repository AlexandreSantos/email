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

/**
 * Cria a conexão com o banco de dados.
 * procure sua connectionString PDO (http://www.php.net/manual/pt_BR/pdo.drivers.php)
 */
$dns = "";
$user = "";
$passwd = "";

$seqNameEmail = "email_seq"; // Armazena o nome do objeto sequência do PostgreSQL


try{
    $pdopg = new PDO($dns, $user, $passwd,  array(PDO::ATTR_PERSISTENT => true));
}catch (PDOException $pdoE){
    print "erro! -> " . $pdoE->getMessage() . "<br />";
    $resultado = $pdoE->getMessage();
}


/**
 * Recebe o 'act' via POST ou GET e executa o código selecionado por essa flag
 */

$act = $_REQUEST['act'];

/**
 * Se a ação for cadastrar, faz o INSERT e retorna um objeto JSON da linha
 * cadastrada no banco de dados, exemplo do JSON:
 * [{"id":1,"nome":"Alexandre Santos","email":"alexandre@diariodecodigos.info"}]
 */
if($act == "insert"){
    $nome = $_REQUEST['nome'];
    $email = $_REQUEST['email']; 

    try{
        $pdopg->beginTransaction(); // Inicia transação

        $stmt = $pdopg->prepare("INSERT INTO email(nome, email) VALUES(:nome, :email)"); // Prepara a query sql
        $pdopg->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // adicionar os valores dos parâmetros
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);

        $stmt->execute(); // Executa a query
        $pdopg->commit(); // faz commit da transação
    }catch (PDOException $e) {
        /**
         * Se algo der errado
         */
        $pdopg->rollBack(); // Faz rollback
        $resultado = $e->getMessage(); // Armazena a mensagem de erro na variável $resultado
        $resultJson = array('resultado' => $resultado); // coloca em JSON
        header('Content-type: application/json'); // formata o cabeçalho para o tipo JSON
        echo json_encode($resultJson); // retorna o JSON com a mensagem de erro
        die();
    }


    try{
        $lastId = $pdopg->lastInsertId($seqNameEmail); // pega o valor do ID do último INSERT

        $stmtLast = $pdopg->prepare("SELECT id, nome, email FROM email WHERE id = :id");
        $stmtLast->bindParam(':id', $lastId);
        $stmtLast->execute();

        $resultStmt = $stmtLast->fetchAll(PDO::FETCH_ASSOC);
        $resultArr = Array('id' => $resultStmt[0]['id'],
                           'nome' => $resultStmt[0]['nome'],
                           'email' => $resultStmt[0]['email']);

        header('Content-type: application/json');
        echo json_encode($resultArr);
    }catch (PDOException $e){
        $resultArr = Array('id' => 0,
                           'nome' => $e->getMessage(),
                           'email' => 'Falha!');
        header('Content-type: application/json');
        echo json_encode($resultArr);
    }
    
    
}// FIM > act = insert


/**
 * Se a ação for 'SELECT' busca todos os registros do banco de dados
 * e retorna um valor JSON.
 */
if($act == "select"){
    try{
        //$arrJSON = array();
        $pdostmt = $pdopg->prepare("SELECT id, nome, email FROM email");
        $pdostmt->execute();
        $arrResult = $pdostmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-type: application/json');
        echo json_encode($arrResult);
    }catch (PDOException $e){
        echo "ERro!: " . $e->getMessage();
    }
} // FIM > act = select


?>