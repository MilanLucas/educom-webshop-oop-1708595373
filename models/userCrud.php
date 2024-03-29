<?php
class UserCrud{
  private $crud;

  public function __construct($crud){
    $this -> crud = $crud;
  }

  public function createUser($email, $user, $password){
    $sql = "INSERT INTO users (email, user, password)
  VALUES (:email, :user, :password)";
  $params = array('email' => $email, 'user'=>$user, 'password'=>$password);
  try {
    $id = $this->crud->createRow($sql, $params);
  } catch (PDOException $e) {
    // gotta change this to rethrowing the error message
    // left overs from testing
    var_dump($e->getMessage());
  }
  }

  public function updateUserPassword($email, $password){
    $sql = 'UPDATE users 
    SET password=:password
    WHERE email=:email';
    $params = array('email' => $email, 'password' => $password);
    try {
      $this->crud->updateRow($sql, $params);
    } catch (PDOException $e) {
      var_dump($e->getMessage());
    }
  }

  public function readUserByEmail($email){
    $sql = 'SELECT * FROM users WHERE email=:email';
    $params = array('email' => $email);
    try {
      $data = $this->crud->readOneRow($sql, $params);
    } catch (PDOException $e) {
      var_dump($e->getMessage());
    }
    return $data;
  }

}

?>