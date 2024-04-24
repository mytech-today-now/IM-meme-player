# IM Meme Player v.0.0.7.3

## Introduction

Welcome to IM Meme Player, a cutting-edge WordPress plugin designed to transform your website with dynamic meme playback. This plugin mimics popular social media styles like Snapchat and TikTok, providing a familiar and engaging user interface.

## Key Features

- **Dynamic Meme Playback**: Effortlessly integrates a variety of meme formats, creating an interactive and constantly updated stream of content.
- **Nested Playback Capability**: Unique feature allowing playbacks to contain additional meme playbacks, offering a multi-layered viewing experience.
- **User-Friendly Admin Panel**: Configure playback settings and features easily through the WordPress admin interface.

## Installation Guide

1. Download the plugin zip file from the GitHub repository [builds](https://github.com/mytech-today-now/IM-meme-player/tree/main/builds) folder.
2. Navigate to 'Plugins > Add New' in your WordPress dashboard.
3. Select 'Upload Plugin' and upload the downloaded file.
4. Activate the plugin once installed.

## Docker Quickstart

1. Ensure [Docker](https://www.docker.com/products/personal/) is installed.
2. Open a terminal in the plugin directory.
3. Execute: `docker-compose -f compose-dev.yaml up -d`
4. Access the local server at `http://localhost:8080`. Troubleshoot with [this guide](https://locall.host/) if needed.

## Usage Instructions

The meme playlist is a new custom post type.  Each item in the playlist can consist of media type of some sort, such as MP4,  MP3,  jpeg, jpg,  PNG,  Ico,  SVG,  jfif,  or Ogg.  When viewing the playlist,  the playlist advances through images based of of a setting in the configuration page.   If the item is video, it will automatically play the video.   Upon completion of the video the playlist will move to the next item.

The user is able to advance forward in the playlist or backward in the playlist, using GUI controls.

The playlist is able to alter playlist playback, and edit playlists on the configuration page.

Embed memes using `[meme_player]` shortcode in posts or pages. Adjust settings in the WordPress admin panel under 'Tools > Meme Player'.

## System Requirements

- WordPress 5.0 or higher.
- PHP 7.2 or above.

## Getting Started

Configure the plugin under 'Tools > Meme Player' in the WordPress dashboard. Then, use the `[meme_player]` shortcode to display the meme player on your site.

## Support and Community

For support, feature suggestions, or bug reports, visit our [GitHub Issues page](https://github.com/mytech-today-now/IM-meme-player/issues). We welcome contributions to enhance this project.

## Acknowledgements

Special thanks to the entire community of developers, AI, testers, and users for their invaluable contributions.

## Licensing

IM Meme Player is open-sourced under GPL v2 or later.

## Contact

For further inquiries, reach us at <mytechtoday@protonmail.com>.

## Stay Informed

Follow the latest updates and releases on our [GitHub repository](https://github.com/mytech-today-now/IM-meme-player).

## Version History
- **v.0.0.7.3** (Current): 
  - Implemented error handling and exception tracking for better debugging.
  - Fixed minor bugs reported in the previous version.
  - Enhanced user interface for a more intuitive user experience.
  - Added support for more meme formats.
  - Improved security measures to protect user data.
  - Begin creating builds of the plugin as a .zip file in the [builds](https://github.com/mytech-today-now/IM-meme-player/tree/main/builds) folder, with new naming convention.
  - Clarified plug-in usage in the readme file.
- **v.0.0.7.2**: 
  - Added debugging statements throughout the repo.
  - Added the ability to add delete and rearrange the order of items in the playlist. 
  - Added the CMV for all of that.
  - updated more debugging throughout.
- **v.0.0.7.1**: Installation enabled; requires code refinement for optimal functionality.
- **v.0.0.7.0**: Enhanced admin settings interface; minor bug fixes.

