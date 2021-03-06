<?php

    include("../index.php");

    use \semmelsamu\Router;
    use \semmelsamu\Route;

    $router = new Router();

    $router->add([
        new Route(id: "start"),
        new Route(id: "blog", url: "blog", file: "blog.php"),
        new Route(id: "about", url: "about", file: "about.php"),
        new Route(url: "about-us", goto: "about"),
        new Route(id: "edit_post", url: "/blog\/id\/([0-9]*)\/edit/", file: "edit_post.php"),
        new Route(id: "comments", url: "/blog\/id\/([0-9]*)\/comments\/([0-9]*)/", file: "comments.php")
    ]);

    $router->route();

    if(!$router->result->file_is_php) $router->output();

?>

<!-- Basic styling, nothing fancy -->
<style> body { padding: 32px; box-sizing: border-box; max-width: 1280px; } </style>

<base href="<?= $router->base() ?>">

<ul>
    <li><a href="<?= $router->id("start") ?>">Start</a></li>
    <li><a href="<?= $router->id("blog") ?>">Blog</a></li>
    <li><a href="<?= $router->id("about") ?>">About</a></li>
    <li><a href="sitemap.xml">/sitemap.xml</a></li>
</ul>

<hr>

<?= $router->output(); ?>

<hr>

<p>Here are Some useful router functions:</p>
<?php global $router; ?>

<p>url()</p>
<?php db($router->url()); ?>

<p>base()</p>
<?php db($router->base()); ?>

<p><a href="<?= $router->id("blog") ?>">id("blog")</a></p>
<?php db($router->id("blog")); ?>