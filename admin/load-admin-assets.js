(async function(){
  const scriptUrl = new URL(document.currentScript.src, document.baseURI);
  const base = scriptUrl.href.replace(/load-admin-assets\.js$/, '');
  async function load(id, file){
    try{
      const res = await fetch(base + file);
      if(res.ok){
        const html = await res.text();
        const el = document.getElementById(id);
        if(el){
          el.innerHTML = html;
          if(file === 'admin_header.html'){
            const logout = el.querySelector('#adminLogout');
            if(logout){
              logout.addEventListener('click', function(e){
                e.preventDefault();
                localStorage.removeItem('admin_token');
                window.location = '/admin/login';
              });
            }
          }
        }
      }
    }catch(e){console.error('Failed to load', file, e);}
  }
  await load('adminHeader','admin_header.html');
  await load('adminFooter','admin_footer.html');
})();
