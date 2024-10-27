# Public Zonneplan Nexus with Home Assistant
This code will let you expose your Zonneplan Nexus data to the internet, so others can follow its progress.

[Zonneplan has their own demo on their website](https://www.zonneplan.nl/thuisbatterij/nexus-demo) and this will let you compare other 'real' installations.

Mine is public on https://nick.bouwhuis.net/homebattery/

And this is the code.

It's mostly ChatGPT engineering and there is room for improvement. For example, it can cache the results and only fetch new details based on the 'Last Measurement' entity. Right now it fetches data for all endpoints every minute. 

Looking forward to your pull requests! 😊

## Installation

1. Make sure your webserver can reach your Home Assistant instance
2. Copy exmaple config
```bash
cp config.example.php config.php
```
3. Edit `config.php`
```bash
vim config.php
```
4. Fill in your Home Assistant API URL and Bearer token 
You can generate a token in Home Assistant by going to your profile page (your name) and clicking the Security tab.
5. Download Chart.js v4.4.5 and place it alongside the index.html
```bash
wget https://cdn.jsdelivr.net/npm/chart.js@4.4.5/dist/chart.umd.min.js -O chart.js
```
