# Slackcoin

Have Bitcoin info delivered to your Slack team!

## Usage
### Prerequisites
- Docker
- [Incoming Webhook](https://api.slack.com/incoming-webhooks) for your Slack team

### Get started
The main thing to understand is that the script depends on `SLACK_WEBHOOK_URL` environment variable.

Here are two fairly easy ways to get going:  
1. Set-up an environment variable `SLACK_WEBHOOK_URL` and then run `./slackcoin`
2. Run  `SLACK_WEBHOOK_URL=https://your-webhook-url.com ./slackcoin`

Voila!  
You should see current Bitcoin value sent to your Slack team.
  
If you want more customization and more data, please see **Advanced usage** chapter.

## Advanced usage

There are more options you can use to customize your experience.
All of the options can be customized through environment variables.

### Historic data and comparison
By default, only *current* Bitcoin data is delivered. If you want to customize this, `TIMESTAMPS` is the environment variable you have to modify.  

**Possible value**: a string consisting of comma-separated values that can be provided as `$time` parameter to PHP's [DateTime](http://php.net/manual/en/datetime.construct.php)

**Example usage**  
`TIMESTAMPS="yesterday, 3 days ago, 7 days ago, 30 days ago" ./slackcoin`

### Currency
By default, `USD` currency is being used.

If you want to use something else, just modify `CURRENCY` environment variable.

**Example usage**
`CURRENCY=EUR ./slackcoin`

### Inline display
If you provide multiple timestamps in `TIMESTAMPS` variable, by default all values are displayed each on their own line.  
If you want them to be displayed side-by-side, set `INLINE` to `true` (or anything else that PHP considers truthy).

**Example usage**
`INLINE=true ./slackcoin`

### Combine
Of course you can combine these as you like. One example I personally use is:  
`CURRENCY=EUR INLINE=true TIMESTAMPS="yesterday, 7 days ago, 30 days ago" ./slackcoin`

And here's how it looks  
![Example](https://i.imgur.com/8ELlnoF.png)

## How it works

It just fetches data from [Coinbase](https://www.coinbase.com/)'s API and sends it nicely formatted to your Slack channel. That's it.
