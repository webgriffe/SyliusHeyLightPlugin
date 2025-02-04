<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

<h1 align="center">Sylius <a href="https://heylight.com/" target="_blank">HeyLight</a> Plugin</h1>

<p align="center">Sylius plugin for HeyLight payment gateway (ex PagoLight BNPL and PagoLight PRO).</p>

## Installation

1. Run:
    ```bash
    composer require webgriffe/sylius-heylight-plugin
   ```

2. Add `Webgriffe\SyliusHeylightPlugin\WebgriffeSyliusHeylightPlugin::class => ['all' => true]` to your `config/bundles.php`.
   
   Normally, the plugin is automatically added to the `config/bundles.php` file by the `composer require` command. If it is not, you have to add it manually.

3. Create a new file config/packages/webgriffe_sylius_heylight_plugin.yaml:
   ```yaml
   imports:
       - { resource: "@WebgriffeSyliusHeylightPlugin/config/config.php" }
   ```

4. Import the routes needed for cancelling the payments. Add the following to your config/routes.yaml file:
   ```yaml
   webgriffe_sylius_heylight_plugin_shop:
       resource: "@WebgriffeSyliusHeylightPlugin/config/shop_routing.php"
       prefix: /{_locale}
       requirements:
           _locale: ^[A-Za-z]{2,4}(_([A-Za-z]{4}|[0-9]{3}))?(_([A-Za-z]{2}|[0-9]{3}))?$

   webgriffe_sylius_heylight_plugin_ajax:
       resource: "@WebgriffeSyliusHeylightPlugin/config/shop_ajax_routing.php"

   sylius_shop_payum_cancel:
       resource: "@PayumBundle/Resources/config/routing/cancel.xml"

   ```
   **NB:** The file shop_routing needs to be after the prefix _locale, so that messages can be displayed in the right
   language. You should also include the cancel routes from the Payum bundle if you do not have it already!

5. Add the WebhookToken entity. Create a new file `src/Entity/Payment/WebhookToken.php` with the following content:
   ```php
    <?php

    declare(strict_types=1);

    namespace App\Entity\Payment;

    use Doctrine\ORM\Mapping as ORM;
    use Webgriffe\SyliusHeylightPlugin\Entity\WebhookToken as BaseWebhookToken;
    
    /**
     * @ORM\Entity
     * @ORM\Table(name="webgriffe_sylius_heylight_webhook_token")
     */
    class WebhookToken extends BaseWebhookToken
    {
    }
    ```
6. Run:
    ```bash
    php bin/console doctrine:migrations:diff
    php bin/console doctrine:migrations:migrate
    ```

7. Run:
    ```bash
    php bin/console sylius:install:assets
   ```
   Or, you can add the entry to your webpack.config.js file:
    ```javascript
    .addEntry(
        'webgriffe-sylius-heylight-entry',
        './vendor/webgriffe/sylius-heylight-plugin/public/poll_payment.js'
    )
    ```
   And then override the template `WebgriffeSyliusHeylightPlugin/after_pay.html.twig` to include the entry:
    ```twig
    {% block javascripts %}
        {{ parent() }}

        <script>
            window.afterUrl = "{{ afterUrl }}";
            window.paymentStatusUrl = "{{ paymentStatusUrl }}";
        </script>
        {{ encore_entry_script_tags('webgriffe-sylius-heylight-entry', null, 'sylius.shop') }}
    {% endblock %}
    ```

## Usage

Access to the admin panel and go to the `Payment methods` section. Create a new payment method and select `HeyLight dilazione`
or `HeyLight finanziamento` as the gateway. Then, configure the payment method with the required parameters.

Automatically, the plugin will hide the payment method if the currency is not EUR, GBP or CH or if the country is not
Italy or Switzerland. HeyLight finanziamento will also be visible only if the order total amount is greater than 100 EUR.

## Contributing

For a comprehensive guide on Sylius Plugins development please go to Sylius documentation,
there you will find the <a href="https://docs.sylius.com/en/latest/plugin-development-guide/index.html">Plugin Development Guide</a>, that is full of examples.
