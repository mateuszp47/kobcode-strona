const menu=document.querySelector('.menu');
const navigation=document.querySelector('#nav');
function closeMenu(focus=false){menu?.setAttribute('aria-expanded','false');navigation?.classList.remove('open');if(focus)menu?.focus()}
menu?.addEventListener('click',()=>{const open=menu.getAttribute('aria-expanded')!=='true';menu.setAttribute('aria-expanded',String(open));navigation?.classList.toggle('open',open)});
navigation?.addEventListener('click',event=>{if(event.target.closest('a'))closeMenu()});
document.addEventListener('keydown',event=>{if(event.key==='Escape'&&menu?.getAttribute('aria-expanded')==='true')closeMenu(true)});
document.addEventListener('click',event=>{if(!navigation?.contains(event.target)&&!menu?.contains(event.target))closeMenu()});
window.matchMedia('(min-width: 981px)').addEventListener('change',event=>{if(event.matches)closeMenu()});
for(const switcher of document.querySelectorAll('[data-projects]')){
 const controls=[...switcher.querySelectorAll('[data-work-select]')];
 const panels=[...switcher.querySelectorAll('[data-work-panel]')];
 const select=key=>{for(const control of controls)control.setAttribute('aria-pressed',String(control.dataset.workSelect===key));for(const panel of panels)panel.hidden=panel.dataset.workPanel!==key};
 for(const control of controls)control.addEventListener('click',()=>select(control.dataset.workSelect));
 select(controls.find(control=>control.getAttribute('aria-pressed')==='true')?.dataset.workSelect??controls[0]?.dataset.workSelect);
}
if('IntersectionObserver' in window&&!window.matchMedia('(prefers-reduced-motion: reduce)').matches){
 const observer=new IntersectionObserver(entries=>{for(const entry of entries){if(entry.isIntersecting){entry.target.classList.add('is-visible');observer.unobserve(entry.target)}}},{threshold:.08});
 for(const element of document.querySelectorAll('[data-reveal]')){if(element.getBoundingClientRect().top>window.innerHeight){element.classList.add('will-reveal');observer.observe(element)}}
}
