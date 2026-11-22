import { chromium } from 'playwright';
import assert from 'node:assert/strict';
import { mkdir } from 'node:fs/promises';

const base = 'http://127.0.0.1:8001';
const browser = await chromium.launch({ headless: true });
await mkdir('browser-results', { recursive: true });
const errors = [];
async function open(page, path) {
    const response = await page.goto(base + path, { waitUntil: 'networkidle' });
    assert.equal(response.status(), 200, path);
    return response;
}
async function addClient(page, name) {
    await open(page, '/demo/installflow/klienci/nowy');
    await page.locator('#name').fill(name);
    await page.locator('#city').fill('Miasto demonstracyjne');
    await page.locator('#email').fill('browser@installflow.test');
    await page.getByRole('button', { name: 'Zapisz klienta', exact: true }).click();
    await page.waitForURL('**/klienci/31');
    await page.getByRole('heading', { name, exact: true }).waitFor();
}
try {
    const a = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
    const b = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
    const page = await a.newPage(); const other = await b.newPage();
    for (const p of [page, other]) p.on('pageerror', error => errors.push(error.message));
    await open(page, '/demo');
    await page.getByRole('link', { name: 'Uruchom demo', exact: true }).click();
    await page.getByRole('heading', { name: 'Dobry dzień na dobrą organizację.' }).waitFor();
    await addClient(page, 'Wyłącznie sesja A');
    await addClient(other, 'Wyłącznie sesja B');
    await open(other, '/demo/installflow/klienci/31');
    assert.ok(!(await other.locator('main').innerText()).includes('Wyłącznie sesja A'));
    await open(page, '/demo/installflow/klienci/31');
    assert.ok(!(await page.locator('main').innerText()).includes('Wyłącznie sesja B'));
    const csrf = await page.request.post(base + '/demo/installflow/klienci', { form: { name: 'Bez CSRF', type: 'person', city: 'Test' } });
    assert.equal(csrf.status(), 419);
    await open(page, '/demo/installflow/oferty/nowy');
    await page.locator('#title').fill('Klimatyzacja — test całego procesu');
    await page.locator('#client_id').selectOption('31');
    await page.locator('#status').selectOption('accepted');
    const first = page.locator('[data-offer-row]').first();
    await first.locator('input').nth(0).fill('Urządzenie');
    await first.locator('[data-quantity]').fill('2');
    await first.locator('[data-price]').fill('100.01');
    await page.getByRole('button', { name: 'Dodaj pozycję', exact: true }).click();
    const second = page.locator('[data-offer-row]').nth(1);
    await second.locator('input').nth(0).fill('Montaż');
    await second.locator('[data-quantity]').fill('1.5');
    await second.locator('[data-price]').fill('10');
    assert.match(await page.locator('[data-total-gross]').innerText(), /264,47/);
    await page.getByRole('button', { name: 'Zapisz ofertę', exact: true }).click();
    await page.waitForURL('**/oferty/19');
    await page.getByRole('button', { name: 'Utwórz zlecenie', exact: true }).click();
    await page.waitForURL('**/zlecenia/25');
    await open(page, '/demo/installflow/kalendarz');
    const date = new Date(); date.setDate(date.getDate() + 2);
    const iso = date.toISOString().slice(0, 10) + 'T11:45';
    const schedule = page.locator('[data-schedule-form]');
    await schedule.locator('[name="order_id"]').selectOption('25');
    await schedule.locator('[name="scheduled_at"]').fill(iso);
    await schedule.locator('[name="employee_id"]').selectOption('1');
    await schedule.getByRole('button', { name: 'Zapisz nowy termin', exact: true }).click();
    await page.waitForURL('**/kalendarz?month=*');
    await page.getByRole('status').waitFor();
    await open(page, '/demo/installflow/zlecenia/25');
    assert.match(await page.locator('main').innerText(), /11:45/);
    await page.getByRole('link', { name: 'Edytuj zlecenie / status' }).click();
    await page.locator('#status').selectOption('completed');
    await page.getByRole('button', { name: 'Zapisz zlecenie', exact: true }).click();
    await page.waitForURL('**/zlecenia/25');
    assert.match(await page.locator('.if-timeline').innerText(), /Zakończone/);
    await open(page, '/demo/installflow/oferty/19/druk');
    const pdf = await page.pdf({ path: 'browser-results/installflow-offer.pdf', format: 'A4' });
    assert.equal(pdf.subarray(0, 4).toString(), '%PDF');
    await page.screenshot({ path: 'browser-results/installflow-offer-print.png', fullPage: true });
    await open(page, '/demo/installflow');
    page.once('dialog', dialog => dialog.accept());
    await page.getByRole('button', { name: 'Przywróć dane', exact: true }).click();
    await page.waitForURL('**/demo/installflow');
    const reset = await page.request.get(base + '/demo/installflow/klienci/31');
    assert.equal(reset.status(), 404);
    await open(other, '/demo/installflow/klienci/31');
    assert.match(await other.locator('main').innerText(), /Wyłącznie sesja B/);
    console.log('PASS browser workflow: clients, isolated sessions, CSRF, offer items/totals, conversion, calendar, status/history, PDF and isolated reset');
    for (const width of [390, 768, 1440]) {
        await page.setViewportSize({ width, height: 1000 });
        for (const path of ['/demo', '/demo/installflow/opis', '/demo/installflow', '/demo/installflow/klienci', '/demo/installflow/oferty', '/demo/installflow/zlecenia', '/demo/installflow/kalendarz', '/demo/installflow/pracownicy', '/demo/installflow/raporty', '/demo/installflow/oferty/nowy']) {
            const response = await open(page, path);
            await page.evaluate(() => document.fonts.ready);
            assert.equal(await page.locator('h1').count(), 1, path);
            const overflow = await page.evaluate(() => document.documentElement.scrollWidth > innerWidth + 1);
            if (overflow) {
                console.log('OVERFLOW', width, path, await page.evaluate(() => [...document.querySelectorAll('body *')].filter(el => {
                    const rect = el.getBoundingClientRect(); return rect.width && rect.right > innerWidth + 1 && !el.closest('.if-table-wrap');
                }).slice(0, 15).map(el => ({ tag: el.tagName, class: el.className, width: el.getBoundingClientRect().width, right: el.getBoundingClientRect().right, text: el.textContent.slice(0, 70) }))));
                await page.screenshot({ path: 'browser-results/installflow-overflow.png', fullPage: true });
            }
            assert.ok(!overflow, `${width}px horizontal overflow: ${path}`);
            if (path === '/demo/installflow') {
                assert.match(response.headers()['cache-control'], /no-store/);
                assert.equal(response.headers()['x-robots-tag'], 'noindex, nofollow');
                await page.screenshot({ path: `browser-results/installflow-dashboard-${width}.png`, fullPage: true });
            }
            if (width === 1440 && path === '/demo/installflow/kalendarz') await page.screenshot({ path: 'browser-results/installflow-calendar.png', fullPage: true });
        }
        if (width === 390) {
            await page.getByRole('button', { name: 'Otwórz menu', exact: true }).click();
            assert.equal(await page.getByRole('button', { name: 'Zamknij menu', exact: true }).getAttribute('aria-expanded'), 'true');
            await page.keyboard.press('Escape');
            assert.equal(await page.getByRole('button', { name: 'Otwórz menu', exact: true }).getAttribute('aria-expanded'), 'false');
        }
        console.log(`PASS InstallFlow ${width}px: 10 pages, responsive, H1, private/noindex dashboard, sidebar`);
    }
    assert.deepEqual(errors, [], 'Browser JavaScript errors');
    await a.close(); await b.close();
} finally {
    await browser.close();
}
