<?php
declare(strict_types=1);

function renderAboutPage(string $basePath, string $siteName, string $requestPath): void
{
    renderHeader(
        "About Us | " . $siteName,
        "ViewNPoint is a blog from FORBIX SEMICON engineers — practical notes on design, tools, and ideas we care about.",
        [
            "canonical" => absoluteUrl("/about", $basePath),
            "og_title" => "About ViewNPoint",
            "og_description" => "Engineers from FORBIX SEMICON started this blog to share ideas, not brochures.",
            "keywords" => "about ViewNPoint, FORBIX SEMICON blog, engineering writing Bangalore",
        ],
        "article"
    );
    renderNav($basePath, $requestPath, $siteName);
    ?>
<main class="container">
    <section class="hero">
        <span class="badge">About</span>
        <h1>About ViewNPoint</h1>
        <p>A small corner for engineers who still like writing things down.</p>
    </section>

    <article class="article">
        <p>This site started with a simple itch. A few of us at <a href="https://www.forbixindia.com" rel="noopener noreferrer">FORBIX SEMICON</a> — a Bangalore electronics team that's spent years on wireless nurse-call systems, panic alarms, long-range RF, and industrial automation — kept saying the same thing after late debugging sessions: “someone should write this up.”</p>
        <p>So we did. ViewNPoint is that write-up space. Not a product catalogue. Not a press kit. Just notes on what we're curious about: design trade-offs, tools we hack together, and the odd essay that wanders into cities, education, or how tech lands in everyday life.</p>

        <h2>What you'll find here</h2>
        <p>Plenty of technical blogs lean either into sales copy or into lecture notes. We try to sit in the awkward middle — practical, a bit messy, written by people who still touch schematics and solder.</p>
        <ul>
            <li><strong>Engineering deep-dives</strong> — decisions we made, bugs we chased, ratios and radio quirks that bit us.</li>
            <li><strong>Small tools</strong> — interactive pages we built because we needed them (hearing-frequency lab, gear calculator, Fourier sketches).</li>
            <li><strong>Longer essays</strong> — India, cities, careers, and whatever else keeps the team talking past tea time.</li>
        </ul>

        <h2>Who it's for</h2>
        <p>Engineers, students, hobbyists, and anyone who likes knowing how something works. We're not here to push a cart on you. If a FORBIX product shows up in a post, it's because it sits right next to the idea — not the other way around.</p>

        <h2>The team behind the posts</h2>
        <p>Circuit design, RF, embedded firmware, enclosures — different desks, same office. ViewNPoint is where those desks share what they're learning, testing, or occasionally losing sleep over.</p>

        <p>Questions, corrections, or a better way to explain something? <a href="<?= e(url("/contact", $basePath)) ?>">Say hello</a>.</p>
    </article>
</main>
<?php
    renderFooter();
}

function renderPrivacyPage(string $basePath, string $siteName, string $requestPath): void
{
    renderHeader(
        "Privacy Policy | " . $siteName,
        "How ViewNPoint handles personal data for comments, contact forms, analytics, and advertising.",
        [
            "canonical" => absoluteUrl("/privacy", $basePath),
            "og_title" => "Privacy Policy — ViewNPoint",
            "og_description" => "What we collect on ViewNPoint, why we collect it, and how to reach us about your data.",
            "keywords" => "ViewNPoint privacy policy, data protection, cookies, AdSense",
        ],
        "article"
    );
    renderNav($basePath, $requestPath, $siteName);
    ?>
<main class="container">
    <section class="hero">
        <span class="badge">Legal</span>
        <h1>Privacy Policy</h1>
        <p>Last updated: 20 July 2026</p>
    </section>

    <article class="article">
        <p>ViewNPoint (“we”, “us”) operates this website and the related interactive tools. This Privacy Policy describes the personal information we collect when you use the site, how that information is used, and the choices available to you.</p>

        <h2>What we collect</h2>
        <ul>
            <li><strong>Comments</strong> — if you register or leave a comment, we store the details you give us (name or display name, email, login provider if you use Google, and the comment text). Comments are moderated before they appear publicly.</li>
            <li><strong>Contact form</strong> — name, email, optional subject, and your message. Stored for the ViewNPoint team to review and reply when needed.</li>
            <li><strong>Technical logs</strong> — normal web-server bits (IP address, browser type, pages requested) may be recorded by the host for security and uptime.</li>
            <li><strong>Cookies / local storage</strong> — we keep a theme preference (light/dark) in your browser. Comment login may use a session cookie.</li>
        </ul>

        <h2>What we do not collect</h2>
        <ul>
            <li>Payment card or banking details — we do not take payments on this site</li>
            <li>Government ID numbers, biometric data, or precise GPS location</li>
            <li>Contacts, photos, or files from your device</li>
            <li>Information from social media accounts beyond what you choose to share when signing in (for example, email and display name via Google login)</li>
            <li>Data from children under 13 (see Children below)</li>
        </ul>
        <p>We also do not run background tracking of your offline activity, and we do not buy personal data about you from data brokers to build marketing profiles.</p>

        <h2>Advertising</h2>
        <p>We use Google AdSense. Google and its partners may use cookies or similar tech to show ads based on your visits to this and other sites. You can learn more and manage ad personalization in <a href="https://adssettings.google.com/" rel="noopener noreferrer">Google Ads Settings</a> and read Google’s policies at <a href="https://policies.google.com/technologies/ads" rel="noopener noreferrer">policies.google.com/technologies/ads</a>.</p>

        <h2>How we use the information</h2>
        <ul>
            <li>To publish and moderate comments</li>
            <li>To answer messages you send through Contact</li>
            <li>To keep the site secure and working</li>
            <li>To understand roughly how pages are used (if analytics are enabled later)</li>
        </ul>
        <p>We don’t sell your email list. We don’t hand contact-form messages to random third parties for marketing.</p>

        <h2>Who sees it</h2>
        <p>Site moderators on the ViewNPoint team can see comments and contact messages. Hosting providers process data as needed to deliver the service. Google processes data for ads as described in their policies.</p>

        <h2>How long we keep it</h2>
        <p>Comments stay while the article and moderation records exist, unless you ask us to remove them and we can verify the request. Contact form messages are kept while we need them for follow-up, then removed under normal moderation. Server logs rotate on the host’s schedule.</p>

        <h2>Your choices</h2>
        <p>You can browse without creating an account. You can ask us to delete or correct a comment tied to your email. For ad cookies, use your browser settings and Google’s ad controls linked above.</p>

        <h2>Children</h2>
        <p>This site isn’t aimed at children under 13. We don’t knowingly collect their data.</p>

        <h2>Changes</h2>
        <p>If we change this policy in a meaningful way, we’ll update the date at the top of this page.</p>

        <h2>Contact</h2>
        <p>Privacy questions: use the <a href="<?= e(url("/contact", $basePath)) ?>">Contact</a> page.</p>
    </article>
</main>
<?php
    renderFooter();
}

function renderContactPage(string $basePath, string $siteName, string $requestPath): void
{
    $actionUrl = url("/contact_action.php", $basePath);
    renderHeader(
        "Contact Us | " . $siteName,
        "Write to the ViewNPoint team — questions, corrections, or ideas for the blog.",
        [
            "canonical" => absoluteUrl("/contact", $basePath),
            "og_title" => "Contact ViewNPoint",
            "og_description" => "Send a note to the ViewNPoint / FORBIX SEMICON blog team.",
            "keywords" => "contact ViewNPoint, FORBIX SEMICON contact blog",
        ],
        "article",
        ["contact.css"]
    );
    renderNav($basePath, $requestPath, $siteName);
    ?>
<main class="container">
    <section class="hero">
        <span class="badge">Contact</span>
        <h1>Contact us</h1>
        <p>Got a question, a correction, or a better way to explain something? Send it over.</p>
    </section>

    <article class="article">
        <p>Messages are saved for the ViewNPoint team to review. If a reply makes sense, we’ll write back to the email you leave below.</p>

        <form class="contact-form" id="contact-form" method="post" action="<?= e($actionUrl) ?>" novalidate>
            <div class="contact-field contact-honeypot" aria-hidden="true">
                <label for="contact-website">Website</label>
                <input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off">
            </div>
            <div class="contact-field">
                <label for="contact-name">Name</label>
                <input type="text" id="contact-name" name="name" maxlength="120" required autocomplete="name">
            </div>
            <div class="contact-field">
                <label for="contact-email">Email</label>
                <input type="email" id="contact-email" name="email" maxlength="180" required autocomplete="email">
            </div>
            <div class="contact-field">
                <label for="contact-subject">Subject <span class="contact-optional">(optional)</span></label>
                <input type="text" id="contact-subject" name="subject" maxlength="160" autocomplete="off">
            </div>
            <div class="contact-field">
                <label for="contact-message">Message</label>
                <textarea id="contact-message" name="message" rows="7" maxlength="5000" required></textarea>
            </div>
            <p class="contact-status" id="contact-status" role="status" aria-live="polite" hidden></p>
            <button class="btn btn-primary" type="submit" id="contact-submit">Send message</button>
        </form>
    </article>
</main>
<?php
    renderFooter(["contact-form.js"]);
}
