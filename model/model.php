<?php
class model{
    //error properties
    public bool $errorUnique;
    public bool $errorQueue = false;
    // json properties
    public ?string $username;
    public string $chat_id;
    public ?string $message_id;
    public string $name;
    public string $text;
    //core properties
    public bool $signup;
    public bool $setAdmin;
    public bool $home;
    public bool $addRequest;
    public bool $deleteRequest;
    public string $downloadRequest;
    public array $seeMessage;

    public  function __construct($obj) {
        $conn = model::connection($this);
        $this->chat_id = $obj->chat_id;
        $this->name = $obj->name;
        $this->username = $obj->username;
        $this->text = $obj->text;
        $this->message_id = $obj->message_id ?? NULL;
        $this->parser($obj,$conn);
    }
    public static function connection($obj){
        try {
            $conn = new PDO("mysql:host=".SERVERNAME.";dbname=".DBNAME, USERNAME, PASSWORD);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            Controller::ErrorHandlling($obj,$e);
        }
    }

    private function parser($obj,$conn){
        if(isset($obj->home)){$this->checkAccount($conn);return;}
        if(isset($this->adminToken)){$this->setAdmin($conn);return;}
        if(isset($obj->signup)){$this->signup($obj,$conn);return;}
        //replied text request
        if(isset($obj->addRequest)){$this->queueAdd($conn);return;}
        if(isset($obj->deleteRequest)){$this->queueDelete($conn);return;}
        //message after inline keyboard
        if(isset($obj->seeMessage)){$this->seeMessage($conn);return;}
    }

    //query for admin registration
    private function setAdmin($conn) {
        $sql = "UPDATE users SET admin = 1 WHERE chat_id = :chat_id";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":chat_id",$this->chat_id);
            $stmt->execute();
            $this->setAdmin = true;
        }catch (PDOException $e){
            controller::ErrorHandlling($this,$e);
        }
    }
    //query for commands
    private function checkAccount($conn) {
        // check if they have signed up
        $sql = "SELECT * FROM users WHERE chat_id=:chat_id";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":chat_id",$this->chat_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result != NULL){
                $this->home = true;
            }else{
                $this->home = false;
            }
        }catch (PDOException $e){
            controller::ErrorHandlling($this,$e);
        }
    }




    //query for inline input's
    private function signup($obj, $conn) {
        try {
            $sql = "SELECT * FROM users WHERE chat_id=:chat_id";
            $firstStmt = $conn->prepare($sql);
            $firstStmt->bindParam(":chat_id", $this->chat_id);
            $firstStmt->execute();
            $user = $firstStmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $sql = "INSERT INTO users (username, name, chat_id)
                        VALUES (:username, :name, :chat_id);";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':username', $this->username);
                $stmt->bindParam(':name', $this->name);
                $stmt->bindParam(':chat_id', $this->chat_id);
                $this->signup = (bool)$stmt->execute();
                $this->errorUnique = false;
            } else {
                $this->signup = false;
                $this->errorUnique = true;
            }
        } catch (PDOException $e) {
            Controller::ErrorHandlling($obj,$e);
        }
    }
    private function seeMessage($conn){
        try {
            $sql = "SELECT queue1,queue2,queue3 FROM users WHERE chat_id=:chat_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":chat_id", $this->chat_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result['queue1'] == NULL && $result['queue2'] == NULL && $result['queue3'] == NULL){
                $this->seeMessage = ["NULL"];
                $this->errorQueue = true;
            }else{
                $this->seeMessage = $result;
            }
        }catch (PDOException $e){
            Controller::ErrorHandlling($this,$e);
        }
    }






    //query for reply message's
    private function queueAdd($conn) {
        $sql = "SELECT queue1,queue2,queue3 FROM users WHERE chat_id=:chat_id";
        try {
            $stmt =$conn->prepare($sql);
            $stmt->bindParam(":chat_id",$this->chat_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result['queue1'] == NULL or $result['queue2'] == NULL or $result['queue3'] == NULL){
                $sql1 = $result['queue1'] == NULL?"UPDATE users SET queue1=:queue1 WHERE chat_id=:chat_id":false;
                $sql2 = $result['queue2'] == NULL?"UPDATE users SET queue2=:queue2 WHERE chat_id=:chat_id":false;
                $sql3 = $result['queue3'] == NULL?"UPDATE users SET queue3=:queue3 WHERE chat_id=:chat_id":false;
                switch (true){
                    case $sql1 != false:
                        $stmt = $conn->prepare($sql1);
                        $stmt->bindParam(":queue1",$this->text);
                        $stmt->bindParam(":chat_id",$this->chat_id);
                        $stmt->execute();
                        break;
                    case $sql2 != false:
                        $stmt = $conn->prepare($sql2);
                        $stmt->bindParam(":queue2",$this->text);
                        $stmt->bindParam(":chat_id",$this->chat_id);
                        $stmt->execute();
                        break;
                    case $sql3 != false:
                        $stmt = $conn->prepare($sql3);
                        $stmt->bindParam(":queue3",$this->text);
                        $stmt->bindParam(":chat_id",$this->chat_id);
                        $stmt->execute();
                        break;
                }
                $this->addRequest = true;
            }else{
                $this->addRequest = false;
            }
        }catch (PDOException $e){
            controller::ErrorHandlling($this,$e);
        }
    }

    private function queueDelete($conn) {
        $sql = "SELECT queue1,queue2,queue3 FROM users WHERE chat_id=:chat_id";
        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":chat_id",$this->chat_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            foreach ($result as $key => $value){
                if ($value == $this->text){
                    $sql = "UPDATE users SET ".$key."= NULL WHERE chat_id =".$this->chat_id;
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $this->deleteRequest = true;
                }
            }
            if (empty($this->deleteRequest)){
                $this->deleteRequest = false;
            }
        }catch (PDOException $e){
            controller::ErrorHandlling($this,$e);
        }
    }
}