Feature: delete entries
	As user
	I would like to delete offline saved website 
	So that I can remove website which I dont need anymore

Scenario: deleting the selected entries successfully
	Given user has logged in with username "admin" and password "admin"
 	And there is an entry listed in a list with the title "JankariTech" and the link description "jankaritech.com"
	When the user deletes the item with the title "..."
	Then an entry with the title "JankariTech" and the link description "jankaritech.com"  should not be listed in the list 
	And count of unread entries is 0

Scenario: Cancelling the delete operation 
	Given user has logged in with username "admin" and password "admin"
 	And there is listed in the list with the title "JankariTech" and the link description "jankaritech.com"
 	And count of entries is 1
	When the user click on delete button
	And user press Cancel button on popup
	Then an entry with the title "JankariTech" and the link description "jankaritech.com"  should be listed in the list 
And count of unread entries is 1