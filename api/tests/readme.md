# Testing...
- Feature tests.
Feature tests should contain all feature flow from arranging data to asserting expected results.
Each feature test should test only one single feature and all corner cases around it. 
A feature - is an action, which can be performed via Controllers or Console interfaces.
The such type of test can use mocks for checks not-testable results, such as Mail Sending, Password Hashing, Random Generators, etc.
It should use database for comparing processed data with excepted values. 

- Unit tests.
Unit tests should copy the application folders structure to make easy navigation through tests.
Each unit test is applied to a class it tests which, and should cover 100% lines of the class. 
The test should mock every injected external dependency. 
It may also use database for comparing results, which current class stores.  

# TODO api
- add package for universal tokens generation with all required features (hashing, checking, expiring, etc.) and use it for password_reset

# TODO docker
- add instructions about `make up` and `make down` commands
