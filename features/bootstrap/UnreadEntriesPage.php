<?php
use Behat\Mink\Session;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class UnreadEntriesPage extends Page{
    protected $path = '/unread/list';
    protected $unreadCountXPath = '//ul[@id="slide-out"]//a[contains(text(),"Unread")]/span';
    protected $listEntriesPath = "//ul[contains(@class,'collection')]/li[contains(@class,'col')]";
    protected $gridEntriesPath = "//ul[contains(@class,'row data')]/li[contains(@class,'col')]";
    protected $titleXpath = "//a[contains(@class,'card-title')]";
    protected  $descriptionXpath = "//a[contains(@class,'grey-text')]";
    protected $navBtnID = 'nav-btn-add';
    protected $entryUrl = 'entry_url';
    protected $addButton = 'add';
    
    public function countUnreadEntry(Session $session){
        $page=$session->getPage();
        return $page->find('xpath',$this->unreadCountXPath)->getHtml();
    }
    
    public function isEntryListed(Session $session,$title,$description){
        $page=$session->getPage();
        $Allentry = $page->findAll('xpath', $this->listEntriesPath);
        if (empty($Allentry)){
            $Allentry = $page->findAll('xpath',$this->gridEntriesPath);
        }
        foreach ($Allentry as $entry){
            if ($entry->find('xpath', $this->titleXpath)->getText() == $title
                && $entry->find('xpath', $this->descriptionXpath)->getText() == $description){
                    return;
            }
        }
        throw new Exception("Could not find entry");
    }
    
    public function addNewEntry(Session $session,$link){
        $page=$session->getPage();
        $page->findById($this->navBtnID)->click();
        $page->fillField($this->entryUrl, $link);
        $page->findButton($this->addButton)->click();
    }
}