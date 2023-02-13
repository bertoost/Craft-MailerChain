<h1 align="center">Mailer Chain for Craft CMS</h1>

This plugin provides a mailer adapter integration for [Craft CMS](https://craftcms.com/) to configure a chain 
of mailers and send randomly through one of the configured mailers.

## Requirements

This plugin requires Craft CMS 4 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project's Control Panel and search for "Mailer Chain". Then click on the "Install" 
button in its modal window.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require bertoost/craft-mailerchain

# tell Craft to install the plugin
./craft install/plugin mailerchain
```

## Sending e-mails

Just use the Craft Mailer to send emails, normally.

This plugin acts like a mail adapter and passes a random configured adapter to the Mailer.