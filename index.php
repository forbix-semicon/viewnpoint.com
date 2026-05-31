<?php
declare(strict_types=1);

$siteName = "ViewNPoint";
$siteTagline = "Attractive blog and software showcase for smart web tools.";
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

$software = [
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

$blogPosts = [
    [
        "slug" => "vertical-trap",
        "title" => "The Vertical Trap: Why India's Development Is Heating Up—And Burning Out",
        "path" => "/the-vertical-trap-why-indias-development-is-heating-up-and-burning-out",
        "excerpt" => "Glass towers, broken commutes, and sky-high rents are squeezing India's hardware startups—and the idea of living well.",
        "snapshot" => "How vertical urban development traps heat, burns out workers, and pushes founders toward software-only models instead of medical devices, robotics, and real manufacturing.",
        "image" => "/blog/the-vertical-trap-why-indias-development-is-heating-up-and-burning-out.jpg",
        "imageFallback" => "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1200&q=80",
        "readTime" => "8 min read",
        "published" => "2026-05-22",
        "author" => "JB",
        "seoDescription" => "India's glass towers, commute chaos, and commercial rents are heating cities and burning out startups. Explore the vertical trap and what must change for hardware innovation.",
        "keywords" => "India urban development, glass buildings heat, startup rent Mumbai Bengaluru, hardware startups India, work life balance India, maker spaces India",
    ],
    [
        "slug" => "paradox-of-success",
        "title" => "The Paradox of Progress: Rethinking India's Engineering Education",
        "path" => "/the-paradox-of-progress-rethinking-indias-engineering-education",
        "excerpt" => "A critical look at progress and what engineering education in India needs to deliver for the future.",
        "snapshot" => "A data-backed perspective on India's engineering education shift, the role of AI, and practical ideas for rebuilding product-first core engineering ecosystems.",
        "image" => "/blog/the-paradox-of-progress-rethinking-indias-engineering-education.jpg",
        "readTime" => "6 min read",
        "published" => "2026-04-30",
        "author" => "JB",
        "seoDescription" => "A data-backed look at India's engineering education shift, core branch decline, AI's role, and a roadmap for product-first innovation.",
        "keywords" => "India engineering education, core engineering decline, AI engineering India, IIT seat trends, hardware innovation India",
    ],
];

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

function findBlogPost(array $blogPosts, string $path): ?array
{
    foreach ($blogPosts as $post) {
        if (($post["path"] ?? "") === $path) {
            return $post;
        }
    }

    return null;
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

function renderHeader(string $title, string $description, array $seo = []): void
{
    $canonical = $seo["canonical"] ?? null;
    $ogType = $seo["og_type"] ?? "website";
    $ogImage = $seo["og_image"] ?? null;
    $keywords = $seo["keywords"] ?? "";
    $robots = $seo["robots"] ?? "index, follow";
    $jsonLd = $seo["json_ld"] ?? null;
    ?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($seo["og_title"] ?? $title) ?>">
    <meta name="twitter:description" content="<?= e($seo["og_description"] ?? $description) ?>">
    <?php if ($ogImage): ?>
    <meta name="twitter:image" content="<?= e($ogImage) ?>">
    <?php endif; ?>
    <?php if ($jsonLd): ?>
    <script type="application/ld+json"><?= $jsonLd ?></script>
    <?php endif; ?>
    <script>
        (function () {
            var key = "viewnpoint-theme";
            var stored = localStorage.getItem(key);
            var theme = stored === "light" || stored === "dark" ? stored : "dark";
            document.documentElement.setAttribute("data-theme", theme);
        })();
    </script>
    <style>
        [data-theme="dark"] {
            --bg: #09090f;
            --surface: #121420;
            --text: #f7f8fb;
            --muted: #a7b0c0;
            --accent: #7c8cff;
            --accent-2: #30d5c8;
            --border: rgba(255, 255, 255, 0.1);
            --shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
            --header-bg: rgba(9, 9, 15, 0.75);
            --glow-1: rgba(124, 140, 255, 0.35);
            --glow-2: rgba(48, 213, 200, 0.22);
            --card-top: rgba(255, 255, 255, 0.03);
            --card-bottom: rgba(255, 255, 255, 0.01);
            --badge-text: #dbeafe;
            --badge-bg: rgba(124, 140, 255, 0.2);
            --badge-border: rgba(124, 140, 255, 0.5);
            --nav-active-text: #fff;
            --btn-muted-text: #d9deeb;
            --btn-muted-bg: rgba(255, 255, 255, 0.03);
            --meta: #9aa8c3;
            --article-text: #d6deef;
            --article-byline: #9aa8c3;
            --article-em: #f5d58d;
            --article-strong: #ffffff;
            --table-surface: rgba(255, 255, 255, 0.02);
            --table-title-bg: rgba(124, 140, 255, 0.15);
            --table-title-text: #f0f3ff;
            --table-cell-border: rgba(255, 255, 255, 0.08);
            --table-cell-text: #dde4f4;
            --table-head-bg: rgba(48, 213, 200, 0.12);
            --table-head-text: #f8fbff;
            --table-note: #aeb9cd;
            --toggle-track: rgba(255, 255, 255, 0.12);
            --toggle-thumb: #f7f8fb;
            --toggle-icon: #a7b0c0;
        }

        [data-theme="light"] {
            --bg: #f3f5fb;
            --surface: #ffffff;
            --text: #121420;
            --muted: #5a6478;
            --accent: #5b6ee6;
            --accent-2: #1fa89c;
            --border: rgba(18, 20, 32, 0.12);
            --shadow: 0 16px 40px rgba(18, 20, 32, 0.08);
            --header-bg: rgba(255, 255, 255, 0.88);
            --glow-1: rgba(91, 110, 230, 0.18);
            --glow-2: rgba(31, 168, 156, 0.14);
            --card-top: rgba(255, 255, 255, 0.95);
            --card-bottom: rgba(243, 245, 251, 0.9);
            --badge-text: #2f3f8f;
            --badge-bg: rgba(91, 110, 230, 0.12);
            --badge-border: rgba(91, 110, 230, 0.35);
            --nav-active-text: #fff;
            --btn-muted-text: #3a4558;
            --btn-muted-bg: rgba(18, 20, 32, 0.04);
            --meta: #6b778c;
            --article-text: #3a4558;
            --article-byline: #6b778c;
            --article-em: #9a6b12;
            --article-strong: #121420;
            --table-surface: rgba(18, 20, 32, 0.03);
            --table-title-bg: rgba(91, 110, 230, 0.1);
            --table-title-text: #1e2a5c;
            --table-cell-border: rgba(18, 20, 32, 0.08);
            --table-cell-text: #3a4558;
            --table-head-bg: rgba(31, 168, 156, 0.12);
            --table-head-text: #123b36;
            --table-note: #5a6478;
            --toggle-track: rgba(18, 20, 32, 0.12);
            --toggle-thumb: #ffffff;
            --toggle-icon: #5a6478;
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            font-family: "Inter", "Segoe UI", "Roboto", Arial, sans-serif;
            background:
                radial-gradient(circle at 10% -10%, var(--glow-1), transparent 45%),
                radial-gradient(circle at 95% 10%, var(--glow-2), transparent 50%),
                var(--bg);
            color: var(--text);
            line-height: 1.65;
            transition: background-color .25s ease, color .25s ease;
        }

        .container {
            width: min(1140px, calc(100% - 2rem));
            margin-inline: auto;
        }

        header {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(10px);
            background: var(--header-bg);
            border-bottom: 1px solid var(--border);
            transition: background-color .25s ease, border-color .25s ease;
        }

        .topbar {
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .theme-switch {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            cursor: pointer;
            user-select: none;
            flex-shrink: 0;
        }

        .theme-switch input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }

        .theme-slider {
            position: relative;
            width: 52px;
            height: 28px;
            background: var(--toggle-track);
            border: 1px solid var(--border);
            border-radius: 999px;
            transition: background-color .2s ease, border-color .2s ease;
        }

        .theme-slider::before {
            content: "";
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--toggle-thumb);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: transform .2s ease;
        }

        .theme-switch input:checked + .theme-slider::before {
            transform: translateX(24px);
        }

        .theme-icon {
            font-size: 1rem;
            line-height: 1;
            color: var(--toggle-icon);
        }

        .brand {
            color: var(--text);
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 0.04em;
        }

        nav {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
        }

        nav a {
            text-decoration: none;
            color: var(--muted);
            font-weight: 600;
            padding: .5rem .9rem;
            border-radius: 999px;
            transition: .2s ease;
        }

        nav a:hover,
        nav a.active {
            color: var(--nav-active-text);
            background: linear-gradient(90deg, var(--accent), var(--accent-2));
        }

        .hero {
            padding: clamp(2.4rem, 7vw, 5.2rem) 0 2.2rem;
        }

        .badge {
            display: inline-block;
            font-size: .82rem;
            font-weight: 700;
            color: var(--badge-text);
            background: var(--badge-bg);
            border: 1px solid var(--badge-border);
            padding: .35rem .7rem;
            border-radius: 999px;
        }

        h1 {
            margin: .9rem 0 0;
            font-size: clamp(2rem, 5.2vw, 3.7rem);
            line-height: 1.15;
        }

        .hero p {
            margin-top: 1.05rem;
            color: var(--muted);
            max-width: 70ch;
            font-size: 1.03rem;
        }

        .cta-row {
            display: flex;
            gap: .8rem;
            flex-wrap: wrap;
            margin-top: 1.4rem;
        }

        .btn {
            display: inline-flex;
            text-decoration: none;
            font-weight: 700;
            border-radius: .85rem;
            padding: .72rem 1.05rem;
        }

        .btn-primary {
            color: #fff;
            background: linear-gradient(90deg, var(--accent), #9b68ff);
        }

        .btn-muted {
            color: var(--btn-muted-text);
            border: 1px solid var(--border);
            background: var(--btn-muted-bg);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 1rem;
            padding-bottom: 3rem;
        }

        .card {
            grid-column: span 12;
            background: linear-gradient(180deg, var(--card-top), var(--card-bottom));
            border: 1px solid var(--border);
            border-radius: 1rem;
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: background .25s ease, border-color .25s ease, box-shadow .25s ease;
        }

        .card-content { padding: 1.1rem; }
        .card h3 { margin: 0 0 .45rem; }
        .card p { margin: 0; color: var(--muted); }
        .col-6 { grid-column: span 6; }
        .col-4 { grid-column: span 4; }

        .img-wrap {
            width: 100%;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            border-bottom: 1px solid var(--border);
        }

        .img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .img-wrap.trimmed {
            aspect-ratio: 18 / 7;
        }

        .meta {
            display: block;
            font-size: .82rem;
            color: var(--meta);
            margin-top: .5rem;
        }

        .article {
            margin-bottom: 3rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: clamp(1.2rem, 3vw, 2rem);
            transition: background-color .25s ease, border-color .25s ease;
        }

        .article h1 { font-size: clamp(1.6rem, 3.5vw, 2.2rem); margin-bottom: .3rem; }
        .article h2 { margin-top: 2rem; margin-bottom: .65rem; font-size: clamp(1.25rem, 2.2vw, 1.6rem); }
        .article p { color: var(--article-text); margin: 0 0 1rem; text-align: justify; }
        .article .byline { color: var(--article-byline); font-size: .92rem; margin-bottom: 1.2rem; }
        .article .section-break {
            border: 0;
            border-top: 1px solid var(--border);
            margin: 1.3rem 0 1.4rem;
        }
        .article ul { margin: 0 0 1rem 1.2rem; padding: 0; }
        .article li { margin-bottom: .6rem; color: var(--article-text); text-align: justify; }
        .article em { color: var(--article-em); }
        .article strong { color: var(--article-strong); }

        .table-card {
            margin: 1rem 0 1.25rem;
            border: 1px solid var(--border);
            border-radius: .8rem;
            overflow: hidden;
            background: var(--table-surface);
        }

        .table-title {
            margin: 0;
            padding: .8rem 1rem;
            background: var(--table-title-bg);
            color: var(--table-title-text);
            font-size: .98rem;
            border-bottom: 1px solid var(--border);
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 680px;
        }

        th, td {
            text-align: left;
            padding: .72rem .8rem;
            border-bottom: 1px solid var(--table-cell-border);
            color: var(--table-cell-text);
            vertical-align: top;
        }

        th {
            background: var(--table-head-bg);
            color: var(--table-head-text);
            font-size: .92rem;
        }

        .table-note {
            color: var(--table-note);
            font-size: .9rem;
            margin-top: .5rem;
            text-align: justify;
        }

        footer {
            border-top: 1px solid var(--border);
            padding: 1.1rem 0 2rem;
            color: var(--muted);
            font-size: .94rem;
        }

        .comments-box {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }

        .comments-box h2 {
            margin: 0 0 .45rem;
            font-size: clamp(1.25rem, 2.2vw, 1.6rem);
        }

        .comments-note,
        .comments-empty {
            color: var(--meta);
            font-size: .92rem;
        }

        .comment-list {
            display: grid;
            gap: .8rem;
            margin: 1rem 0;
        }

        .comment-item,
        .comment-form {
            border: 1px solid var(--border);
            border-radius: .85rem;
            background: var(--table-surface);
            padding: 1rem;
        }

        .comment-item p {
            margin-bottom: 0;
            text-align: left;
        }

        .comment-meta {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            color: var(--meta);
            font-size: .86rem;
            margin-bottom: .45rem;
        }

        .comment-auth-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .comment-form {
            display: grid;
            gap: .55rem;
        }

        .comment-form h3 {
            margin: 0 0 .25rem;
        }

        .comment-form label {
            color: var(--article-strong);
            font-weight: 700;
        }

        .comment-form input,
        .comment-form textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: .75rem;
            padding: .75rem .85rem;
            color: var(--text);
            background: var(--surface);
            font: inherit;
        }

        .comment-form textarea {
            resize: vertical;
        }

        .comment-flash,
        .comment-error {
            border-radius: .8rem;
            padding: .75rem .9rem;
            margin: .8rem 0;
            background: var(--table-title-bg);
            color: var(--article-text);
        }

        .comment-flash-success {
            border: 1px solid rgba(48, 213, 200, .4);
        }

        .comment-flash-error,
        .comment-error {
            border: 1px solid rgba(255, 110, 110, .5);
        }

        .link-button {
            border: 0;
            background: none;
            color: var(--accent-2);
            cursor: pointer;
            font: inherit;
            padding: 0;
            text-decoration: underline;
        }

        .comment-oauth-row {
            margin: 1rem 0 .5rem;
        }

        .btn-google {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            color: #fff;
            background: #4285f4;
            border: 1px solid #3367d6;
            text-decoration: none;
            font-weight: 700;
            border-radius: .85rem;
            padding: .72rem 1.05rem;
        }

        .btn-google:hover {
            background: #3367d6;
        }

        .oauth-divider {
            margin-bottom: .5rem;
        }

        @media (max-width: 900px) {
            .col-6, .col-4 { grid-column: span 12; }
            .topbar { flex-wrap: wrap; padding: .6rem 0; }
            .topbar-actions { width: 100%; justify-content: space-between; }
            .comment-auth-grid { grid-template-columns: 1fr; }
            .comment-meta { display: block; }
        }
    </style>
</head>
<body>
<?php
}

function renderFooter(): void
{
    ?>
<footer>
    <div class="container">ViewNPoint - Blog and software updates. More tools and posts are coming soon.</div>
</footer>
<script>
(function () {
    var key = "viewnpoint-theme";
    var root = document.documentElement;
    var toggle = document.getElementById("theme-toggle");
    if (!toggle) return;

    function apply(theme) {
        root.setAttribute("data-theme", theme);
        var isDark = theme === "dark";
        toggle.checked = isDark;
        toggle.setAttribute("aria-checked", isDark ? "true" : "false");
        localStorage.setItem(key, theme);
    }

    toggle.addEventListener("change", function () {
        apply(toggle.checked ? "dark" : "light");
    });

    apply(root.getAttribute("data-theme") || "dark");
})();
</script>
</body>
</html>
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
        <a class="brand" href="<?= e(url("/", $basePath)) ?>"><?= e($siteName) ?></a>
        <div class="topbar-actions">
            <label class="theme-switch" for="theme-toggle" title="Toggle light and dark mode">
                <span class="theme-icon" aria-hidden="true">☀</span>
                <input type="checkbox" id="theme-toggle" role="switch" aria-checked="true" aria-label="Dark mode">
                <span class="theme-slider"></span>
                <span class="theme-icon" aria-hidden="true">☾</span>
            </label>
            <nav>
                <a class="<?= isActive($requestPath, "/") && !isActive($requestPath, "/blog") && !isActive($requestPath, "/tech") && !isActive($requestPath, "/software") ? "active" : "" ?>" href="<?= e(url("/", $basePath)) ?>">Home</a>
                <a class="<?= isActive($requestPath, "/blog") ? "active" : "" ?>" href="<?= e(url("/blog", $basePath)) ?>">Blog</a>
                <a class="<?= isActive($requestPath, "/tech") || isActive($requestPath, "/software") ? "active" : "" ?>" href="<?= e(url("/tech", $basePath)) ?>">Technology</a>
            </nav>
        </div>
    </div>
</header>
<?php
}

function renderHome(string $basePath, string $siteName, string $siteTagline, array $software, array $blogPosts, string $requestPath): void
{
    renderHeader($siteName . " | Home", $siteTagline);
    renderNav($basePath, $requestPath, $siteName);
    ?>
<main class="container">
    <section class="hero">
        <span class="badge">ViewNPoint</span>
        <h1>Your destination for practical software and thoughtful blog content.</h1>
        <p>
            Discover useful tools, fast insights, and clear writing. Start with our featured software:
            TTS and Website Rank Checker. New software products and fresh blog posts will be added soon.
        </p>
        <div class="cta-row">
            <a class="btn btn-primary" href="<?= e(url("/tech", $basePath)) ?>">Explore Technology</a>
            <a class="btn btn-muted" href="<?= e(url("/blog", $basePath)) ?>">Read Blog</a>
        </div>
    </section>

    <section class="grid">
        <?php foreach ($software as $item): ?>
            <article class="card col-6">
                <a href="<?= e($item["path"]) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($item["title"]) ?>">
                    <div class="img-wrap">
                        <img src="<?= e(strpos($item["image"], "http") === 0 ? $item["image"] : url($item["image"], $basePath)) ?>" alt="<?= e($item["title"]) ?>">
                    </div>
                </a>
                <div class="card-content">
                    <h3><a href="<?= e($item["path"]) ?>" target="_blank" rel="noopener noreferrer" style="color:inherit; text-decoration:none;"><?= e($item["title"]) ?></a></h3>
                    <p><?= e($item["description"]) ?></p>
                    <span class="meta">Featured software</span>
                </div>
            </article>
        <?php endforeach; ?>

        <article class="card col-12">
            <div class="card-content">
                <h3>Featured Editorials</h3>
                <p>
                    Read <strong><?= e($blogPosts[0]["title"]) ?></strong> and
                    <strong><?= e($blogPosts[1]["title"] ?? $blogPosts[0]["title"]) ?></strong>—essays on urban burnout,
                    hardware startups, and engineering education in India.
                </p>
                <div class="cta-row">
                    <a class="btn btn-primary" href="<?= e(url($blogPosts[0]["path"], $basePath)) ?>">Latest Article</a>
                    <a class="btn btn-muted" href="<?= e(url("/blog", $basePath)) ?>">All Blog Posts</a>
                </div>
            </div>
        </article>
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
            <article class="card col-6">
                <a href="<?= e($item["path"]) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($item["title"]) ?>">
                    <div class="img-wrap">
                        <img src="<?= e(strpos($item["image"], "http") === 0 ? $item["image"] : url($item["image"], $basePath)) ?>" alt="<?= e($item["title"]) ?>">
                    </div>
                </a>
                <div class="card-content">
                    <h3><a href="<?= e($item["path"]) ?>" target="_blank" rel="noopener noreferrer" style="color:inherit; text-decoration:none;"><?= e($item["title"]) ?></a></h3>
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
        <p>Explore concise snapshots of our latest posts and open the full article for complete reading.</p>
    </section>
    <section class="grid">
        <?php foreach ($blogPosts as $post): ?>
            <article class="card col-12">
                <a href="<?= e(url($post["path"], $basePath)) ?>" aria-label="<?= e($post["title"]) ?>">
                    <div class="img-wrap trimmed">
                        <img src="<?= e(blogPostImage($post, $basePath)) ?>" alt="Snapshot image for <?= e($post["title"]) ?>">
                    </div>
                </a>
                <div class="card-content">
                    <h3>
                        <a href="<?= e(url($post["path"], $basePath)) ?>" style="color:inherit; text-decoration:none;">
                            <?= e($post["title"]) ?>
                        </a>
                    </h3>
                    <p><?= e($post["excerpt"]) ?></p>
                    <?php if (!empty($post["snapshot"])): ?>
                        <p><strong>Snapshot:</strong> <?= e($post["snapshot"]) ?></p>
                    <?php endif; ?>
                    <span class="meta"><?= e($post["readTime"]) ?></span>
                    <div class="cta-row">
                        <a class="btn btn-primary" href="<?= e(url($post["path"], $basePath)) ?>">Read Post</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</main>
<?php
    renderFooter();
}

function renderParadoxPost(string $basePath, string $siteName, string $requestPath, array $blogPosts): void
{
    $post = findBlogPost($blogPosts, "/the-paradox-of-progress-rethinking-indias-engineering-education") ?? $blogPosts[0];
    renderHeader(
        $post["title"] . " | " . $siteName,
        $post["seoDescription"] ?? $post["excerpt"],
        articleSeo($post, $basePath, $siteName)
    );
    renderNav($basePath, $requestPath, $siteName);
    ?>
<main class="container">
    <section class="hero">
        <span class="badge">Blog Post</span>
        <h1><?= e($post["title"]) ?></h1>
        <p><?= e($post["excerpt"]) ?></p>
    </section>

    <article class="article" itemscope itemtype="https://schema.org/Article">
        <div class="img-wrap" style="margin-bottom: 1rem; border: 1px solid var(--border); border-radius: .85rem;">
            <img src="<?= e(blogPostImage($post, $basePath)) ?>" alt="<?= e($post["title"]) ?>" itemprop="image">
        </div>

        <h1 itemprop="headline"><?= e($post["title"]) ?></h1>
        <p class="byline"><strong>Written by <?= e($post["author"] ?? "JB") ?></strong> · <em>Published: <time itemprop="datePublished" datetime="<?= e($post["published"] ?? "") ?>"><?= e(date("j F Y", strtotime($post["published"] ?? "now"))) ?></time></em></p>

        <p>
            The Indian education system, particularly in the realm of engineering, stands at a curious crossroads.
            On one hand, it produces millions of graduates annually, feeding a global appetite for technical talent.
            On the other, it is silently haemorrhaging its own intellectual diversity.
        </p>
        <p>
            The contemporary narrative of success has become dangerously narrow: an engineering degree is now largely
            synonymous with a ticket to the IT services industry. Disciplines like <strong>Mechanical, Civil, Electrical,
            and Electronics</strong>—the very pillars upon which industrialised nations are built—are being systematically
            marginalised, if not phased out altogether from a growing number of colleges.
        </p>
        <hr class="section-break">

        <h2>The Silent Erosion of Core Engineering</h2>
        <p>
            Walk into any private engineering institution today, and you will observe a stark reality.
            Enrolment in Computer Science, Data Science, and Information Technology has swollen to bursting,
            while laboratories for thermodynamics, power systems, and structural analysis gather dust—not for lack
            of equipment or competent faculty, but for lack of students.
        </p>
        <p>
            Metropolitan hubs such as Delhi-NCR, Bengaluru, Pune, and Hyderabad now function as extended service
            centres for foreign clients. Their demand is not for machine designers or circuit architects, but for business
            analysts, quality assurance testers, and cloud support associates—roles that rarely require deep knowledge
            of fluid mechanics or electromagnetic theory.
        </p>
        <p>
            Consequently, a student entering Mechanical Engineering knows, with grim certainty, that their starting
            salary will be a fraction of that offered to an average computer science graduate. The market message is
            unequivocal: <strong>core knowledge has little monetary value here.</strong>
        </p>
        <hr class="section-break">

        <h2>The Great Intra-National Brain Drain</h2>
        <p>
            In the 1990s, India's brightest minds migrated abroad in search of cutting-edge research and design.
            Today, that drain has turned inward. Instead of leaving the country, talented mechanical, electrical,
            and civil engineers simply abandon their fields.
        </p>
        <p>
            They retrain through online certification courses in Python or web development, not out of passion,
            but out of compulsion. A brilliant young engineer who can conceive an efficient heat exchanger or a
            low-cost seismic dampener ends up writing routine SQL queries for a foreign bank's back office.
        </p>
        <p>
            The talent remains within India's geographical borders, but it is channelled into what can only be described
            as <em>clerical work with a technical veneer</em>.
        </p>
        <hr class="section-break">

        <h2>The Innovation Conundrum</h2>
        <p>
            Why is India not a global hub for product design and manufacturing? The answer lies not in a lack
            of capability, but in a lack of nerve. Innovation is inherently risky and requires patient capital,
            cross-disciplinary collaboration, and, most crucially, a culture that rewards building over billing.
        </p>
        <p>
            If this trajectory continues, we face a gradual but irreversible decline in deep technological innovation.
            Today's spreadsheet-driven service economy cannot spontaneously generate tomorrow's electric vehicle motor,
            medical equipment, or an indigenously designed aircraft engine. Innovation is a habit, not an accident.
        </p>
        <hr class="section-break">

        <h2>The AI Paradox: Machine Intelligence Demands Human Ingenuity</h2>
        <p>
            Artificial Intelligence—widely feared as a job displacer—may in fact be the catalyst that rescues core engineering.
            AI does not diminish the need for human intellect; it magnifies it. If your work can be done by a machine,
            then human value must come from <strong>creativity, synthesis, and first-principles thinking</strong>.
        </p>
        <p>
            Far from rendering core knowledge obsolete, AI elevates its value. The engineer who understands thermodynamics
            and can direct AI to optimise a heat exchanger becomes irreplaceable.
            <strong>India must not confuse coding ability with engineering depth.</strong>
        </p>
        <hr class="section-break">

        <h2>Data Snapshot: Structural Shift in Engineering Education</h2>
        <p>The following data, drawn from AICTE approvals and JEE Advanced seat matrices, illustrates this shift.</p>

        <div class="table-card">
            <h3 class="table-title">Table 1: Decline in Core Engineering Seats at Select IITs (2015–2025)</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Institute</th>
                            <th>Branch</th>
                            <th>% Cut in Open Seats</th>
                            <th>2015 Intake (Open)</th>
                            <th>2025 Intake (Open)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>IIT Roorkee</td><td>Metallurgical &amp; Materials Engg.</td><td>54.55%</td><td>55</td><td>25</td></tr>
                        <tr><td>IIT Delhi</td><td>Textile Engineering</td><td>48.07%</td><td>52</td><td>27</td></tr>
                        <tr><td>IIT Kanpur</td><td>Materials Science &amp; Engg.</td><td>40.00%</td><td>45</td><td>27</td></tr>
                        <tr><td>IIT ISM Dhanbad</td><td>Mineral Engineering</td><td>39.13%</td><td>23</td><td>14</td></tr>
                        <tr><td>IIT Bombay</td><td>Chemical Engineering</td><td>34.33%</td><td>61</td><td>40</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-card">
            <h3 class="table-title">Table 2: Rising Intake in Computer Science &amp; Allied Branches</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Institution</th>
                            <th>Branch</th>
                            <th>Previous Intake</th>
                            <th>Current Intake</th>
                            <th>Change</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>COEP Technological University, Pune</td><td>Computer Science &amp; Engg.</td><td>150 (2024)</td><td>300 (2025)</td><td>+100%</td></tr>
                        <tr><td>Dr. G.U. Pope College of Engg., Tamil Nadu</td><td>Computer Science &amp; Engg.</td><td>90 (2024)</td><td>120 (2025)</td><td>+33%</td></tr>
                        <tr><td>IITs (8 Oldest IITs Combined)</td><td>CSE &amp; AI-related</td><td>~2,800 (2015)</td><td>~5,400 (2025)</td><td>+93% (approx.)</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-card">
            <h3 class="table-title">Table 3: Rising Tuition Fee Trends (2024–2025)</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Institution Type / Location</th>
                            <th>Fee Component</th>
                            <th>2024–25 (INR)</th>
                            <th>2025–26 (INR)</th>
                            <th>Increase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Private Unaided (Type 1), Karnataka</td><td>CET Govt. Quota</td><td>1,06,231</td><td>1,14,199</td><td>+7.5%</td></tr>
                        <tr><td>Private Unaided (Type 2), Karnataka</td><td>COMED-K Quota</td><td>2,61,477</td><td>2,81,088</td><td>+7.5%</td></tr>
                        <tr><td>Govt. Engineering Colleges, Karnataka</td><td>All Seats</td><td>42,116</td><td>44,200</td><td>+5%</td></tr>
                        <tr><td>Private Colleges, Telangana</td><td>CSE / AI / ML Branches</td><td>1.5 lakh - 2.3 lakh</td><td>1.65 lakh - 3 lakh</td><td>+20–30% annually</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <p class="table-note">
            <strong>Note on Table 3:</strong> Karnataka introduced a 50% fee waiver for Mechanical, Civil, Automobile,
            Textile Technology, and Silk Technology courses in government colleges for AY 2025–26.
            This acknowledges market failure and attempts to support demand for core branches.
        </p>
        <hr class="section-break">

        <h2>A Roadmap for Reinvention</h2>
        <p>What must change? The answer is not to abolish IT education, but to restore balance and elevate ambition.</p>
        <ul>
            <li><strong>Founders from core disciplines:</strong> Experienced engineers should become builders, not only employees.</li>
            <li><strong>Products over services:</strong> Ask what can be designed, manufactured, and sold globally.</li>
            <li><strong>Design-and-build houses:</strong> Create firms focused on robotics, powertrains, and indigenous systems.</li>
            <li><strong>Project-based curricula:</strong> Require final-year prototypes in core branches.</li>
            <li><strong>Core Engineering Risk Fund:</strong> Provide low-interest capital for physical product ventures.</li>
        </ul>
        <hr class="section-break">

        <h2>The Entrepreneur's Mandate: From Services to Products</h2>
        <p>The opportunity landscape is vast and urgently underserved:</p>
        <ul>
            <li><strong>Robotics &amp; Automation:</strong> Industrial manipulators, farming robots, disaster-response drones.</li>
            <li><strong>Medical Devices:</strong> AI diagnostics, ventilators, surgical robots, dialysis machines.</li>
            <li><strong>Sensors &amp; Instrumentation:</strong> IoT sensor ecosystems powered by MEMS and materials science.</li>
            <li><strong>Defence &amp; Aerospace:</strong> Composite materials, cooling systems, precision subsystems.</li>
            <li><strong>Advanced Materials:</strong> EV alloys, self-healing concrete, biodegradable polymers, high-efficiency cells.</li>
            <li><strong>Green Infrastructure:</strong> Passive cooling, embedded water recycling, resilient urban systems.</li>
            <li><strong>Transport &amp; Energy:</strong> Regenerative braking, electric bus transfer systems, traffic optimisation.</li>
            <li><strong>Food &amp; Biotechnology:</strong> Post-harvest automation and climate-specific bioengineering solutions.</li>
        </ul>
        <p>
            Each domain demands the marriage of <strong>core engineering depth</strong> with modern software and AI fluency.
            Teams that combine these strengths can create defensible products, export value globally, and generate high-value jobs.
        </p>
        <hr class="section-break">

        <h2>Conclusion</h2>
        <p>
            India does not lack talent. It lacks the courage to deploy that talent where it is most needed.
            The choice is simple: continue maintaining another country's legacy systems, or begin building our own future.
        </p>
        <?php vp_render_comments($post, $basePath); ?>
    </article>
</main>
<?php
    renderFooter();
}

function renderVerticalTrapPost(string $basePath, string $siteName, string $requestPath, array $blogPosts): void
{
    $post = findBlogPost($blogPosts, "/the-vertical-trap-why-indias-development-is-heating-up-and-burning-out") ?? $blogPosts[0];
    renderHeader(
        $post["title"] . " | " . $siteName,
        $post["seoDescription"] ?? $post["excerpt"],
        articleSeo($post, $basePath, $siteName)
    );
    renderNav($basePath, $requestPath, $siteName);
    ?>
<main class="container">
    <section class="hero">
        <span class="badge">Blog Post</span>
        <h1><?= e($post["title"]) ?></h1>
        <p><?= e($post["excerpt"]) ?></p>
    </section>

    <article class="article" itemscope itemtype="https://schema.org/Article">
        <div class="img-wrap" style="margin-bottom: 1rem; border: 1px solid var(--border); border-radius: .85rem;">
            <img src="<?= e(blogPostImage($post, $basePath)) ?>" alt="<?= e($post["title"]) ?>" itemprop="image">
        </div>

        <h1 itemprop="headline"><?= e($post["title"]) ?></h1>
        <p class="byline"><strong>Written by <?= e($post["author"] ?? "JB") ?></strong> · <em>Published: <time itemprop="datePublished" datetime="<?= e($post["published"] ?? "") ?>"><?= e(date("j F Y", strtotime($post["published"] ?? "now"))) ?></time></em></p>

        <p>We are losing something precious. Not just trees. But the very idea of living.</p>
        <p>
            On the name of development, we cut down every green thing. Then we pour concrete. Then we add glass.
            Lots of glass.
        </p>
        <hr class="section-break">

        <h2>What Replaced Our Old Buildings?</h2>
        <div class="table-card">
            <h3 class="table-title">Traditional Buildings vs Modern Glass Towers</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th>Traditional Building</th>
                            <th>Modern Glass Tower</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Walls</td><td>Thick brick (insulated)</td><td>Thin glass (transparent)</td></tr>
                        <tr><td>Height</td><td>1–2 storeys</td><td>20–50 storeys</td></tr>
                        <tr><td>Temperature</td><td>Naturally cool</td><td>Oven-like</td></tr>
                        <tr><td>Air cooling</td><td>Luxury</td><td>Necessary for survival</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <p>Glass looks rich. But it traps heat like a car on a summer day.</p>
        <hr class="section-break">

        <h2>The Thermal Cost of Progress</h2>
        <p>Here is what happens inside a glass building:</p>
        <ul>
            <li>Sunlight enters. Heat stays trapped.</li>
            <li>AC runs from March to November.</li>
            <li>Electricity bills skyrocket.</li>
            <li>Rent for one floor becomes unaffordable.</li>
        </ul>
        <p>We moved air cooling from a luxury to a necessity.</p>
        <hr class="section-break">

        <h2>The Vertical Lie</h2>
        <p>Builders say vertical growth is efficient. They are wrong.</p>
        <p>They pile up concrete without planning. Roads remain broken. Construction work never ends.</p>
        <div class="table-card">
            <h3 class="table-title">Urban Problems and Daily Employee Impact</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Problem</th>
                            <th>Effect on Employee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Untarred roads</td><td>2 hours extra commute</td></tr>
                        <tr><td>Road digging every month</td><td>Late arrival every day</td></tr>
                        <tr><td>No footpaths</td><td>Risk of injury</td></tr>
                        <tr><td>Poor public transport</td><td>Forced to drive</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <p>You leave home at 8 AM. You reach office at 10:30 AM. You are already tired.</p>
        <hr class="section-break">

        <h2>The Late Night Trap</h2>
        <p>You work late to make up time. Then a meeting appears.</p>
        <p>Western clients want calls at 8 PM IST.</p>
        <p>One meeting becomes three. You eat dinner at 11 PM. You sleep at 1 AM.</p>
        <div class="table-card">
            <h3 class="table-title">Health Consequences of the Late-Night Work Cycle</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Health Consequence</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Irregular sleep</td><td>Chronic fatigue</td></tr>
                        <tr><td>No family time</td><td>Broken relationships</td></tr>
                        <tr><td>Stress eating</td><td>Obesity</td></tr>
                        <tr><td>No exercise</td><td>Heart disease</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <p>Where is the essence of living? It has disappeared.</p>
        <hr class="section-break">

        <h2>The Startup Squeeze</h2>
        <p>Commercial building prices are insane. Let me show you why.</p>
        <div class="table-card">
            <h3 class="table-title">Average Office Rent vs Startup Budget (500 sq ft)</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Average Rent (500 sq ft office)</th>
                            <th>% of Startup Budget</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Mumbai</td><td>₹1,50,000/month</td><td>60–70%</td></tr>
                        <tr><td>Bengaluru</td><td>₹1,20,000/month</td><td>50–60%</td></tr>
                        <tr><td>Gurugram</td><td>₹1,00,000/month</td><td>45–55%</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <p class="table-note"><strong>Note:</strong> These numbers are from 2025–2026 real estate reports.</p>
        <p>What happens to a hardware startup with this math?</p>
        <div class="table-card">
            <h3 class="table-title">City Office vs Rural Shed: Hardware Startup Trade-offs</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>If You Stay in City</th>
                            <th>If You Move to Rural Area</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Rent a tiny 150 sq ft space</td><td>Rent a large 1000 sq ft shed</td></tr>
                        <tr><td>No room for tools or machines</td><td>Hours of travel through traffic</td></tr>
                        <tr><td>Landlord bans electronics testing</td><td>Talented employees refuse to relocate</td></tr>
                        <tr><td>You cannot build anything real</td><td>You cannot retain anyone good</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="section-break">

        <h2>The Cramped Reality</h2>
        <p>Picture this. A startup wants to build a medical device. They need:</p>
        <ul>
            <li>A 3D printer</li>
            <li>A small CNC machine</li>
            <li>A soldering station</li>
            <li>An oscilloscope</li>
            <li>Space for 5 engineers to stand and think</li>
        </ul>
        <p>In a city office, this is impossible.</p>
        <p>
            You cannot fit a CNC machine in a 200 sq ft room. You cannot test a sensor prototype.
            You cannot even hammer a bracket without a noise complaint.
        </p>
        <hr class="section-break">

        <h2>What Are Indian Startups Reduced To?</h2>
        <p>Only software. Only services. Only code.</p>
        <p>Look at the list:</p>
        <div class="table-card">
            <h3 class="table-title">What Startups Should Build vs What They Actually Build</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>What Startups Should Build</th>
                            <th>What Startups Actually Build</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Medical devices</td><td>Food delivery apps</td></tr>
                        <tr><td>Machine parts</td><td>EdTech videos</td></tr>
                        <tr><td>Industrial sensors</td><td>Fintech dashboards</td></tr>
                        <tr><td>Defence equipment</td><td>E-commerce websites</td></tr>
                        <tr><td>Robotics components</td><td>CRM software</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <p>Where is the invention? Where is the physical product?</p>
        <hr class="section-break">

        <h2>The Silent Death of Hardware Innovation</h2>
        <p>We have the brains. We have the hands. But we have no space to use them.</p>
        <p>
            A student with an idea for a new heart valve cannot test it. An engineer with a better motor design cannot prototype it.
            A team with a drone innovation cannot assemble it.
        </p>
        <p>Their only option? Move to Shenzhen or Austin.</p>
        <p>That is brain drain. But this time, we are doing it to ourselves.</p>
        <hr class="section-break">

        <h2>What Needs to Change</h2>
        <div class="table-card">
            <h3 class="table-title">Problems and Practical Solutions</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Problem</th>
                            <th>Solution</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Glass buildings heating the city</td><td>Mandate reflective coatings and insulation</td></tr>
                        <tr><td>Broken roads around new towers</td><td>No occupancy certificate without road completion</td></tr>
                        <tr><td>High rents killing hardware startups</td><td>Government-run maker sheds at subsidized rates</td></tr>
                        <tr><td>Only software being funded</td><td>Venture capital for deep tech and manufacturing</td></tr>
                        <tr><td>No work-life balance</td><td>Right-to-disconnect law for after-hours calls</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="section-break">

        <h2>The Bigger Vision</h2>
        <p>India needs startups that design:</p>
        <ul>
            <li><strong>Medical devices</strong> (ventilators, dialysis machines, surgical robots)</li>
            <li><strong>Machine tools</strong> (CNC spindles, precision bearings)</li>
            <li><strong>Sensors</strong> (air quality, vibration, temperature for factories)</li>
            <li><strong>Defence equipment</strong> (drone jammers, night vision housings)</li>
            <li><strong>Robotics components</strong> (actuators, controllers, grippers)</li>
            <li><strong>Energy storage</strong> (battery packs, inverters, cooling systems)</li>
            <li><strong>Agricultural machinery</strong> (small harvesters, sorters, sprayers)</li>
            <li><strong>Material handling</strong> (conveyor belts, palletizers, lifters)</li>
            <li><strong>Automotive parts</strong> (EV controllers, motor windings, chargers)</li>
            <li><strong>Consumer hardware</strong> (smart locks, water purifiers, air coolers)</li>
        </ul>
        <p>This list is not complete. It is a starting point.</p>
        <hr class="section-break">

        <h2>The One Question</h2>
        <p>Ask your local builder this:</p>
        <p>
            <em>"Where will the person who builds India's first indigenous medical robot work? In your glass tower with no loading dock? Or on the road outside?"</em>
        </p>
        <p>There is no answer. Because nobody thought about it.</p>
        <hr class="section-break">

        <h2>Development Claims vs Ground Reality</h2>
        <div class="table-card">
            <h3 class="table-title">India's Development Claim vs Ground Reality</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>India's Development Claim</th>
                            <th>Ground Reality</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>"We are building vertical"</td><td>We are stacking problems</td></tr>
                        <tr><td>"We are modernizing"</td><td>We are heating up</td></tr>
                        <tr><td>"We are creating offices"</td><td>We are destroying workshops</td></tr>
                        <tr><td>"We are attracting startups"</td><td>We are squeezing them out</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="section-break">

        <h2>Conclusion</h2>
        <p><strong>Development without planning is just expensive destruction.</strong></p>
        <?php vp_render_comments($post, $basePath); ?>
    </article>
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
    renderHome($basePath, $siteName, $siteTagline, $software, $blogPosts, $requestPath);
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

if (
    $requestPath === "/the-vertical-trap-why-indias-development-is-heating-up-and-burning-out" ||
    $requestPath === "/vertical-trap" ||
    $requestPath === "/blog/vertical-trap"
) {
    renderVerticalTrapPost($basePath, $siteName, $requestPath, $blogPosts);
    exit;
}

if (
    $requestPath === "/the-paradox-of-progress-rethinking-indias-engineering-education" ||
    $requestPath === "/paradox-of-success" ||
    $requestPath === "/blog/paradox-of-success"
) {
    renderParadoxPost($basePath, $siteName, $requestPath, $blogPosts);
    exit;
}

renderNotFound($basePath, $siteName, $requestPath);
