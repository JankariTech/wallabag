Feature: delete entries
	As user
	I would like to delete offline saved website 
	So that I can remove website which I dont need anymore

Scenario: deleting the selected entries successfully
	Given the user has browsed to the login page
	And user has logged in with username "admin" and password "admin"
	And the user adds a new entry with the url "http://www.jankaritech.com"
 	And there is an entry listed in a list with the title "JankariTech" and the link description "jankaritech.com"
	When the user deletes the item with the title "JankariTech"
	Then the count of unread entries should be 0

Scenario: Cancelling the delete operation 
	Given the user has browsed to the login page
	And user has logged in with username "admin" and password "admin"
	And the user adds a new entry with the url "http://www.jankaritech.com"
 	And there is an entry listed in a list with the title "JankariTech" and the link description "jankaritech.com"
	When user press cancel button on popup after pressing delete button for title "JankariTech"
	Then the count of unread entries should be 1