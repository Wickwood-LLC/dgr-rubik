<header id="header">
  <a href="/" rel="home" id="logo">
    <img src="<?php print $logo; ?>" />
  </a>
  <h1 id="site-name">
    <?php print $site_name; ?>
  </h1>
  <h2 id="site-slogan">
    <?php print $site_slogan; ?>
  </h2>
  <?php print render($page['header']); ?>
  <?php print render($page['navbar']); ?>
</header>
<?php if (theme_get_setting('rubik_show_branding')): ?>
  <div id='branding'><div class='limiter clearfix'>  
    <?php if ((arg(0) == 'user' && arg(1) == 'login') || (arg(0) == 'user' && arg(1) == 'password')): ?>
      <div class='breadcrumb clearfix'></div>
    <?php else: ?>
      <div class='breadcrumb clearfix'><?php print $breadcrumb ?></div>
    <?php endif; ?>
  
    <?php if (!$overlay && isset($secondary_menu)) : ?>
      <?php print theme('links', array('links' => $secondary_menu, 'attributes' => array('class' => 'links secondary-menu'))) ?>
    <?php endif; ?>
  </div></div>
<?php endif; ?>

<div id='page-title'><div class='limiter clearfix'>
  <div class='tabs clearfix'>
    <?php if ($primary_local_tasks): ?>
      <ul class='primary-tabs links clearfix'><?php print render($primary_local_tasks) ?></ul>
    <?php endif; ?>
  </div>
  <?php print render($title_prefix); ?>
  <h1 class='page-title <?php print $page_icon_class ?>'>
    <?php if (!empty($page_icon_class)): ?><span class='icon'></span><?php endif; ?>
    <?php if ($title) print $title ?>
  </h1>
  <?php if ($action_links): ?>
    <ul class='action-links links clearfix'><?php print render($action_links) ?></ul>
  <?php endif; ?>
  <?php print render($title_suffix); ?>
</div></div>

<?php if ($show_messages && $messages): ?>
<div id='console'><div class='limiter clearfix'><?php print $messages; ?></div></div>
<?php endif; ?>

<div id='page'><div id='main-content' class='limiter clearfix'>
  <?php if ($page['help']) print render($page['help']) ?>
  <div id='content' class='page-content clearfix'>
    <?php if (!empty($page['content'])) print render($page['content']) ?>
  </div>
</div></div>

<div id='footer' class='clearfix'>
  <?php if ($feed_icons): ?>
    <div class='feed-icons clearfix'>
      <label><?php print t('Feeds') ?></label>
      <?php print $feed_icons ?>
    </div>
  <?php endif; ?>
</div>

<?php print render($page['footer']); ?>
