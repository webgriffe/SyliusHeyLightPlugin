@paying_for_order
Feature: Paying with Pagolight during checkout
    In order to buy products
    As a Customer
    I want to be able to pay with Pagolight Payment Checkout

    Background:
        Given the store operates on a single channel in "EUR" currency
        And there is a zone "The Rest of the World" containing all other countries
        And the store ships to "Italy"
        And there is a user "john@example.com" identified by "password123"
        And the store has a payment method "Pagolight" with a code "PAGOLIGHT_PAYMENT_METHOD" and Pagolight Payment Checkout gateway
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And I am logged in as "john@example.com"

    @ui @javascript
    Scenario: Successful payment
        Given I added product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        And I specify the billing address as "Via Franceschini 3", "Casalgrande", "42013", "Italy" for "Mario Rossi"
        And I complete the addressing step
        And I proceeded with "Free" shipping method and "Pagolight" payment
        When I confirm my order
        And I complete the payment on Pagolight
        Then I should be on the capture payment page
        When Pagolight notify the store about the successful payment
        Then I should be redirected to the thank you page
        And I should be notified that my payment has been completed
        When I am viewing the summary of my last order
        Then I should see its payment status as "Completed"

    @ui @javascript
    Scenario: Failed payment
        Given I added product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        And I specify the billing address as "Via Franceschini 3", "Casalgrande", "42013", "Italy" for "Mario Rossi"
        And I complete the addressing step
        And I proceeded with "Free" shipping method and "Pagolight" payment
        When I confirm my order
        And I complete the payment on Pagolight
        Then I should be on the capture payment page
        When Pagolight notify the store about the failed payment
        Then I should be redirected to the order page page
        And I should be notified that my payment has been cancelled
        And I should be able to pay again

    @ui
    Scenario: Cancelling the payment
        Given I added product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        And I specify the billing address as "Via Franceschini 3", "Casalgrande", "42013", "Italy" for "Mario Rossi"
        And I complete the addressing step
        And I proceeded with "Free" shipping method and "Pagolight" payment
        When I confirm my order
        And I cancel the payment on Pagolight
        Then I should be notified that my payment has been cancelled
        And I should be able to pay again

    @ui @javascript
    Scenario: Retrying the payment with success
        Given I added product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        And I specify the billing address as "Via Franceschini 3", "Casalgrande", "42013", "Italy" for "Mario Rossi"
        And I complete the addressing step
        And I proceeded with "Free" shipping method and "Pagolight" payment
        And I have confirmed order
        But I have cancelled Pagolight payment
        When I try to pay again with Pagolight
        And Pagolight notify the store about the successful payment
        Then I should be redirected to the thank you page
        And I should be notified that my payment has been completed
