Portal Rev plugin for WordPress
========================

## Introduction

This plugin creates a search interface for the [Virtual Health Library](http://modelo.bvsalud.org/en/) Centers information source.

## Requirements

Wordpress 3.x

## Install

0. [Download](https://github.com/bireme/centers-wp-plugin/archive/master.zip) the Centers plugin for Wordpress;
0. Unzip the plugin below the `wp-content/plugins` folder of your Wordpress instance and rename it to `centers`;
0. Activate the Centers plugin through the administration panel of WordPress (dashboard).

For further information on installing plugins please see the [Manual Plugin Installation from Wordpress codex site](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

## Configuration

Go to `Settings` in the administration panel (dashboard) and click on the newly created `Centers` item.
* `Plugin page` is mandatory and is set to `centers` by default. It defines the URL of the search interface page;
* `Filter query` is optional and defines the strategy (a term or expression) to act as a filter for record displaying.
* `Search form` is optional and defines a flag to control the display of the search box in the homepage;
* `Google Analytics code` is optional and allows the integration of website analytics services provided by Google. Notice this requires previous registration in Google.
