const menu = document.querySelector('.if-menu');
const sidebar = document.querySelector('.if-sidebar');
const setMenu = open => { sidebar?.classList.toggle('is-open', open); menu?.setAttribute('aria-expanded', String(open)); menu?.setAttribute('aria-label', open ? 'Zamknij menu' : 'Otwórz menu'); };
menu?.addEventListener('click', () => setMenu(menu.getAttribute('aria-expanded') !== 'true'));
document.addEventListener('keydown', event => { if (event.key === 'Escape') { setMenu(false); menu?.focus(); } });
document.addEventListener('click', event => { if (!sidebar?.contains(event.target) && !menu?.contains(event.target)) setMenu(false); });
document.querySelector('[data-print]')?.addEventListener('click', () => window.print());
document.querySelector('.if-errors')?.focus();
for (const form of document.forms) {
    form.addEventListener('submit', event => {
        if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) { event.preventDefault(); return; }
        if (form.method.toLowerCase() === 'post') {
            for (const button of form.querySelectorAll('button[type="submit"], button:not([type])')) { button.disabled = true; button.setAttribute('aria-busy', 'true'); }
        }
    });
}
window.addEventListener('pageshow', () => document.querySelectorAll('[aria-busy="true"]').forEach(button => { button.disabled = false; button.removeAttribute('aria-busy'); }));
const form = document.querySelector('[data-offer-form]');
if (form) {
    const rows = form.querySelector('[data-offer-items]');
    let nextIndex = rows.children.length;
    const currency = value => new Intl.NumberFormat('pl-PL', { style: 'currency', currency: 'PLN' }).format(value / 100);
    const hundredths = value => { const [whole = '0', fraction = ''] = String(value).replace(',', '.').split('.'); return Number(whole || 0) * 100 + Number(fraction.padEnd(2, '0').slice(0, 2)); };
    function update() {
        let total = 0;
        for (const row of rows.querySelectorAll('[data-offer-row]')) {
            const quantity = hundredths(row.querySelector('[data-quantity]').value);
            const price = hundredths(row.querySelector('[data-price]').value);
            const line = Math.round(quantity * price / 100);
            total += line;
            row.querySelector('[data-line-total]').textContent = currency(line);
            row.querySelector('[data-remove-item]').disabled = rows.children.length === 1;
        }
        const vat = Math.round(total * 23 / 100);
        form.querySelector('[data-total-net]').textContent = currency(total);
        form.querySelector('[data-total-vat]').textContent = currency(vat);
        form.querySelector('[data-total-gross]').textContent = currency(total + vat);
        form.querySelector('[data-add-item]').disabled = rows.children.length >= 20;
    }
    form.querySelector('[data-add-item]').addEventListener('click', () => {
        if (rows.children.length >= 20) return;
        const template = document.getElementById('if-item-template');
        const container = document.createElement('div');
        container.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++));
        rows.append(container.firstElementChild);
        rows.lastElementChild.querySelector('input').focus();
        update();
    });
    rows.addEventListener('click', event => { if (event.target.closest('[data-remove-item]') && rows.children.length > 1) { event.target.closest('[data-offer-row]').remove(); update(); } });
    rows.addEventListener('input', update);
    update();
}
