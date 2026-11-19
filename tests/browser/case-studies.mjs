import { chromium } from 'playwright';
import assert from 'node:assert/strict';
import { mkdir } from 'node:fs/promises';

const base = 'http://127.0.0.1:8001';
const paths = ['/realizacje', ...['cermax', 'olaya-strona', 'olaya-aplikacja', 'parkstop', 'walbud24'].map(slug => `/realizacje/${slug}`)];
const browser = await chromium.launch({ headless: true });
const errors = [];
await mkdir('browser-results', { recursive: true });
try {
    for (const width of [390, 768, 1440]) {
        const page = await browser.newPage({ viewport: { width, height: 900 }, deviceScaleFactor: 1 });
        page.on('pageerror', error => errors.push(error.message));
        for (const path of paths) {
            const response = await page.goto(base + path, { waitUntil: 'networkidle' });
            assert.equal(response.status(), 200, `${width}: ${path}`);
            assert.equal(await page.locator('h1').count(), 1);
            await page.evaluate(async () => {
                // Trigger native lazy loading across the page before checking images.
                for (let y = 0; y < document.body.scrollHeight; y += 600) {
                    window.scrollTo(0, y);
                    await new Promise(resolve => setTimeout(resolve, 30));
                }
                await document.fonts.ready;
                await Promise.all([...document.images].map(image => image.decode().catch(() => {})));
                window.scrollTo(0, 0);
            });
            const dimensions = await page.evaluate(() => ({
                viewport: innerWidth,
                page: document.documentElement.scrollWidth,
                brokenImages: [...document.images].filter(image => !image.complete || image.naturalWidth === 0).map(image => image.src),
            }));
            assert.ok(dimensions.page <= dimensions.viewport + 1, `${width}: overflow ${path}: ${JSON.stringify(dimensions)}`);
            assert.deepEqual(dimensions.brokenImages, [], `${width}: broken images ${path}`);
            if (path !== '/realizacje') {
                await page.getByRole('link', { name: /Porozmawiajmy o podobnym rozwiązaniu/ }).click();
                assert.equal(new URL(page.url()).pathname, '/kontakt');
                await page.goBack({ waitUntil: 'networkidle' });
            }
            if (path === '/realizacje/cermax' || path === '/realizacje/olaya-aplikacja') {
                await page.screenshot({ path: `browser-results/${path.split('/').pop()}-${width}.png`, fullPage: true });
            }
            console.log(`PASS ${width}px ${path}: HTTP 200, H1, images, no horizontal overflow, CTA`);
        }
        if (width === 390) {
            await page.getByRole('button', { name: /menu/i }).click();
            assert.equal(await page.getByRole('button', { name: /menu/i }).getAttribute('aria-expanded'), 'true');
            await page.keyboard.press('Escape');
            assert.equal(await page.getByRole('button', { name: /menu/i }).getAttribute('aria-expanded'), 'false');
        }
        await page.close();
    }
    assert.deepEqual(errors, [], 'Unexpected browser JavaScript errors');
} finally {
    await browser.close();
}
