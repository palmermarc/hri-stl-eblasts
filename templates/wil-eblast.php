<?php get_header(); ?>
<style>
    .html { margin: 1em 0; color: blue; cursor: hand; cursor: pointer; font-size: small; }
</style>

<div id="primary" class="site-content">
    <div id="content" role="main">
    
        <?php if( have_posts() ) : ?>
            <?php while( have_posts() ) : the_post(); ?>
                <?php $core_items = str_replace( "\'", "/'", get_post_meta( get_the_ID(), 'core_items', TRUE ) ); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    <br /><br /><br />
                    
                    <div style="width: 50%; float: left;">
                        <h2>HTML Version</h2>
                        <textarea id="html_version" cols="45" rows="15">
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#2a1909">
    <tr>
        <td width="100%">
            <table cellpadding="0" border="0" cellspacing="0" align="center">
                <tr>
                    <td colspan="5"><img src="http://media.dev-cms.com/stl/5/532/53239.png" alt="" /></td>
                </tr>
                <tr >
                    <td><img src="http://media.dev-cms.com/stl/5/532/53240.png" alt="" /></td>
                    <td><a href="http://www.wil92.com/local-concerts/"><img src="http://media.dev-cms.com/stl/5/532/53228.jpg" alt="" /></a></td>
                    <td><a href="http://rewards.wil92.com/asp3/contests.aspx"><img src="http://media.dev-cms.com/stl/5/532/53229.png" alt="" /></a></td>
                    <td><a href="http://www.wil92.com/"><img src="http://media.dev-cms.com/stl/5/532/53241.png" alt="" /></a></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table cellpadding="0" border="0" cellspacing="0" width="759" align="center">
                <tr bgcolor="#e9dbce">
                    <td align="center">
                        <a href="<?php echo get_post_meta( get_the_ID(), 'leaderboard_link', TRUE ); ?>">
                            <img src="<?php echo get_post_meta( get_the_ID(), 'leaderboard_image', TRUE ); ?>" />
                        </a>
                    </td>
                </tr>
                <tr bgcolor="#e9dbce">
                    <td align="center">
                        <a href="<?php echo get_post_meta( get_the_ID(), 'featured_link', TRUE ); ?>">
                            <img src="<?php echo get_post_meta( get_the_ID(), 'featured_image', TRUE ); ?>" />
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table cellpadding="25" border="0" cellspacing="0" width="759" align="center">
                    <tr bgcolor="#e9dbce">
                        <td>
                            <table cellspacing="10">
                                <?php foreach( json_decode( $core_items ) as $item ) : ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo $item->link; ?>"><img src="<?php echo $item->image; ?>" width="160" height="160" /></a>
                                    </td>
                                    <td><?php echo $item->text; ?> <a href="<?php echo $item->link; ?>"><br />Click here to learn more!</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                        </td>
                    </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table cellpadding="0" border="0" cellspacing="0" width="759" bgcolor="#e9dbce" align="center">
                <tr>
                    <td align="right"><a href="<?php echo get_post_meta( get_the_ID(), 'footer_left_link', TRUE ); ?>"><img src="<?php echo get_post_meta( get_the_ID(), 'footer_left_image', TRUE ); ?>" /></a></td>
                    <td align="left"><a href="<?php echo get_post_meta( get_the_ID(), 'footer_right_link', TRUE ); ?>"><img src="<?php echo get_post_meta( get_the_ID(), 'footer_right_image', TRUE ); ?>" /></a></td>
                </tr>
                <tr>
                    <td align="center" style="padding: 15px;"></td>
                </tr>
                <tr bgcolor="#e9dbce" align="center">
                    <td colspan="2">            
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding: 15px;"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
                        </textarea>
                        <p class="html">View HTML Version</p>
                    </div>
                    
                    <div style="width: 50%; float: left;">
                        <h2>Text Version</h2>
                        <textarea cols="45" rows="15">
<?php echo get_post_meta( get_the_ID(), 'leaderboard_text', TRUE ); ?>


Learn More: <?php echo get_post_meta( get_the_ID(), 'leaderboard_link', TRUE ); ?>



<?php echo get_post_meta( get_the_ID(), 'featured_text', TRUE ); ?>


Learn More: <?php echo get_post_meta( get_the_ID(), 'featured_link', TRUE ); ?>


<?php foreach( json_decode( $core_items ) as $item_id => $item ) : ?>
    <?php var_dump( $item ); ?>

<?php echo $item->text; ?>


Learn More: <?php echo $item->link; ?>

<?php endforeach; ?>



<?php echo get_post_meta( get_the_ID(), 'footer_left_text', TRUE ); ?>


Learn More: <?php echo get_post_meta( get_the_ID(), 'footer_left_link', TRUE ); ?>



<?php echo get_post_meta( get_the_ID(), 'footer_right_text', TRUE ); ?>


Learn More: <?php echo get_post_meta( get_the_ID(), 'footer_right_link', TRUE ); ?>
                        </textarea>
                    </div>
                    <div style="clear: both;"></div>
                </article>
                <div style="clear: both;"></div>
            <?php endwhile; ?>
        <?php endif; ?>

    </div>
</div>

<script>
    jQuery('.html').click(function() {
        var w = window.open();
        var data = jQuery('#html_version').val();
        w.document.write(data);
    });
</script>

<?php get_footer(); ?>