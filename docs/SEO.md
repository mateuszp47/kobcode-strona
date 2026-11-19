# SEO — KOBCODE

## Implemented
- Unique page titles/descriptions and consistent canonical, Open Graph and Twitter metadata.
- Absolute canonical and sitemap URLs based on APP_URL, independent of request host and tracking query strings.
- Organization, WebSite, WebPage and relevant Service JSON-LD, with safe JSON encoding. No invented reviews, ratings, prices or client results.
- XML sitemap containing public pages and only approved published CMS projects; project lastmod uses its real updated_at. No fake daily dates or priority values.
- Dynamic robots.txt controlled by SITE_INDEXABLE. Preview has noindex and disallows crawling; sitemap returns 404 while indexing is disabled.
- Service-to-project and footer links. Existing server-rendered content, responsive WebP screenshots, image dimensions, lazy loading and self-hosted font retained.

## At public launch
1. Set APP_URL to the final HTTPS domain (choose www or non-www). Do not leave localhost. Configure hosting redirects from the alternate host and HTTP to the selected HTTPS address.
2. Finalize contact details/form and privacy information. Then set SITE_INDEXABLE=true only on the public production instance; keep local/staging false.
3. Rebuild assets and run php artisan optimize:clear after environment changes; apply deployment-specific caches as appropriate.
4. Check /robots.txt, /sitemap.xml, page canonical URLs and shared image URLs on the real domain.
5. Verify ownership in Google Search Console, submit /sitemap.xml, inspect the homepage and service URLs. No Search Console submission or deployment was performed by this change.
6. Measure field Core Web Vitals on production. Local builds and automated tests do not establish Lighthouse scores, real-user performance or ranking gains.

The sitemap includes all seven public pages (including contact and privacy) and approved published CMS projects. Sitemap XML is serialized with DOMDocument rather than compiled Blade. Public canonical URLs have no trailing slash except the homepage. Production-only middleware normalizes the alternate host and public URL paths; HTTPS enforcement remains a hosting/proxy setting. Uploaded project images use their actual dimensions when a local file exists. No city landing pages, keyword stuffing, fabricated performance figures or guaranteed search positions.

Reference: https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap
