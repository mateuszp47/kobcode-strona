const button=document.querySelector('.menu');const nav=document.querySelector('#nav');
const closeMenu=(focus=false)=>{button?.setAttribute('aria-expanded','false');nav?.classList.remove('open');if(focus)button?.focus()};
button?.addEventListener('click',()=>{const open=button.getAttribute('aria-expanded')!=='true';button.setAttribute('aria-expanded',String(open));nav?.classList.toggle('open',open)});
nav?.addEventListener('click',e=>{if(e.target.closest('a'))closeMenu()});
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&button?.getAttribute('aria-expanded')==='true')closeMenu(true)});
document.addEventListener('click',e=>{if(!nav?.contains(e.target)&&!button?.contains(e.target))closeMenu()});
const desktop=window.matchMedia('(min-width: 981px)');desktop.addEventListener('change',e=>{if(e.matches)closeMenu()});
