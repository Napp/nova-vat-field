<?php

namespace Napp\NovaVatValidation;

use SoapClient;
use SoapFault;

class VatValidator
{
    /**
     * Regular expression patterns per country code
     *
     * @var array
     * @link http://ec.europa.eu/taxation_customs/vies/faq.html?locale=en#item_11
     */
    protected static $patterns = array(
        'AT' => 'U[A-Z\d]{8}',
        'BE' => '(0\d{9}|\d{10})',
        'BG' => '\d{9,10}',
        'CY' => '\d{8}[A-Z]',
        'CZ' => '\d{8,10}',
        'DE' => '\d{9}',
        'DK' => '(\d{2} ?){3}\d{2}',
        'EE' => '\d{9}',
        'EL' => '\d{9}',
        'ES' => '[A-Z]\d{7}[A-Z]|\d{8}[A-Z]|[A-Z]\d{8}',
        'FI' => '\d{8}',
        'FR' => '([A-Z]{2}|\d{2})\d{9}',
        'GB' => '\d{9}|\d{12}|(GD|HA)\d{3}',
        'HR' => '\d{11}',
        'HU' => '\d{8}',
        'IE' => '[A-Z\d]{8}|[A-Z\d]{9}',
        'IT' => '\d{11}',
        'LT' => '(\d{9}|\d{12})',
        'LU' => '\d{8}',
        'LV' => '\d{11}',
        'MT' => '\d{8}',
        'NL' => '\d{9}B\d{2}',
        'PL' => '\d{10}',
        'PT' => '\d{9}',
        'RO' => '\d{2,10}',
        'SE' => '\d{12}',
        'SI' => '\d{8}',
        'SK' => '\d{10}'
    );

    /**
     * Validate a VAT number format. This does not check whether the VAT number was really issued.
     *
     * @param string $vatNumber
     *
     * @return boolean
     */
    public function validateFormat(string $vatNumber): bool
    {
        $vatNumber = strtoupper($vatNumber);
        $country = substr($vatNumber, 0, 2);
        $number = substr($vatNumber, 2);

        if (!isset(self::$patterns[$country])) {
            return false;
        }

        return preg_match('/^' . self::$patterns[$country] . '$/', $number) > 0;
    }

    /**
     *
     * @param string $vatNumber
     *
     * @return boolean
     * @throws \Exception
     */
    public function validateExistence(string $vatNumber): bool
    {
        $vatNumber = strtoupper($vatNumber);

        return $this->checkVat(substr($vatNumber, 0, 2), substr($vatNumber, 2));
    }

    /**
     * Validates a VAT number using format + existence check.
     *
     * @param string $vatNumber Either the full VAT number (incl. country) or just the part after the country code.
     *
     * @return boolean
     * @throws \Exception
     */
    public function validate(string $vatNumber): bool
    {
        return $this->validateFormat($vatNumber) && $this->validateExistence($vatNumber);
    }


    /**
     * @param string $countryCode
     * @param string $vatNumber
     *
     * @return bool
     * @throws \Exception
     */
    public function checkVat(string $countryCode, string $vatNumber): bool
    {
        $client = new SoapClient(
            'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl',
            ['connection_timeout' => 10]
        );

        try {
            $response = $client->checkVat(['countryCode' => $countryCode, 'vatNumber' => $vatNumber]);
        } catch (SoapFault $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return (bool) $response->valid;
    }


}