<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    
    protected $loginPage;
    protected $quickStartPage;
    protected $unreadPage;
    protected $apiClientPage;
    protected $username;
    protected $password;
    public function __construct(
        LoginPage $loginpage,QuickStartPage $quickStart,UnreadEntriesPage $unreadPage,APIClientsPage $apiClientPage,
        $parameters){
            $this->loginPage = $loginpage;
            $this->quickStartPage = $quickStart;
            $this->unreadPage = $unreadPage;
            $this->apiClientPage= $apiClientPage;
            $this->username=$parameters['adminUserName'];
            $this->password=$parameters['adminPassword'];
    }
    
    /**
     * @When the user adds a new entry with the url :link
     * @Given the user has added a new entry with the url :link
     *
     */
    public function addEntry($link)
    {
        $this->unreadPage->addNewEntry($this->getSession(),$link);
    }
    
    /**
     * @Then an entry should be listed in the list with the title :title and the link description :description
     */
    public function entryShouldBeListed($title, $description)
    {
        $isEntryListed = $this->unreadPage->isEntryListed($this->getSession(),$title,$description);
        expect($isEntryListed)->toBe(true);
    }
    
    /**
     * @Then the count of unread entries should be :num
     * @Given the list of unread entries is :num
     */
    public function theCountOfUnreadEntries($num)
    {
        $unread = $this->unreadPage->countUnreadEntry($this->getSession());
        expect($unread)->toBe($num);
    }
    
    /**
     * @Given the user has browsed to the login page
     */
    public function visitLogIn()
    {
        $this->loginPage->open();
        var_dump($this->getSession()->getDriver()->getWebDriverSession()->getUrl());
    }
    
    /**
     * @When the user logs in with username :username and password :password
     * @Given user has logged in with username :username and password :password
     */
    public function logIn($username, $password)
    {
        $this->loginPage->login($this->getSession(), $username, $password);
    }
    
    /**
     * @When the user logs in as super admin
     * @Given user has logged in as super admin
     */
    public function logInSuperAdmin()
    {
        $this->loginPage->login($this->getSession(), $this->username, $this->password);
    }
    
    /**
     * @Then the user should be redirected to a page with the title :pageTitle
     */
    public function redirectToPage($pageTitle)
    {
        $title = $this->quickStartPage->checkTitle($this->getSession());
        expect($title)->toBe($pageTitle);
    }
    
    /**
     * @Then an error message should be displayed saying :errorMessage
     */
    public function errorMessage($errorMessage)
    {
        $error = $this->loginPage->getError($this->getSession());
        expect($error)->toBe($errorMessage);
    }
    
    /**
     * @BeforeScenario
     */
    public function  getApiClient(BeforeScenarioScope $scope){
        if ( getenv('API_CLIENT_ID') == false && getenv('CLIENT_SECRET') == false){
            $this->visitLogIn();
            $this->logIn($this->username,$this->password);
            $this->apiClientPage->open();
            $this->apiClientPage->createClient($this->getSession(), $this->username);
            $API_CLIENT_ID=$this->apiClientPage->getClientId($this->getSession());
            $apiCLIENT_SECRET=$this->apiClientPage->getClientSecret($this->getSession());
            putenv("API_CLIENT_ID=$API_CLIENT_ID");
            putenv("CLIENT_SECRET=$apiCLIENT_SECRET");
        }
    }
    
    /** @BeforeScenario  */
    public function clearAllItemsAfterScenario(BeforeScenarioScope $scope){
        $ch = curl_init();
        $SERVER_URL = $this->getMinkParameter("base_url");
        curl_setopt($ch, CURLOPT_URL, "$SERVER_URL/oauth/v2/token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            'grant_type=password&client_id='.getenv('API_CLIENT_ID').'&client_secret='.getenv('CLIENT_SECRET').'&username='. $this->username. '&password='.$this->password);
        $output = curl_exec($ch);
        $outputArray = json_decode($output, true);
        $accessToken = $outputArray['access_token'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$SERVER_URL/api/entries.json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . $accessToken
        ));
        $output = curl_exec($ch);
        $outputArray = json_decode($output, true);
        foreach ($outputArray["_embedded"]["items"] as $item) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "$SERVER_URL/api/entries/".$item["id"].".json");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization:Bearer ' . $accessToken
            ));
            $output = curl_exec($ch);
        }
    }
    
    
    /**
     * @When the user deletes the item with the title :title
     */
    public function deleteItem($title)
    {
        $this->unreadPage->deleteEntry($this->getSession(), $title);
    }
    
    /**
     * @When user press cancel button on popup after pressing delete button for title :title
     */
    public function userPressCancel($title)
    {
        $this->unreadPage->cancelDelete($this->getSession(), $title);
    }
    
    /**
     * @Then there should not be entry in list with title :title and the link description :description
     */
    public function thereShouldNotBeEntryInListWithTitle($title,$description)
    {
        expect($this->unreadPage->isEntryListed($this->getSession(), $title, $description))->toBe(false);
    }
}
