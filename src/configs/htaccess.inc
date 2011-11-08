Options +FollowSymLinks
RewriteEngine on
RewriteBase @AIKI_REWRITE_BASE@/
RewriteRule ^image/(.*)px/(.*)/(.*) assets/apps/image_viewer.php?id=$3&size=$1&mode=$2
RewriteRule ^image/(.*)px/(.*) assets/apps/image_viewer.php?id=$2&size=$1
RewriteRule ^image/(.*) assets/apps/image_viewer.php?id=$1
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteCond %{SCRIPT_FILENAME} !-f
RewriteRule ^(.*)$ index.php?pretty=$1 [L,QSA]
