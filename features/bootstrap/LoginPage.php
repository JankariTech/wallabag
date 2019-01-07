<?php

use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class LoginPage extends Page {
    
    protected $path = '/login';
    
    protected $usernameFieldName = 'username';
    protected $passwordFieldName = 'password';
    protected $submitBtnXpath = '//form/div/button';
    protected $toastConatinerID = 'toast-container';
    
    public function login(Session $session, $username, $password) {
        $page = $session->getPage();
        $page->fillField($this->usernameFieldName, $username);
        $page->fillField($this->passwordFieldName, $password);
        $page->find('xpath', $this->submitBtnXpath)->click();
    }
    
    public function getError(Session $session){
        return $session->getPage()->findById($this->toastConatinerID)->getText();
    }
}