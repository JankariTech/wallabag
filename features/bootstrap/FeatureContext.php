<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\RawMinkContext; 
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    /**
     * @When the user adds a new entry with the url :link
     */
    public function addEntry($link)
    {
        $page=$this->getSession()->getPage();
        $page->findById('nav-btn-add')->click();
        $page->fillField('entry_url', $link);
        $page->findButton('add')->click();
        
    }

    /**
     * @Then an entry should be listed in the list with the title :title and the link description :description
     */
    public function entryShouldBeListed($title, $description)
    {
        $page=$this->getSession()->getPage();
        $contentLocation = $page->find('xpath', '//div/ul//div');
        $actualTitle = $contentLocation->find('css', '.card-content')->find('xpath', '/a')->getText();
        expect($actualTitle)->toBe($title);
        $actualDescription = $contentLocation->find('css', '.metadata')->find('xpath', '/a')->getText();
        expect($actualDescription)->toBe($description);
    }

    /**
     * @Then the count of unread entries should be :num
     * @Given the list of unread entries is :num
     */
    public function theCountOfUnreadEntries($num)
    {
        $page=$this->getSession()->getPage();
        $unread = $page->find('xpath','//ul[@id="slide-out"]//a[contains(text(),"Unread")]/span')->getHtml();
        expect($unread)->toBe($num);
    }

    /**
     * @Given the user has browsed to the login page
     */
    public function visitLogIn()
    {
        $this->visitPath("/login");
    }

    /**
     * @When the user logs in with username :username and password :password
     * @Given user has logged in with username :username and password :password
     */
    public function logIn($username, $password)
    {
        $page = $this->getSession()->getPage();
        $page->fillField('username', $username);
        $page->fillField('password', $password);
        $page->find('xpath', '//form/div/button')->click();
    }

    /**
     * @Then the user should be redirected to a page with the title :pageTitle
     */
    public function redirectToPage($pageTitle)
    {
        $pageHeading = $this->getSession()->getPage()->find('xpath', '//title');
        $title = trim($pageHeading->getHtml());
        $title = str_replace("\n", "", $title);
        expect($title)->toBe($pageTitle);
    }

    /**
     * @Then an error message should be displayed saying :errorMessage
     */
    public function errorMessage($errorMesage)
    {
        $toastMessage = $this->getSession()->getPage()->findById('toast-container')->getText();
        expect($toastMessage)->toBe($errorMesage);
    }
    
    /** @BeforeScenario */
    public function clearAllItemsBeforeScenario(BeforeScenarioScope $scope)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/oauth/v2/token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "grant_type=password&client_id=1_5lo5ugrdtyg4sgwcsk0wo4ogsws04ccokcw4ss8w0ggkkso00w&client_secret=3tgtp9ry6q80wokg84okcscwgsws8kwog4kgkc000s8kc848ks&username=admin&password=admin");
        $output = curl_exec($ch);
        $outputArray = json_decode($output, true);
        $accessToken = $outputArray['access_token'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://localhost/api/entries.json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . $accessToken
        ));
        $output = curl_exec($ch);
        $outputArray = json_decode($output, true);
        foreach ($outputArray["_embedded"]["items"] as $item) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://localhost/api/entries/".$item["id"].".json");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization:Bearer ' . $accessToken
            ));
            $output = curl_exec($ch);
        } 
    }
    
    /**
     * @Given there is an entry listed in a list with the title :arg1 and the link description :arg2
     */
    public function thereIsAnEntryListedInAListWithTheTitleAndTheLinkDescription($arg1, $arg2)
    {
        throw new Exception();
    }
    
    /**
     * @When the user deletes the item with the title :arg1
     */
    public function theUserDeletesTheItemWithTheTitle($arg1)
    {
        throw new Exception();
    }
    
    /**
     * @Then an entry with the title :arg1 and the link description :arg2  should not be listed in the list
     */
    public function anEntryWithTheTitleAndTheLinkDescriptionShouldNotBeListedInTheList($arg1, $arg2)
    {
        throw new Exception();
    }
    
    /**
     * @Then count of unread entries is :arg1
     */
    public function countOfUnreadEntriesIs($arg1)
    {
        throw new Exception();
    }
    
    /**
     * @Given there is listed in the list with the title :arg1 and the link description :arg2
     */
    public function thereIsListedInTheListWithTheTitleAndTheLinkDescription($arg1, $arg2)
    {
        throw new Exception();
    }
    
    /**
     * @Given count of entries is :arg1
     */
    public function countOfEntriesIs($arg1)
    {
        throw new Exception();
    }
    
    /**
     * @When the user click on delete button
     */
    public function theUserClickOnDeleteButton()
    {
        throw new Exception();
    }
    
    /**
     * @When user press Cancel button on popup
     */
    public function userPressCancelButtonOnPopup()
    {
        throw new Exception();
    }
    
    /**
     * @Then an entry with the title :arg1 and the link description :arg2  should be listed in the list
     */
    public function anEntryWithTheTitleAndTheLinkDescriptionShouldBeListedInTheList($arg1, $arg2)
    {
        throw new Exception();
    }
    
}
