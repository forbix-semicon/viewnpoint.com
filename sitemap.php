<?php
declare(strict_types=1);

/**
 * XML sitemap for search engines. Served at /sitemap.xml via .htaccess rewrite.
 */
$siteUrl = "https://viewnpoint.com";

$pages = [
    [
        "loc" => $siteUrl . "/",
        "lastmod" => "2026-05-30",
        "changefreq" => "weekly",
        "priority" => "1.0",
    ],
    [
        "loc" => $siteUrl . "/blog",
        "lastmod" => "2026-05-30",
        "changefreq" => "weekly",
        "priority" => "0.9",
    ],
    [
        "loc" => $siteUrl . "/tech",
        "lastmod" => "2026-05-30",
        "changefreq" => "monthly",
        "priority" => "0.8",
    ],
    [
        "loc" => $siteUrl . "/the-vertical-trap-why-indias-development-is-heating-up-and-burning-out",
        "lastmod" => "2026-05-22",
        "changefreq" => "monthly",
        "priority" => "0.8",
    ],
    [
        "loc" => $siteUrl . "/the-paradox-of-progress-rethinking-indias-engineering-education",
        "lastmod" => "2026-04-30",
        "changefreq" => "monthly",
        "priority" => "0.8",
    ],
    [
        "loc" => "https://viewnpoint.com/software/tts/",
        "lastmod" => "2026-05-30",
        "changefreq" => "monthly",
        "priority" => "0.7",
    ],
    [
        "loc" => "https://viewnpoint.com/software/website-rank-checker/website-rank-checker.php",
        "lastmod" => "2026-05-30",
        "changefreq" => "monthly",
        "priority" => "0.7",
    ],
];

header("Content-Type: application/xml; charset=UTF-8");

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($pages as $page) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($page["loc"], ENT_XML1) . "</loc>\n";
    echo "    <lastmod>" . htmlspecialchars($page["lastmod"], ENT_XML1) . "</lastmod>\n";
    echo "    <changefreq>" . htmlspecialchars($page["changefreq"], ENT_XML1) . "</changefreq>\n";
    echo "    <priority>" . htmlspecialchars($page["priority"], ENT_XML1) . "</priority>\n";
    echo "  </url>\n";
}

echo "</urlset>\n";
