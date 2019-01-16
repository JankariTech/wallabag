<?php 
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use Behat\Mink\Session;

class APIClientsPage extends Page{
    protected $path= "/developer/client/create";
    protected $clientNameInputId="client_name";
    protected $createNewClientButtonId="client_save";
    protected $clientName='admin';
    protected  $clientsecreteXpath="//li[contains(text(),'Client secret: ')]/strong";
    protected  $clientIdXpath="//li[contains(text(),'Client ID: ')]/strong";
    
    /**
     * create a new client
     * @param Session $session
     * @param string $clientName
     */
    
    public function createClient(Session $session,$clientName){
        $page = $session->getPage();
        $page->fillField( $this->clientNameInputId,$clientName);
        $page->findById($this->createNewClientButtonId)->click();   
    }
    
    /**
     * get APIclient id
     * @param Session $session
     * @return string
     */
    public function  getClientId(Session $session){
        $clientId=$session->getPage()->find('xpath', $this->clientIdXpath)->getText();
        return $clientId;
    }
    
    /**
     *  get APIclient secree key
     * @param Session $session
     * @return string
     */
    public function  getClientSecret(Session $session){
        $clientsecret=$session->getPage()->find('xpath', $this->clientsecreteXpath)->getText();
        return $clientsecret;
    }
}
?>