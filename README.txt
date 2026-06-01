ViewNPoint — README
====================


WEBSITE ARCHITECTURE
--------------------

- Single router: index.php
- Clean URLs via .htaccess
- Blog listing: /blog
- Article pages: /article-slug (no physical file per article)
- blog/ folder holds images only
- comments/ holds login, comments, moderation, Google OAuth
- db/ holds SQLite database and backups
- Public sees only approved comments


MAIN URLS
---------

Local (XAMPP):
  http://localhost/viewnpoint.com/
  http://localhost/viewnpoint.com/blog
  http://localhost/viewnpoint.com/the-paradox-of-progress-rethinking-indias-engineering-education
  http://localhost/viewnpoint.com/comments/admin.php

Production:
  https://viewnpoint.com/
  https://viewnpoint.com/blog
  https://viewnpoint.com/the-paradox-of-progress-rethinking-indias-engineering-education
  https://viewnpoint.com/comments/admin.php


COMMENT FLOW
------------

- User opens a blog article
- User signs in (Google or email/password)
- User writes a comment
- Comment saved as pending
- Admin approves in admin panel
- Approved comment appears on the article page


MODERATE A COMMENT
------------------

1. Open admin page:
   Local:    http://localhost/viewnpoint.com/comments/admin.php
   Live:     https://viewnpoint.com/comments/admin.php

2. Log in as admin (see ADMIN LOGIN below)

3. In Comments table, find status pending

4. Click one action:
   Approve     → visible on blog
   Disapprove  → hidden from blog
   Delete      → marked deleted
   Block User  → user cannot comment again
   Unblock User → restore blocked user


ADMIN LOGIN
-----------

Username: admin
Email:    moderator@viewnpoint.com
Password: set in config file (admin_password)

Local config:       comments/config.php
Production config:  comments/config.production.php → upload as config.php

Do not commit config files with passwords.


CONFIG FILES (PC vs SERVER)
---------------------------

comments/config.php
  - For localhost / XAMPP only
  - Do not upload to server

comments/config.production.php
  - For live server
  - Upload via FileZilla
  - Rename on server to: config.php

comments/config.php.example
  - Template only, no secrets
  - Safe for Git


DATABASE (db/)
--------------

Main file:
  db/viewnpoint_comments.sqlite

Created automatically on first comment or login.
Server needs empty db/ folder with write permission (chmod 755 or 775).
Root .htaccess blocks direct web access to db/ files.

Tables:
  users           — accounts (email, name, login, Google link)
  comments        — messages and status (pending/approved/disapproved/deleted)
  moderation_log  — admin actions


BACKUP FILES (db/)
------------------

Auto-created after comments/users change:

  db/viewnpoint_comments_backup.sqlite   — copy of main database
  db/viewnpoint_users.csv                — user export
  db/viewnpoint_comments.csv             — comment export

Do not upload local db files to server.
Let the server create its own fresh database.


GOOGLE OAUTH
------------

Credentials live in config.php / config.production.php:
  google_client_id
  google_client_secret
  google_redirect_uri

Google Cloud Console:
  https://console.cloud.google.com/apis/credentials

Add both redirect URIs to your OAuth Web client:

  http://localhost/viewnpoint.com/comments/oauth_google_callback.php
  https://viewnpoint.com/comments/oauth_google_callback.php

Optional JavaScript origins:
  http://localhost
  https://viewnpoint.com

User flow:
  Sign in with Google → submit comment → admin approves


GIT — CHECK IN
--------------

  index.php
  .htaccess
  router.php
  .gitignore
  README.txt
  blog/*.jpg
  comments/action.php
  comments/admin.php
  comments/bootstrap.php
  comments/oauth_google.php
  comments/oauth_google_callback.php
  comments/config.php.example


GIT — DO NOT CHECK IN
---------------------

  comments/config.php
  comments/config.production.php
  db/viewnpoint_comments.sqlite
  db/viewnpoint_comments_backup.sqlite
  db/viewnpoint_users.csv
  db/viewnpoint_comments.csv
  .git/
  Thumbs.db, .DS_Store, *.tmp, *.log


FTP — UPLOAD TO SERVER (FileZilla)
----------------------------------

Upload FROM:
  e:\web\htdocs\viewnpoint.com\

Upload TO:
  Server document root (e.g. public_html or htdocs/viewnpoint.com)

Upload these:
  index.php
  .htaccess
  router.php
  sitemap.php
  robots.txt
  assets/                  (css, js)
  img/                     (logos, favicon)
  blog/                    (all images)
  comments/
    action.php
    admin.php
    bootstrap.php
    oauth_google.php
    oauth_google_callback.php
    config.production.php  → rename to config.php on server
    config.php.example
  db/                    (create empty folder on server, or let app create it)


FTP — DO NOT UPLOAD
-------------------

  .git/
  comments/config.php              (localhost only)
  README.txt                       (optional on server)
  db/viewnpoint_comments.sqlite
  db/viewnpoint_comments_backup.sqlite
  db/viewnpoint_users.csv
  db/viewnpoint_comments.csv
  Remark42/


FTP — AFTER UPLOAD
------------------

1. Rename comments/config.production.php → comments/config.php on server
2. Confirm Google redirect URI in Cloud Console (production URL)
3. Make db/ writable
4. Test https://viewnpoint.com/comments/admin.php
5. Test Google sign-in on an article
6. Approve a test comment


FILEZILLA TIPS
--------------

- Enable "Show hidden files" so .htaccess uploads
- Transfer type: Automatic
- Do not overwrite live db/*.sqlite unless you mean to


PROJECT LAYOUT
--------------

  index.php              Site + blog routing
  .htaccess              URL rewriting + db/ protection
  blog/                  Article images
  comments/              Auth, comments, admin, OAuth
  db/                    SQLite + backups (runtime data)
