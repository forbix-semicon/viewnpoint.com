<?php
declare(strict_types=1);

$siteName = "ViewNPoint";
$siteHomeTitle = "ViewNPoint — Practical Tools and Thoughtful Writing";
$siteTagline = "Practical tools and thoughtful writing for thinkers, and curious minds.";
$siteFooterTagline = $siteTagline;
$basePath = rtrim(str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"] ?? "")), "/");
$requestPath = parse_url($_SERVER["REQUEST_URI"] ?? "/", PHP_URL_PATH) ?: "/";

if ($basePath !== "" && $basePath !== "/" && strpos($requestPath, $basePath) === 0) {
    $requestPath = substr($requestPath, strlen($basePath));
}

$requestPath = "/" . trim((string) $requestPath, "/");
if ($requestPath === "//" || $requestPath === "") {
    $requestPath = "/";
}

require_once __DIR__ . "/comments/bootstrap.php";
require_once __DIR__ . "/includes/blog.php";
require_once __DIR__ . "/includes/static-pages.php";

$software = [
    [
        "slug" => "hearing-frequency-lab",
        "title" => "Hearing Frequency Lab",
        "path" => "/hearing-frequency-and-biological-age",
        "description" => "Test the highest tone you can hear and get a rough biological / ear-age estimate — then share your result.",
        "image" => "/blog/hearing-frequency-and-biological-age.jpg",
    ],
    [
        "slug" => "tts",
        "title" => "Text To Speech (TTS)",
        "path" => "https://viewnpoint.com/software/tts/",
        "description" => "Convert text into natural sounding voice in seconds for videos, training, and quick content workflows.",
        "image" => "/blog/text-to-speech.jpg",
    ],
    [
        "slug" => "website-rank-checker",
        "title" => "Website Rank Checker",
        "path" => "https://viewnpoint.com/software/website-rank-checker/website-rank-checker.php",
        "description" => "Track keyword visibility and website rank trends with a clean, no-noise dashboard for SEO decisions.",
        "image" => "https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80",
    ],
];

$blogPosts = require __DIR__ . "/data/posts.php";

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

function url(string $path, string $basePath): string
{
    return ($basePath === "" ? "" : $basePath) . ($path === "/" ? "/" : $path);
}

function assetUrl(string $path, string $basePath): string
{
    return strpos($path, "http") === 0 ? $path : url($path, $basePath);
}

function assetVersion(string $relativePath): string
{
    $full = __DIR__ . $relativePath;

    return is_file($full) ? (string) filemtime($full) : "1";
}

function stylesheetUrl(string $file, string $basePath): string
{
    return assetUrl("/assets/css/" . $file, $basePath) . "?v=" . assetVersion("/assets/css/" . $file);
}

function scriptUrl(string $file, string $basePath): string
{
    return assetUrl("/assets/js/" . $file, $basePath) . "?v=" . assetVersion("/assets/js/" . $file);
}

function siteOrigin(): string
{
    $https = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off")
        || (($_SERVER["HTTP_PORT"] ?? "") === "443")
        || (($_SERVER["HTTP_X_FORWARDED_PROTO"] ?? "") === "https");
    $host = $_SERVER["HTTP_HOST"] ?? "viewnpoint.com";

    return ($https ? "https" : "http") . "://" . $host;
}

function absoluteUrl(string $path, string $basePath): string
{
    return siteOrigin() . url($path, $basePath);
}

function blogPostImage(array $post, string $basePath): string
{
    $image = $post["image"] ?? "";
    if ($image !== "" && strpos($image, "http") !== 0) {
        $localPath = __DIR__ . $image;
        if (is_file($localPath)) {
            return assetUrl($image, $basePath);
        }
    }

    if (!empty($post["imageFallback"])) {
        return $post["imageFallback"];
    }

    return $image !== "" ? assetUrl($image, $basePath) : "";
}

function articleSeo(array $post, string $basePath, string $siteName): array
{
    $canonical = absoluteUrl($post["path"], $basePath);
    $image = blogPostImage($post, $basePath);
    $description = $post["seoDescription"] ?? ($post["excerpt"] ?? "");
    $jsonLd = json_encode([
        "@context" => "https://schema.org",
        "@type" => "Article",
        "headline" => $post["title"],
        "description" => $description,
        "image" => [$image],
        "author" => [
            "@type" => "Person",
            "name" => $post["author"] ?? "JB",
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => $siteName,
        ],
        "datePublished" => $post["published"] ?? "",
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => $canonical,
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    return [
        "canonical" => $canonical,
        "og_type" => "article",
        "og_image" => $image,
        "og_title" => $post["title"],
        "og_description" => $description,
        "keywords" => $post["keywords"] ?? "",
        "json_ld" => $jsonLd ?: "",
    ];
}

function renderHeader(string $title, string $description, array $seo = [], string $pageStyle = "listing", array $extraStyles = []): void
{
    global $basePath;

    $canonical = $seo["canonical"] ?? null;
    $ogType = $seo["og_type"] ?? "website";
    $ogImage = $seo["og_image"] ?? null;
    $keywords = $seo["keywords"] ?? "";
    $robots = $seo["robots"] ?? "index, follow";
    $jsonLd = $seo["json_ld"] ?? null;
    $stylesheets = ["base.css"];
    if ($pageStyle === "listing" || $pageStyle === "article") {
        $stylesheets[] = "listing.css";
    }
    if ($pageStyle === "article") {
        $stylesheets[] = "article.css";
    }
    foreach ($extraStyles as $sheet) {
        if (is_string($sheet) && $sheet !== "" && !in_array($sheet, $stylesheets, true)) {
            $stylesheets[] = $sheet;
        }
    }
    ?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?></title>
    <meta name="description" content="<?= e($description) ?>">
    <meta name="robots" content="<?= e($robots) ?>">
    <?php if ($keywords !== ""): ?>
    <meta name="keywords" content="<?= e($keywords) ?>">
    <?php endif; ?>
    <?php if ($canonical): ?>
    <link rel="canonical" href="<?= e($canonical) ?>">
    <?php endif; ?>
    <meta property="og:title" content="<?= e($seo["og_title"] ?? $title) ?>">
    <meta property="og:description" content="<?= e($seo["og_description"] ?? $description) ?>">
    <meta property="og:type" content="<?= e($ogType) ?>">
    <?php if ($canonical): ?>
    <meta property="og:url" content="<?= e($canonical) ?>">
    <?php endif; ?>
    <?php if ($ogImage): ?>
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <?php endif; ?>
    <?php if ($ogImage && $pageStyle === "article"): ?>
    <link rel="preload" as="image" href="<?= e($ogImage) ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($seo["og_title"] ?? $title) ?>">
    <meta name="twitter:description" content="<?= e($seo["og_description"] ?? $description) ?>">
    <?php if ($ogImage): ?>
    <meta name="twitter:image" content="<?= e($ogImage) ?>">
    <?php endif; ?>
    <?php if ($jsonLd): ?>
    <script type="application/ld+json"><?= $jsonLd ?></script>
    <?php endif; ?>
    <link rel="icon" href="<?= e(url("/img/favicon.ico", $basePath)) ?>" sizes="any">
    <link rel="icon" type="image/png" href="<?= e(url("/img/favicon-light.png", $basePath)) ?>" sizes="32x32">
    <link rel="apple-touch-icon" href="<?= e(url("/img/favicon-light.png", $basePath)) ?>">
    <?php foreach ($stylesheets as $sheet): ?>
    <link rel="stylesheet" href="<?= e(stylesheetUrl($sheet, $basePath)) ?>">
    <?php endforeach; ?>
    <script>
        (function () {
            var key = "viewnpoint-theme";
            var stored = localStorage.getItem(key);
            var theme = stored === "light" || stored === "dark" ? stored : "dark";
            document.documentElement.setAttribute("data-theme", theme);
        })();
    </script>
</head>
<body>
<?php
}

function renderBrandMark(string $basePath, string $siteName, ?string $href = null): void
{
    $tag = $href !== null ? "a" : "div";
    $attrs = $href !== null
        ? ' class="brand" href="' . e(url("/", $basePath)) . '"'
        : ' class="brand brand--static"';
    ?>
    <<?= $tag ?><?= $attrs ?>>
        <img class="brand-logo brand-logo--dark-theme" src="<?= e(url("/img/viewnpoint-logo-white.png", $basePath)) ?>" width="42" height="42" alt="" decoding="async" fetchpriority="high">
        <img class="brand-logo brand-logo--light-theme" src="<?= e(url("/img/viewnpoint-logo-dark.png", $basePath)) ?>" width="42" height="42" alt="" decoding="async">
        <span class="brand-name"><?= e($siteName) ?></span>
    <<?= $tag ?>>
    <?php
}

function renderFooter(array $extraScripts = []): void
{
    global $basePath, $siteName, $siteFooterTagline;
    ?>
<footer>
    <div class="container footer-inner">
        <?php renderBrandMark($basePath, $siteName, "/"); ?>
        <p class="footer-tagline">
            <?= e($siteFooterTagline) ?>
            · <a class="footer-link" href="https://www.forbixindia.com" rel="noopener noreferrer">FORBIX SEMICON</a>
        </p>
        <p class="footer-links">
            <a class="footer-link" href="<?= e(url("/about", $basePath)) ?>">About</a>
            <a class="footer-link" href="<?= e(url("/contact", $basePath)) ?>">Contact</a>
            <a class="footer-link" href="<?= e(url("/privacy", $basePath)) ?>">Privacy</a>
        </p>
    </div>
</footer>
<script src="<?= e(scriptUrl("theme.js", $basePath)) ?>" defer></script>
<?php foreach ($extraScripts as $script): ?>
<?php if (is_string($script) && $script !== ""): ?>
<script src="<?= e(scriptUrl($script, $basePath)) ?>" defer></script>
<?php endif; ?>
<?php endforeach; ?>
</body>
</html>
<?php
}

function renderBlogEditorialCard(array $post, string $basePath, bool $lazyImage = true): void
{
    $href = url($post["path"], $basePath);
    $img = blogPostImage($post, $basePath);
    $imgAttrs = $lazyImage ? ' loading="lazy" decoding="async"' : ' decoding="async"';
    ?>
    <article class="editorial-card">
        <a class="editorial-card-link" href="<?= e($href) ?>">
            <?php if ($img !== ""): ?>
            <span class="editorial-thumb-wrap">
                <img class="editorial-thumb" src="<?= e($img) ?>" alt="" width="112" height="75"<?= $imgAttrs ?>>
            </span>
            <?php endif; ?>
            <span class="editorial-body">
                <h3><?= e($post["title"]) ?></h3>
                <p><?= e($post["excerpt"]) ?></p>
                <?php if (!empty($post["readTime"])): ?>
                <span class="meta"><?= e($post["readTime"]) ?></span>
                <?php endif; ?>
            </span>
        </a>
    </article>
    <?php
}

function isActive(string $requestPath, string $target): bool
{
    return $requestPath === $target || ($target !== "/" && strpos($requestPath, $target . "/") === 0);
}

function renderNav(string $basePath, string $requestPath, string $siteName): void
{
    ?>
<header>
    <div class="container topbar">
        <?php renderBrandMark($basePath, $siteName, "/"); ?>
        <div class="topbar-actions">
            <label class="theme-switch" for="theme-toggle" title="Toggle light and dark mode">
                <span class="theme-icon" aria-hidden="true">☀</span>
                <input type="checkbox" id="theme-toggle" role="switch" aria-checked="true" aria-label="Dark mode">
                <span class="theme-slider"></span>
                <span class="theme-icon" aria-hidden="true">☾</span>
            </label>
            <nav>
                <a class="<?= isActive($requestPath, "/") && !isActive($requestPath, "/blog") && !isActive($requestPath, "/tech") && !isActive($requestPath, "/software") && !isActive($requestPath, "/about") && !isActive($requestPath, "/contact") && !isActive($requestPath, "/privacy") ? "active" : "" ?>" href="<?= e(url("/", $basePath)) ?>">Home</a>
                <a class="<?= isActive($requestPath, "/blog") ? "active" : "" ?>" href="<?= e(url("/blog", $basePath)) ?>">Blog</a>
                <a class="<?= isActive($requestPath, "/tech") || isActive($requestPath, "/software") ? "active" : "" ?>" href="<?= e(url("/tech", $basePath)) ?>">Technology</a>
                <a class="<?= isActive($requestPath, "/about") ? "active" : "" ?>" href="<?= e(url("/about", $basePath)) ?>">About</a>
                <a class="<?= isActive($requestPath, "/contact") ? "active" : "" ?>" href="<?= e(url("/contact", $basePath)) ?>">Contact</a>
            </nav>
        </div>
    </div>
</header>
<?php
}

function renderHome(string $basePath, string $siteName, string $siteHomeTitle, string $siteTagline, array $software, array $blogPosts, string $requestPath): void
{
    renderHeader($siteHomeTitle, $siteTagline);
    renderNav($basePath, $requestPath, $siteName);
    ?>
<main class="container">
    <section class="hero">
        <span class="badge">ViewNPoint</span>
        <h1>Practical software and thoughtful writing for curious minds.</h1>
        <p><?= e($siteTagline) ?></p>
        <div class="cta-row">
            <a class="btn btn-primary" href="<?= e(url("/blog", $basePath)) ?>">Read Editorials</a>
            <a class="btn btn-muted" href="<?= e(url("/tech", $basePath)) ?>">Explore Software</a>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h2>Featured Editorials</h2>
            <p>Essays on engineering, urban development, and innovation in India.</p>
        </div>
        <div class="editorial-grid editorial-grid--multi">
            <?php foreach ($blogPosts as $post): ?>
                <?php renderBlogEditorialCard($post, $basePath); ?>
            <?php endforeach; ?>
        </div>
        <div class="cta-row">
            <a class="btn btn-muted" href="<?= e(url("/blog", $basePath)) ?>">All Blog Posts</a>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h2>Featured Software</h2>
            <p>Useful web tools to make digital work easier.</p>
        </div>
        <div class="grid">
        <?php foreach ($software as $item): ?>
            <?php
            $isExternal = strpos($item["path"], "http") === 0;
            $itemHref = $isExternal ? $item["path"] : url($item["path"], $basePath);
            $itemTarget = $isExternal ? ' target="_blank" rel="noopener noreferrer"' : '';
            $itemImg = strpos($item["image"], "http") === 0 ? $item["image"] : url($item["image"], $basePath);
            ?>
            <article class="card col-6">
                <a href="<?= e($itemHref) ?>"<?= $itemTarget ?> aria-label="<?= e($item["title"]) ?>">
                    <div class="img-wrap">
                        <img src="<?= e($itemImg) ?>" alt="<?= e($item["title"]) ?>" width="640" height="360" loading="lazy" decoding="async">
                    </div>
                </a>
                <div class="card-content">
                    <h3><a class="card-link" href="<?= e($itemHref) ?>"<?= $itemTarget ?>><?= e($item["title"]) ?></a></h3>
                    <p><?= e($item["description"]) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
    </section>
</main>
<?php
    renderFooter();
}

function renderSoftwarePage(string $basePath, string $siteName, array $software, string $requestPath): void
{
    renderHeader("Technology | " . $siteName, "Software products from ViewNPoint.");
    renderNav($basePath, $requestPath, $siteName);
    ?>
<main class="container">
    <section class="hero">
        <span class="badge">Technology</span>
        <h1>Tools built to make digital work easier.</h1>
        <p>More software products are on the roadmap. These are our first featured releases.</p>
    </section>
    <section class="grid">
        <?php foreach ($software as $item): ?>
            <?php
            $isExternal = strpos($item["path"], "http") === 0;
            $itemHref = $isExternal ? $item["path"] : url($item["path"], $basePath);
            $itemTarget = $isExternal ? ' target="_blank" rel="noopener noreferrer"' : '';
            $itemImg = strpos($item["image"], "http") === 0 ? $item["image"] : url($item["image"], $basePath);
            ?>
            <article class="card col-6">
                <a href="<?= e($itemHref) ?>"<?= $itemTarget ?> aria-label="<?= e($item["title"]) ?>">
                    <div class="img-wrap">
                        <img src="<?= e($itemImg) ?>" alt="<?= e($item["title"]) ?>" width="640" height="360" loading="lazy" decoding="async">
                    </div>
                </a>
                <div class="card-content">
                    <h3><a class="card-link" href="<?= e($itemHref) ?>"<?= $itemTarget ?>><?= e($item["title"]) ?></a></h3>
                    <p><?= e($item["description"]) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</main>
<?php
    renderFooter();
}

function renderBlogPage(string $basePath, string $siteName, array $blogPosts, string $requestPath): void
{
    renderHeader(
        "Blog | " . $siteName,
        "Editorial posts on engineering, urban development, innovation, and technology from ViewNPoint.",
        [
            "canonical" => absoluteUrl("/blog", $basePath),
            "og_title" => "ViewNPoint Blog",
            "og_description" => "Thoughtful essays on India's engineering, urban development, and startup ecosystems.",
            "keywords" => "ViewNPoint blog, India engineering, urban development India, hardware startups, innovation essays",
        ]
    );
    renderNav($basePath, $requestPath, $siteName);
    ?>
<main class="container">
    <section class="hero">
        <span class="badge">Blog</span>
        <h1>Thoughtful writing on engineering, innovation, and technology.</h1>
        <p>Short previews of each post — open any article for the full read.</p>
    </section>
    <section class="section">
        <div class="editorial-grid">
        <?php foreach ($blogPosts as $post): ?>
            <?php renderBlogEditorialCard($post, $basePath); ?>
        <?php endforeach; ?>
        </div>
    </section>
</main>
<?php
    renderFooter();
}

function renderNotFound(string $basePath, string $siteName, string $requestPath): void
{
    http_response_code(404);
    renderHeader("404 | " . $siteName, "Page not found");
    renderNav($basePath, $requestPath, $siteName);
    ?>
<main class="container">
    <section class="hero">
        <span class="badge">404</span>
        <h1>Page not found</h1>
        <p>Use the navigation to continue browsing ViewNPoint content.</p>
    </section>
</main>
<?php
    renderFooter();
}

if ($requestPath === "/") {
    renderHome($basePath, $siteName, $siteHomeTitle, $siteTagline, $software, $blogPosts, $requestPath);
    exit;
}

if ($requestPath === "/tech" || $requestPath === "/software") {
    renderSoftwarePage($basePath, $siteName, $software, $requestPath);
    exit;
}

if ($requestPath === "/blog") {
    renderBlogPage($basePath, $siteName, $blogPosts, $requestPath);
    exit;
}

if ($requestPath === "/about") {
    renderAboutPage($basePath, $siteName, $requestPath);
    exit;
}

if ($requestPath === "/privacy") {
    renderPrivacyPage($basePath, $siteName, $requestPath);
    exit;
}

if ($requestPath === "/contact") {
    renderContactPage($basePath, $siteName, $requestPath);
    exit;
}

$matchedPost = findBlogPostByPath($blogPosts, $requestPath);
if ($matchedPost !== null) {
    renderArticle($basePath, $siteName, $requestPath, $matchedPost);
    exit;
}

renderNotFound($basePath, $siteName, $requestPath);
