Feature: Login 
	As user 
	I want to login
	So that I can access my account entries
	
Scenario: Successfully Login
	Given the user has browsed to the login page
	When the user logs in with username 'admin' and password 'admin' 
	Then the user should be redirected to a page with the title 'Unread entries – wallabag'
	
Scenario Outline: Unsuccessful Login
	Given the user has browsed to the login page
	When the user logs in with username '<user>' and password '<password>' 
	Then the user should be redirected to a page with the title 'Welcome to wallabag! – wallabag'
	And an error message should be displayed saying "Invalid credentials."
	Examples:
	| user   | password |
	| bishal | rijal    |
	| admin  |          |
	| admin  | wrong    |
 