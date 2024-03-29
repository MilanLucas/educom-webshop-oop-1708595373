<?php
require_once('PageModel.php');
require_once('Validators.php');

class UserModel extends PageModel{
  public $meta = array();
  public $values = array();
  public $errors = array();
  private $userId = 0;
  public $valid = false;

  public function __construct($pageModel){
    PARENT::__construct($pageModel);
  }

  public function getMeta(){
    //Okay, this is gonna get scattered with comments as I hone down on what I want to do
    // For each form type, I want a meta array that contains the info needed for formDoc to display + what validations its need
    // So that means an array of IDs
    // each id needs to get what to the form? label, type, and content, and what kind of validation it requires 
    // Four flags, whether or not it is empty (and a valid option on select), dependant on select, or dependant on group (I'll do that one later)
    // Also means I can let the error messages depend on validation flags

    // should make this a case
    switch ($this->page){
      case 'contact':
        $this->meta = array(
          'title' => array('label' => 'Titel',   'type' => 'select', 'options' => TITLES, 'validations' => array('notEmpty', 'validOption')),
          'name' => array('label' => 'Naam',   'type' => 'text', 'validations' => array('notEmpty')),
          'email' => array('label' => 'Email',   'type' => 'text', 'validations' => array('notEmptyIf:contact:email', 'validEmail')),
          'phonenumber' => array('label' => 'Telefoonnummer',   'type' => 'text', 'validations' => array('notEmptyIf:contact:phone')),
          'street' => array('label' => 'Straat',   'type' => 'text','group' => 'address-one', 'validations' => array('notEmptyIf:contact:post',"notEmptyGroup:addres-one")),
          'housenumber' => array('label' => 'Huisnummer',   'type' => 'text', 'group' => 'address-one', 'validations' => array('notEmptyIf:contact:post', "notEmptyGroup:addres-one")),
          'postalcode' => array('label' => 'Postcode',  'type' => 'text', 'group' => 'address-one', 'validations' => array('notEmptyIf:contact:post', "notEmptyGroup:addres-one")),
          'city' => array('label' => 'Woonplaats',   'type' => 'text', 'group' => 'address-one', 'validations' => array('notEmptyIf:contact:post', "notEmptyGroup:addres-one")),
          'communication' => array('label' => 'Communicatie voorkeur',   'type' => 'select', 'options' => COMMUNICATIONS, 'validations' => array('notEmpty', 'validOption')),
          'message' => array('label' => 'Reden van contact',   'type' => 'textarea', 'options' =>  array('rows' => 4, 'cols' => 50), 'validations' => array('notEmpty')),
        );
        break;
      case 'login':
        $this->meta = array(
          'email' => array('label' => 'Email', 'type' => 'text', 'validations' => array('notEmpty', 'validEmail')), 
          'password' =>  array('label' => 'Wachtwoord', 'type' => 'text', 'validations' => array('notEmpty')), 
        );
        break;
      case 'register':
      $this->meta = array(
        'user' =>  array('label' => 'Gebruikersnaam', 'type' => 'text', 'validations' => array('notEmpty')), 
        'email' => array('label' => 'Email', 'type' => 'text', 'validations' => array('notEmpty', 'validEmail')), 
        'password' =>  array('label' => 'Wachtwoord', 'type' => 'text', 'validations' => array('notEmpty', 'matchWith:repeat')), 
        'repeat' =>  array('label' => 'Herhaal wachtwoord', 'type' => 'text', 'validations' => array('notEmpty')),
        );
        break;
      case 'password':
        $this->meta = array(
          'password' => array('label' => 'Oud wachtwoord', 'type' => 'text', 'validations' => array('notEmpty')), 
          'newPass' => array('label' => 'Nieuw wachtwoord', 'type' => 'text', 'validations' => array('notEmpty', 'matchWith:newRepeatPass')), 
          'newRepeatPass' => array('label' => 'Herhaal nieuw wachtwoord', 'type' => 'text', 'validations' => array('notEmpty'))
        );
      break;
    }
  }

  public function getInputs(){
    // a post request on a form that hasn't been filled will just return blanks
    foreach (array_keys($this->meta) as $key){
      $this->values[$key] = $this->getPostVar($key);
    }
  }

  public function getErrors(){
    // So if its not a post we still want to mark required posts for the user with a *
    if ($this->page == 'contact'){
      $this->errors =array('title' => '*', 'name' => '*', 'communication' => '*', 'message' => '*');
    } elseif ($this->page == 'login'){
      $this->errors = array('email' => '*', 'password' => '*');
    } elseif ($this->page == 'register'){
      $this->errors = array('name'=>'*', 'email' => '*', 'password' => '*', 'repeat' => '*');
    }else {
      $this->errors = array('password' => '*', 'newPass' => '*', 'newRepeatPass' => '*');
    }
    foreach (array_keys($this->meta) as $key){
      if(!isset($this->errors[$key])){
        $this->errors[$key] = '';
      }
    }

    // a more thorough check is only necessary if it is a POST request
    if ($this->isPost){
      $this->errors = Validators::validateInputs($this->page, $this->meta, $this->values, $this->errors);
    }
  }

  public function validateLogin(){
    $this->getMeta();
    $this->getInputs();
    $this->getErrors();
    if (!$this->errors['valid']){
      return;
    } else {
      $this->authUser($this->values['email']);
    }
  }

  private function authUser($email){
    $userInfo = $this->crud->readUserByEmail($email);
    //userInfo is only null if there was an error in the database
    // otherwise its an array
    if (!isset($userInfo)){
      return $userInfo;}
    //check if password overlaps with the password in $userInfo
    if ($this->passwordDecrypt($this->values['password'], $userInfo->password)){ 
      $this->values['name'] = $userInfo->user;
      $this->userId = $userInfo->id;
      $this->valid = true;
    } else {
      $this->errors['email'] = '*Email of wachtwoord is incorrect';
    }
  }

  private function passwordDecrypt($password, $hash){
    return password_verify($password, $hash);
  }

  private function passwordEncrypt($password){
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 14]);
  }

  public function doLoginUser(){
    $this->sessionManager->doLoginUser($this->values['name'], $this->values['email'], $this->userId);
  }

  public function doLogoutUser(){
    $this->sessionManager->doLogoutUser();
  }

  public function doUpdatePassword(){
    // gotta authenticate user to check if the new password is correct
    $email = $this->sessionManager->getLoggedInUser()['email'];
    $this->authUser($email);
    if($this->valid){
      $password = self::passwordEncrypt( $this->values['newPass']);
      $this->crud->updateUserPassword($email, $password);
      $this->errors['password'] = 'wachtwoord geupdate';
    } else {
      $this->errors['password'] = 'wachtwoord incorrect';
    }
  }

  public function doRegisterUser(){
    $this->values['email'];
    $this->values['user'];
    $this->values['password'];
    if(!$this->crud->readUserByEmail($this->values['email'])){
      self::saveUser();
      $this->setPage('home');
    } else {
      $this->errors['email'] = 'Deze email is al geregisteerd';
    }
  }

  private function saveUser(){
    $password = self::passwordEncrypt($this->values['password']);
    $this->crud->createUser($this->values['email'], $this->values['user'], $password);
  }

}
?> 