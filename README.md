# EU VAT Field for Laravel Nova

This introduces a EU VAT validation field for Laravel Nova.

The field  will validate on client side and server side. 

### Install

You need to run the following command: 
`composer require napp/nova-vat-validation`

### Add the field to your Resource file:

```php
VatValidation::make('vat')
    ->rules('required', 'string', 'vat')
    ->hideFromIndex(),
```

