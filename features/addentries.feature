 Feature: add new entries
 	As user
 	I want to add a new Website 
 	So that I can read the content later
 	
 Background:
 	Given the user has browsed to the login page
 	And user has logged in with username "admin" and password "admin"
 	And the list of unread entries is 0
 

 Scenario: adding a new page successfully
	When the user adds a new entry with the url "http://www.jankaritech.com"
	Then an entry should be listed in the list with the title "JankariTech" and the link description "jankaritech.com"
	And the count of unread entries should be 1
	
Scenario: adding two new pages successfully
	When the user adds a new entry with the url "http://www.jankaritech.com"
	And the user adds a new entry with the url "http://en.wikipedia.org"
	Then an entry should be listed in the list with the title "JankariTech" and the link description "jankaritech.com"
	And an entry should be listed in the list with the title "Main Page" and the link description "en.wikipedia.org"
	And the count of unread entries should be 2