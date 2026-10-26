const button=document.querySelector('.menu');const nav=document.querySelector('#nav');
button?.addEventListener('click',()=>{const open=button.getAttribute('aria-expanded')!=='true';button.setAttribute('aria-expanded',String(open));nav.classList.toggle('open',open)});
nav?.addEventListener('click',e=>{if(e.target.closest('a')){button.setAttribute('aria-expanded','false');nav.classList.remove('open')}});
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&button?.getAttribute('aria-expanded')==='true'){button.setAttribute('aria-expanded','false');nav.classList.remove('open');button.focus()}});
