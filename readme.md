# Symfony Wordpress Admin Bundle

## Gettings started

`composer.json`

```json
{
    "require": {
        "...": "...",
        "johnpbloch/wordpress": "^5.6",
        "advanced-custom-fields/advanced-custom-fields-pro": "1.0",
        "wpackagist-plugin/duplicate-post": "*",
        "wpackagist-plugin/post-types-order": "*",
        "wpackagist-plugin/regenerate-thumbnails": "*",
        "wpackagist-plugin/svg-support": "*",
        "wpackagist-plugin/taxonomy-terms-order": "*",
        "wpackagist-plugin/wordpress-seo": "*"
    },
    "scripts": {
        "auto-scripts": {
            "...": "...",
            "wp:clear-installation": "symfony-cmd"
        },
        "post-install-cmd": [
            "...",
            "@auto-scripts"
        ],
        "post-update-cmd": [
            "...",
            "@auto-scripts"
        ]
    },
    "extra": {
        "...": "...",
        "wordpress-install-dir": "public/wp",
        "installer-paths": {
            "public/content/plugins/{$name}/": [
                "vendor:wpackagist-plugin",
                "type:wordpress-plugin"
            ]
        }
    }
}
```
