(async function(){
  const scriptUrl = new URL(document.currentScript.src, document.baseURI);
  const base = scriptUrl.href.replace(/load-assets\.js$/, '');
  async function load(id, file){
    try{
      const res = await fetch(base + file);
      if(res.ok){
        const html = await res.text();
        const el = document.getElementById(id);
        if(el) el.innerHTML = html;
      }
    }catch(e){console.error('Failed to load', file, e);}
  }
  await load('header','warung_header.html');
  await load('footer','warung_footer.html');

  // mobile menu toggle
  const toggle = document.getElementById('menu-toggle');
  const nav = document.querySelector('.main-nav');
  if(toggle && nav){
    toggle.addEventListener('click',()=>nav.classList.toggle('open'));
  }
})();
