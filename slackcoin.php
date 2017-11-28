<?php

declare(strict_types=1);

require 'vendor/autoload.php';

// Set up
$slack = new Maknz\Slack\Client(getenv('SLACK_WEBHOOK_URL'));
$currency = getenv('CURRENCY');
$locale = getenv('LOCALE');

$timestamps = getenv('TIMESTAMPS') ? getenv('TIMESTAMPS') : [];
if ($timestamps) {
    $timestamps = array_filter(array_map(function ($item) {
        return trim($item);
    }, explode(',', $timestamps)));
}

// fetch current price + whatever specified through TIMESTAMPS env variable
$values = [
    'now' => retrieveBitcoinValueFor('now', $currency),
];
foreach ($timestamps as $timestamp) {
    $values[$timestamp] = retrieveBitcoinValueFor($timestamp, $currency);
}

/**
 * cosmetics.
 * basically just take Bitcoin value retrieved NOW, and make comparison to the first timestamp
 * provided in TIMESTAMPS env variable. used to determine Slack attachment color (good/green, neutral or bad/danger)
 *
 * if there aren't any other timestamps being used for comparison other than current one, we default to good/green
 */
$color = 'good';
if (count($values) > 1) {
    $colorComparisonElements = array_values(array_slice($values, 0, 2));
    if (($diff = ($colorComparisonElements[0] / $colorComparisonElements[1] * 100 - 100)) > 1) {
        $color = 'good';
    } elseif ($diff < -1) {
        $color = 'danger';
    } else {
        $color = 'warning';
    }
}

// resolve fields to be added as the Slack attachment
$fields = [];
$currencyFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
$percentFormatter = new NumberFormatter($locale, NumberFormatter::PERCENT);
$percentFormatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
foreach ($values as $key => $value) {
    if ($key == 'now') {
        $fieldValue = $currencyFormatter->formatCurrency($value, $currency);
    } else {
        $fieldValue = $currencyFormatter->formatCurrency($value, $currency);

        if (filter_var(getenv('PRICE_CHANGE_DIFF'), FILTER_VALIDATE_BOOLEAN)) {
            $priceChange = ($values['now'] - $value) / $value;
            $fieldValue = sprintf('%s (%s)', $fieldValue, $percentFormatter->format($priceChange));
        }
    }

    $fields[] = [
        'title' => ucfirst($key),
        'value' => $fieldValue,
        'short' => getenv('INLINE'),
    ];
}

$attachment = new \Maknz\Slack\Attachment([
    'text' => 'Bitcoin Value - <https://www.coinbase.com/charts|View Chart>',
    'color' => $color,
    'fields' => $fields,
]);

$slack->attach($attachment)->send();
