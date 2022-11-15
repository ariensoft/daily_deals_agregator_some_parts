<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" count="<? echo count($urls); ?>">
    <?php
    if (!empty($urls)) {
        foreach ($urls as $url) {
            echo "<url> \n";
            echo "<loc> \n";
            echo $url['loc'] . "\n";
            echo "</loc> \n";
            echo "<lastmod> \n";
            echo $url['lastmod'] . "\n";
            echo "</lastmod> \n";
            echo "<changefreq> \n";
            echo $url['changefreq'] . "\n";
            echo "</changefreq> \n";
            echo "<priority> \n";
            echo $url['priority'] . "\n";
            echo "</priority> \n";
            echo "</url> \n";
        }
    }
    ?>
</urlset>