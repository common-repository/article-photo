<?php
/*
Plugin Name: Article photo
Plugin URI: http://wordpress.org/extend/plugins/article-photo/
Description: Allows you to attach photos to articles
Version: 1.0
Author: Chris Northwood
Author URI: http://www.pling.org.uk/

Nouse revision: 1851
*/

// Workaround to allow post screen to upload files
// http://trac.wordpress.org/ticket/8960

if ( !function_exists('ob_multipart_entry_form') ) :
#
# ob_multipart_entry_form_callback()
#

function ob_multipart_entry_form_callback($buffer)
{
    $buffer = str_replace(
        '<form name="post"',
        '<form enctype="multipart/form-data" name="post"',
        $buffer
        );

    return $buffer;
} # ob_multipart_entry_form_callback()


#
# ob_multipart_entry_form()
#

function ob_multipart_entry_form()
{
    if ( $GLOBALS['editing'] )
    {
        ob_start('ob_multipart_entry_form_callback');
    }
} # ob_multipart_entry_form()

add_action('admin_head', 'ob_multipart_entry_form');


endif;


// Add form to post screen
function articlephoto_upload_form()
{
    global $post;
    // display if already has a file associated with it
    $suppress = get_post_meta($post->ID,'_articlephoto_suppress',true);
?>
    <input type="file" name="articlephoto_file" id="articlephoto_file" /><br/>
    <label for="articlephoto_caption">Image caption:</label> <input type="text" name="articlephoto_caption" id="articlephoto_caption" value="<?php the_article_photo_caption(); ?>" /><br/>
    <label for="articlephoto_suppress">Suppress article page image?</label> <input type="checkbox" name="articlephoto_suppress" value="suppress" <?php if ($suppress == 'suppress') { ?> checked="checked"<?php } ?> /><br />
<?php
    $i = get_the_article_img(0);
    if ($i)
    {
?>
    <h4>Current Photo</h4>
<?php
        echo $i;
    }
}

function articlephoto_init()
{
    add_meta_box('articlephoto','Article Photo', 'articlephoto_upload_form', 'post', 'normal', 'high');
}

add_action('admin_menu', 'articlephoto_init');

// Parse uploaded file
function articlephoto_upload($postid)
{
    if (isset($_POST['articlephoto_suppress']))
    {
        delete_post_meta($postid, '_articlephoto_suppress');
        add_post_meta($postid, '_articlephoto_suppress', $_POST['articlephoto_suppress']);
    }
    if (isset($_POST['articlephoto_caption']))
    {
        delete_post_meta($postid, '_articlephoto_caption');
        add_post_meta($postid, '_articlephoto_caption', htmlspecialchars($_POST['articlephoto_caption']));
    }
    if ($_FILES['articlephoto_file']['size'] > 0)
    {
        $uploaded = wp_handle_upload($_FILES['articlephoto_file'], array('action' => $_POST['action']));
        if (!isset($uploaded['error']))
        {
            delete_post_meta($postid, '_articlephoto_url');
            delete_post_meta($postid, '_articlephoto_filename');
            add_post_meta($postid, '_articlephoto_url', $uploaded['url']);
            add_post_meta($postid, '_articlephoto_filename', $uploaded['file']);
        }
    }
}
add_action('edit_post', 'articlephoto_upload');
add_action('save_post', 'articlephoto_upload');

// Size = 0 for original, otherwise image width
function get_the_article_photo($size)
{
    global $post;
    $rawurl = get_post_meta($post->ID, '_articlephoto_url', true);
    $rawfile = get_post_meta($post->ID, '_articlephoto_filename', true);
    if ($rawurl == '')
    {
        return FALSE;
    }
    $image = new Imagick($rawfile);
    if ($size == 0 || $size >= $image->getImageWidth())
    {
        return $rawurl;
    }
    if (file_exists($rawfile . '-' . $size))
    {
        return $rawurl . '-' . $size;
    }
    $image->thumbnailImage($size, 0);
    $image->writeImage($rawfile . '-' . $size);
    return $rawurl . '-' . $size;
}

function the_article_photo($size)
{
    $p = get_the_article_photo($size);
    if ($p === FALSE)
    {
        $p = get_bloginfo('template_directory') . '/img/box.png';
    }
    echo $p;
}

function get_the_article_photo_caption()
{
    global $post;
    return get_post_meta($post->ID, '_articlephoto_caption', true);
}

function the_article_photo_caption()
{
    echo get_the_article_photo_caption();
}

function get_the_article_img($size)
{
    $p = get_the_article_photo($size);
    if ($p === false)
    {
        return '';
    }
    else
    {
        $c = get_the_article_photo_caption();
        return '<img src="' . $p . '" alt="' . $c . '" title="' . $c . '" />';
    }
}

function the_article_img($size)
{
    echo get_the_article_img($size);
}

?>
