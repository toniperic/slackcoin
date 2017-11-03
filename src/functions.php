<?php

use GuzzleHttp\Client;

function retrieveBitcoinValueFor($day, $currency): float {
    $http = new Client();
    $url = sprintf('https://api.coinbase.com/v2/prices/BTC-%s/spot', $currency);

    return (float) json_decode($http->get($url, [
        'headers' => [
            'CB-VERSION' => '2017-11-03',
        ],
        'query' => [
            'date' => (new DateTime($day))->format('Y-m-d'),
        ]
    ])->getBody()->getContents())->data->amount;
}

