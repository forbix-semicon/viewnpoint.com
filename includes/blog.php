<?php
declare(strict_types=1);

function vp_parsedown(): Parsedown
{
    static $parser = null;
    if ($parser === null) {
        require_once dirname(__DIR__) . "/lib/Parsedown.php";
        $parser = new Parsedown();
    }

    return $parser;
}

function blogContentPath(array $post): string
{
    return dirname(__DIR__) . "/content/" . ($post["slug"] ?? "") . ".md";
}

function blogPostBodyHtml(array $post): string
{
    $path = blogContentPath($post);
    if (!is_file($path)) {
        return "<p>Article content is not available.</p>";
    }

    $markdown = file_get_contents($path);
    if ($markdown === false || trim($markdown) === "") {
        return "<p>Article content is not available.</p>";
    }

    return vp_parsedown()->text($markdown);
}

function findBlogPostByPath(array $blogPosts, string $path): ?array
{
    foreach ($blogPosts as $post) {
        if (($post["path"] ?? "") === $path) {
            return $post;
        }
        foreach ($post["aliases"] ?? [] as $alias) {
            if ($alias === $path) {
                return $post;
            }
        }
    }

    return null;
}

function renderArticle(string $basePath, string $siteName, string $requestPath, array $post): void
{
    $heroBadge = $post["heroBadge"] ?? "Blog Post";
    $heroLead = $post["heroSubheading"] ?? ($post["excerpt"] ?? "");
    $heroLeadIsHtml = !empty($post["heroSubheading"]);
    $extraStyles = $post["styles"] ?? [];
    $extraScripts = $post["scripts"] ?? [];

    renderHeader(
        $post["title"] . " | " . $siteName,
        $post["seoDescription"] ?? ($post["excerpt"] ?? ""),
        articleSeo($post, $basePath, $siteName),
        "article",
        $extraStyles
    );
    renderNav($basePath, $requestPath, $siteName);
    ?>
<main class="container">
    <section class="hero">
        <span class="badge"><?= e($heroBadge) ?></span>
        <h1 itemprop="headline"><?= e($post["title"]) ?></h1>
        <?php if ($heroLead !== ""): ?>
        <p><?= $heroLeadIsHtml ? "<em>" . e($heroLead) . "</em>" : e($heroLead) ?></p>
        <?php endif; ?>
    </section>

    <article class="article" itemscope itemtype="https://schema.org/Article">
        <?php if (empty($post["hideHeroImage"])): ?>
        <div class="img-wrap article-hero-image">
            <img src="<?= e(blogPostImage($post, $basePath)) ?>" alt="<?= e($post["title"]) ?>" itemprop="image" width="1140" height="641" fetchpriority="high" decoding="async">
        </div>
        <?php endif; ?>
        <p class="byline"><strong>Written by <?= e($post["author"] ?? "ViewNPoint") ?></strong> · <em>Published: <time itemprop="datePublished" datetime="<?= e($post["published"] ?? "") ?>"><?= e(date("j F Y", strtotime($post["published"] ?? "now"))) ?></time></em></p>

        <?= blogPostBodyHtml($post) ?>
        <?php vp_render_comments($post, $basePath); ?>
    </article>
</main>
<?php
    renderFooter($extraScripts);
}
