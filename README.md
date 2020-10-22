# Embedder plugin for Craft CMS 3.x

Embed videos (YouTube, Vimeo, Facebook) easily!

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require tibemolde/embedder

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Embedder.

## Embedder Overview

Embed videos easily!

## Configuring Embedder

No configuration is needed outside of runtime-options, however you can tweak where video-posters are stored locally through the plugin-settings

## Using Embedder

Basic usage:

    <div class="embed embed--{{ craft.embedder.getVideoType(videoUrl) }}">
      <iframe src="{{ craft.embedder.getVideoEmbedUrl(videoUrl) }}" title="{{ 'Watch video'|t }}"></iframe>
    </div>
    
    
Style as you please (this is just an example):


    .embed {
	    position: relative;
	    overflow: hidden;
	    width: 100%;
	    height: auto;
	    padding-bottom: 56.25%; /* ratio for 16:9 embed */

	    iframe,
	    object,
	    embed {
		    position: absolute;
		    top: 0;
		    left: 0;
		    width: 100% !important;
		    height: 100% !important;
		    border: 0;
	    }
    }

## Embedder Roadmap

Some things to do, and ideas for potential features:

* Support oEmbed
* Support audio-playlists (Spotify, Soundcloud etc)

Brought to you by [TIBE Molde](https://github.com/tibemolde)
