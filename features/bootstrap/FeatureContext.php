<?php

use Behat\Behat\Context\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

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
    protected $footerBarXPath = "//div[contains(@style,'display: block') and contains(@id,'sfToolbarMainContent')]";
    protected $footerHideButton = "//a[contains(@class,'hide-button')]";
    
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
     * Add a new entry with given url parameter 
     * @When the user adds a new entry with the url :link
     * @Given the user has added a new entry with the url :link
     * @param string $link
     * 
     * @return void
     */
    public function addEntry($link)
    {
        $this->unreadPage->addNewEntry($this->getSession(),$link);
    }
    
    /**
     * Expect added entry with correct title and link description as given in feature file
     * @Then an entry should be listed in the list with the title :title and the link description :description
     * @param string $description
     * @param string $title
     * 
     * @return void
     */
    public function entryShouldBeListed($title, $description)
    {
        $isEntryListed = $this->unreadPage->isEntryListed($this->getSession(),$title,$description);
        expect($isEntryListed)->toBe(true);
    }
    
    /**
     * Expect correct number of unread entries as expected in feature file
     * @Then the count of unread entries should be :num
     * @Given the list of unread entries is :num
     * 
     * @param integer $num
     * 
     * @return void
     */
    public function theCountOfUnreadEntries($num)
    {
        $unread = $this->unreadPage->countUnreadEntry($this->getSession());
        expect($unread)->toBe($num);
    }
    
    /**
     * Open the login page.
     * @Given the user has browsed to the login page
     * 
     * @return Page
     */
    public function visitLogIn()
    {
        $this->loginPage->open();
        var_dump($this->getSession()->getDriver()->getWebDriverSession()->getUrl());
    }
    
    /**
     * Logs in with given username and password as given in feature file.
     * @When the user logs in with username :username and password :password
     * @Given user has logged in with username :username and password :password
     * 
     * @param string $username
     * @param string $password
     * 
     * @return void
     */
    public function logIn($username, $password)
    {
       $this->loginPage->login($this->getSession(), $username, $password);
    }
    
    /**
     * Logs in as super admin
     * @When the user logs in as super admin
     * @Given user has logged in as super admin
     * 
     * @return void
     */
    public function logInSuperAdmin()
    {
        $this->loginPage->login($this->getSession(), $this->username, $this->password);
    }
    
    /**
     * Expect the title of the page as provided in feature file.
     * @Then the user should be redirected to a page with the title :pageTitle
     * 
     * @param string $pageTitle
     * 
     * @return void
     */
    public function checkPageTitle($pageTitle)
    {
        $title = $this->quickStartPage->checkTitle($this->getSession());
        expect($title)->toBe($pageTitle);
    }
    
    /**
     * Expect correct error message as login fails
     * @Then an error message should be displayed saying :errorMessage
     * 
     * @param string $errorMessage
     * 
     * @return void
     */
    public function errorMessage($errorMessage)
    {
        $error = $this->loginPage->getError($this->getSession());
        expect($error)->toBe($errorMessage);
    }
    
    /**
     * Delete entry with given title as in feature file
     * @When the user deletes the item with the title :title
     * @param string $title
     * 
     * @return void
     */
    public function deleteItem($title)
    {
        $this->unreadPage->deleteEntry($this->getSession(), $title);
    }
    
    /**
     * Cancel the delete entry
     * @When user press cancel button on popup after pressing delete button for title :title
     * 
     * @param string $title
     */
    public function userPressCancel($title)
    {
        $this->unreadPage->cancelDelete($this->getSession(), $title);
    }
    
    /**
     * Check and Expect the entry with given title and description is lited in unread entries list
     * @Then there should not be entry in list with title :title and the link description :description
     * 
     * @param string $title
     * @param string $description
     * 
     * @return void
     */
    public function thereShouldNotBeEntryInListWithTitle($title,$description)
    {
        expect($this->unreadPage->isEntryListed($this->getSession(), $title, $description))->toBe(false);
    }
    
    /**
     * Get API Client Id and Client Secret 
     * @BeforeScenario
     * 
     * @param BeforeScenarioScope $scope
     * 
     * @return void
     */
    public function  getApiClient(BeforeScenarioScope $scope){
        if ( getenv('API_CLIENT_ID') == false && getenv('CLIENT_SECRET') == false){
            $this->visitLogIn();
            $this->logIn($this->username,$this->password);
            $this->apiClientPage->open();
            $this->apiClientPage->createClient($this->getSession(), $this->username);
            $apiClientId=$this->apiClientPage->getClientId($this->getSession());
            $apiClientSecret=$this->apiClientPage->getClientSecret($this->getSession());
            putenv("API_CLIENT_ID=$apiClientId");
            putenv("CLIENT_SECRET=$apiClientSecret");
        }
    }
    
    /**
     * Hide the footer bar 
     * @BeforeScenario
     * @param BeforeScenarioScope $scope
     * 
     * @return void
     */
    public function hideFooter(BeforeScenarioScope $scope){
        $footerBar = $this->getSession()->getPage()->find('xpath', $this->footerBarXPath);
        if ($footerBar != Null){
            $this->getSession()->getPage()->find('xpath', $this->footerHideButton)->click();
        }
    }
    
    /**
     * Clear All the items Before Scenario 
     * @BeforeScenario  
     * @param BeforeScenarioScope $scope
     * 
     * @return void
     */
    public function clearAllItemsBeforeScenario(BeforeScenarioScope $scope){
        $ch = curl_init();
        $SERVER_URL = $this->getMinkParameter("base_url");
        curl_setopt($ch, CURLOPT_URL, "$SERVER_URL/oauth/v2/token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            'grant_type=password&client_id='.getenv('API_CLIENT_ID').
            '&client_secret='.getenv('CLIENT_SECRET').'&username='. $this->username. 
            '&password='.$this->password);
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
    
    
}
