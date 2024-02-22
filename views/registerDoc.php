<?php
require_once('formDoc.php');
class RegisterDoc Extends FormDoc{
 protected function showTitleContent(){
   echo 'Register page'; 
 }

 protected function showHeaderContent(){
   echo 'Register'; 
 }

 protected function showContent(){
     $this->formStart();
     $this->formContent();
     $this->submitButton();
     $this->formEnd();
 }

 protected function formStart(){
  echo '
  <form class="contact" method="POST" action="index.php">
  <input type="hidden" name="page" value="register" id="page"/>
  <fieldset class="persoon">';
 }

  protected function formContent(){
    $formInputs = $this=>getData()['formInputs'];
    echo '
    <div> 
    <label for="name"> Naam:</label> 
    <input type="text" name="name" value="'.$formInputs['name'].'" id="name">
    <span class="error">* '.$formInputs['nameErr'].'</span>
    </div>
    <div> 
    <label for="email"> Email:</label> 
    <input type="text" name="email" value="'.$formInputs['email'].'" id="email">
    <span class="error">* '.$formInputs['emailErr'].'</span>
    </div>
    <div> 
    <label for="password"> Wachtword:</label> 
    <input type="text" name="password" value="'.$formInputs['password'].'" id="password">
    <span class="error">* '.$formInputs['passwordErr'].'</span>
    </div>
    <div> 
    <label for="repeat"> Herhaal het wachtword:</label> 
    <input type="text" name="repeat" value="'.$formInputs['repeat'].'" id="repeat">
    <span class="error">* '.$formInputs['repeatErr'].'</span>
    </div>';
  }

}
?>