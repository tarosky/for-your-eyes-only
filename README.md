# For Your Eyes Only

Contributors: tarosky, Takahashi_Fumiki, hametuha  
Tags: membership, login, restrict, gutenberg  
Requires at least: 6.1  
Tested up to: 6.6  
Stable tag: nightly  
Requires PHP: 7.2  
License: GPLv3 or later  
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

Add a restricted block for specified users.

## Description

This plugin adds a block to your block editor.
This block changes its display depending on a current user's capability.

* You can set capability for block.
* If a current user has a capability for the block, its content will be replaced.
* Or else, block is displayed as log in link.
* This block is an inner block, so you can nest any blocks inside it. Convert it to a reusable block for your productivity.

See screenshot for how block will be displayed.

This plugin use REST API to convert block content, so you can use it with cached WordPress.
Even if you use CDN like [CloudFront](https://aws.amazon.com/cloudfront/) or [Cloudflare](https://www.cloudflare.com/), this plugin displays proper contents for each users.

For performance enhancement, [Cookie Tasting](https://wordpress.org/plugins/cookie-tasting/) is recommended.
It decrease server access including REST API by checking COOKIE value before accessing to server side script.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/for-your-eyes-only` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. You get new block named **Restricted Block** on Block Editor.

## Frequently Asked Questions

### How to Contribute

We host our code on [Github](https://github.com/tarosky/for-your-eyes-only), so feel free to send PR or issues.

## Screenshots

1. You get a new block to restrict non-members.

## Changelog

### 1.1.0

* Ownership is now changed. Thanks to @hametuha for taking over this plugin.
* Add dynamic mode. This works as PHP rendering.

### 1.0.1

* Add auto deploy.

### 1.0.0

* First Release.
