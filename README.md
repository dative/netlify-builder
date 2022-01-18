# NetlifyBuilder plugin for Craft CMS 3.x

NetlifyBuilder helps you build & track differences between the Craft's entries and what's live.

![Screenshot](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.7 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require dative/netlify-builder

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for NetlifyBuilder.

## Configuring NetlifyBuilder

After installing, you will need to add both the Netlify build webhook and the build badge source.

## Using NetlifyBuilder

Once configured, just add the widget to the dashboard. As you update entries, assets, etc., the plugin will track those changes and display the butotn to trigger a build.

## NetlifyBuilder Roadmap

Some things to do, and ideas for potential features:

* Add view to see the changes since the last build.

Brought to you by [Dative](https://hellodative.com)
