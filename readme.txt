=== Article Photos ===
Contributors: chrisnorthwood
Tags: images, post, posts, image, admin, thumbnail
Requires at least: 2.7
Tested up to: 2.7.1
Stable tag: 1.0

== Description ==

This plugin adds a form to your post screen that allows you to upload an image to go with your blog post. You can then use `the_article_image()` function in your theme to show the image whereever you want on your theme, at whatever size (dynamically rescaled and cached using ImageMagick so no dodgy browser rescaling or sending huge photos out).

An example of it in use is on http://www.nouse.co.uk/, where the photo alongside each post is attached, and then shown on the front page, and on the single page itself in the theme.

== Installation ==

Make sure you're using PHP 5 and the ImageMagick extension installed.

Once the plugin is active, you use `the_article_image($size);` function, which echoes an `img` tag for the image and caption associated with that article, at the width defined by the `$size` parameter. If there is no image, nothing is returned.

Other functions include a `get_the_article_img($size)` variant, which returns, rather than echoes the `img` tag. Also, `the_article_photo_caption()` and `get_the_article_photo_caption()` which get the caption associated with that article. `get_the_article_photo($size)` works like `get_the_article_img($size)` but just returns a URL, rather than an `img` tag, and `the_article_photo($size)` returns the URL to the image, or if no image exists, to a placeholder image in the template directory called `img/box.png`.

== FAQ ==

= I get errors when the plugin is loaded =

Ensure that the ImageMagick PHP extension is installed.
