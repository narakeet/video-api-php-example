# Narakeet Video Build API example in `PHP`

This repository provides a quick example demonstrating how to access the Narakeet [markdown to video API](https://www.narakeet.com/docs/automating/rest/) from PHP.

The example sends a request to generate a video from a local ZIP file, then downloads the resulting video into a temporary local file. 

The video is created from the [video](video) directory, using a pre-zipped archive. 

## Prerequisites

This example works with PHP 7.4 and later. You can run it inside Docker (then it does not require a local PHP installation), or on a system with a PHP 7.4 or later.

## Running the example

1. set and export a local environment variable called `NARAKEET_API_KEY`, containing your API key (or modify [video.php](video.php) line 3 to include your API key).
2. optionally edit [video.php](video.php) and modify the video zip archive, main video file, and the function that handles progress notification (lines 4, 5 and 6).
2. to run inside docker, execute `make run`
3. Or to run outside docker, on a system with `php` command line, execute `php video.php`

## More information

Check out <https://www.narakeet.com/docs/automating/rest/> for more information on the Narakeet Markdown to Video API. 
