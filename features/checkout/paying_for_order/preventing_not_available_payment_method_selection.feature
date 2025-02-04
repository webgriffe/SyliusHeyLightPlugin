@paying_for_order
Feature: Preventing not available payment method selection
    In order to pay for my order properly
    As a Customer
    I want to be prevented from selecting not available payment methods

    Background:
        Given the store operates on a single channel in "EUR" currency
        And there is a zone "The Rest of the World" containing all other countries
        And the store ships to "Italy"
        And there is a user "john@example.com" identified by "password123"
        And the store has a payment method "Heylight BNPL" with a code "HEYLIGHT_BNPL_PAYMENT_METHOD" and Heylight Payment Checkout gateway
        And the store has a payment method "Heylight Financing" with a code "HEYLIGHT_FINANCING_PAYMENT_METHOD" and Heylight Payment Checkout gateway
        And the store has a product "PHP T-Shirt" priced at "€19.99"
        And the store ships everywhere for free
        And I am logged in as "john@example.com"

    @ui
    Scenario: Not being able to select Heylight Financing payment method if order total is less than 100 EUR
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the billing address as "Via Franceschini 3", "Casalgrande", "42013", "Italy" for "Mario Rossi"
        And I complete the addressing step
        And I select "Free" shipping method
        And I complete the shipping step
        Then I should not be able to select "Heylight Financing" payment method
