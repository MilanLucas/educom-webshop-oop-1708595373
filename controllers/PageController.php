<?php
require_once('models/PageModel.php');
require_once('models/UserModel.php');
require_once('models/ShopModel.php');

class PageController{
  private $model;

  public function __construct() {
    $this->model = new PageModel(NULL);
  }

  public function handleRequest(){
    $this->getRequest();
    $this->processRequest();
    $this->showResponse();
  }

  private function getRequest(){
    $this->model->getRequestedPage();
  }

  public function logErrors($msg){
    echo "LOG TO SERVER:".$msg;
  }

  private function processRequest(){
    switch($this->model->page){  
      case 'contact':
        $this->model = new UserModel($this->model);
        $this->model->getMeta();
        $this->model->getInputs();
        $this->model->getErrors();
        if(isset($this->model->errors['page'])){
          $this->model->setPage('thank');
        }
        break;
      case 'webshop':
        $this->model = new ShopModel($this->model);
        $this->model->getWebShopData();
        break;
      case 'top':
        $this->model = new ShopModel($this->model);
        $this->model->getTopFiveData();
        break;
      case strstr($this->model->page, 'product'):
        $this->model = new ShopModel($this->model);
        $this->model->getDetailData();
        break;
      case 'login':
        $this->model = new UserModel($this->model);
        $this->model->validateLogin();
        if($this->model->valid){
          //otherwise a correct password stays afloat in the data
          $this->model->values['password'] = '';
          $this->model->doLoginUser();
          $this->model->setPage('home');
        }
        break;
      case 'register':{
        $this->model = new UserModel($this->model);
        $this->model->getMeta();
        $this->model->getInputs();
        $this->model->getErrors();
        if($this->model->errors['valid']){
          $this->model->doRegisterUser();
        }
        break;
      }
      case 'password':
        $this->model = new UserModel($this->model);
        $this->model->getMeta();
        $this->model->getInputs();
        $this->model->getErrors();
        if($this->model->errors['valid']){
          $this->model->doUpdatePassword();
        }
        break;
      case'logout':
        $this->model = new UserModel($this->model);
        $this->model->doLogoutUser();
        $this->model->setPage('home');
        break;
    }
  }

  private function showResponse(){
    $this->model->createMenu();
    switch($this->model->page){
      case 'home':
        require_once('views/homeDoc.php');
        $view = new HomeDoc($this->model);
        break;
      case 'about':
        require_once('views/aboutDoc.php');
        $view = new AboutDoc($this->model);
        break;
      case 'contact':
        require_once('views/contactDoc.php');
        $view = new ContactDoc($this->model);
        break;
      case 'thank':
        require_once('views/thanksDoc.php');
        $view = new ThanksDoc($this->model);
        break;
      case 'webshop':
        require_once('views/webshopDoc.php');
        $view = new WebshopDoc($this->model);
        break;
      case 'top':
        require_once('views/top5Doc.php');
        $view = new Top5Doc($this->model);
        break;
      case strstr($this->model->page, 'product'):
        require_once('views/detailDoc.php');
        $view = new DetailDoc($this->model);
        break;
      case 'register':{
        require_once('views/registerDoc.php');
        $view = new RegisterDoc($this->model);
        break;
      }
      case 'login':
        require_once('views/loginDoc.php');
        $view = new LoginDoc($this->model);
        break;
      case 'password':
        require_once('views/passwordDoc.php');
        $view = new PasswordDoc($this->model);
        break;
    }
    $view->show();
  }


}


?>