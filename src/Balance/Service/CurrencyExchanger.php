<?php

namespace App\Balance\Service;

use GuzzleHttp\Client;

class CurrencyExchanger
{
    private const API_CURRENCY_URL = 'https://free.currencyconverterapi.com/api/v6/';

    private $currencies = [];

    private $currencyValue = 1;

    public function __construct()
    {
        $this->actualizeCurrencies();
    }

    public function exchangeToPLN(float $value): float
    {
        return $value * $this->currencyValue;
    }

    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    public function isCurrencyExists(string $currency): bool
    {
        return array_key_exists($currency, $this->currencies);
    }

    public function determineCurrencyValue(string $currency): void
    {
        if ($this->isCurrencyExists($currency)) {
            $client = new Client([
                'base_uri' => self::API_CURRENCY_URL
            ]);

            $response = $client->request('GET', sprintf('convert?q=PLN_%s&compact=ultra', $currency));
            $currencyValue = json_decode($response->getBody(), true);

            $this->currencyValue = reset($currencyValue);
        }
    }

    private function actualizeCurrencies(): void
    {
        $client = new Client([
            'base_uri' => self::API_CURRENCY_URL
        ]);

        $response = $client->request('GET', 'currencies');
        $body = json_decode($response->getBody(), true);

        $this->currencies = reset($body);
    }
}