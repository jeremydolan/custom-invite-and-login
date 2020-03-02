<?php
/* Template Name: Authorised */
ob_start();
get_header('authorised');
?>

<main id="content">

  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

      	<header class="header">

      	  <h1 class="entry-title"><?php
              // check if we should display the registration title or the login title
              if( isset( $_GET['invbractiveuser'] ) ) {
                echo 'Beta access invitation';
              } else {
                the_title();
              }?>
            </h1>

          </header>

          <div class="row-authorised">

            <div class="law-society-login">
              <?php
              // display the registration form or the login form
              ( isset( $_GET['invbractiveuser'] ) ) ?

                // include( get_template_directory() . '/TLS-includes/registration.php')
                // moved into plugin instead of theme
                include( dirname( __FILE__ )  . '/registration.php' )
              :
                wp_login_form( array('redirect' => home_url()) )
              ;

              // check if we should display the registration title or the login title
              if( isset( $_GET['invbractiveuser'] ) ) {
                // no content to show on registration version of page
              } else {
                ?>
                <p><a class="login-trouble" href="<?php echo wc_lostpassword_url();?>">Having trouble logging in?</a></p>

                <?php
                the_content();
              }
            ?>
          </div>

            <div class="entry-links"><?php wp_link_pages(); ?></div>

          </div>


    </article>

  <?php endwhile; endif; ?>

</main>

<?php
get_footer('authorised');
ob_flush();
?>
