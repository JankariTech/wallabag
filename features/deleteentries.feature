Feature: delete entries
	As user
	I would like to delete offline saved website 
	So that I can remove website which I dont need anymore

Background:
	Given the user has browsed to the login page
	And user has logged in with username "admin" and password "mypassword"
	And the user has added a new entry with the url "http://www.jankaritech.com"

Scenario: deleting the selected entries successfully
	When the user deletes the item with the title "JankariTech"
	Then the count of unread entries should be 0

Scenario: Cancelling the delete operation 
	When user press cancel button on popup after pressing delete button for title "JankariTech"
	Then the count of unread entries should be 1
	
Scenario: Add another entry and delete that entry
	Given the user has added a new entry with the url "http://en.wikipedia.org"
	And the user has added a new entry with the url "http://de.wikipedia.org"
	When the user deletes the item with the title "JankariTech"
	Then the count of unread entries should be 2
	And there should not be entry in list with title "JankariTech" and the link description "jankaritech.com"
