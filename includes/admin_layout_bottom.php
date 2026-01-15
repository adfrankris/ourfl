<?php
// includes/admin_layout_bottom.php
declare(strict_types=1);
?>
    </main>
    <footer class="admin-footer">@AditriFrankris</footer>
  </div>

<script>
(function(){
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('sbToggle');
  const mobile = document.getElementById('mobileMenu');
  function setCollapsed(v){
    if(v) sidebar.classList.add('collapsed'); else sidebar.classList.remove('collapsed');
    localStorage.setItem('ofx_sb', v ? '1':'0');
  }
  const saved = localStorage.getItem('ofx_sb');
  if(saved === '1') sidebar.classList.add('collapsed');
  toggle?.addEventListener('click', ()=> setCollapsed(!sidebar.classList.contains('collapsed')));
  mobile?.addEventListener('click', ()=>{
    sidebar.classList.toggle('open');
  });
  document.addEventListener('click', (e)=>{
    if(!sidebar) return;
    const t = e.target;
    if(window.innerWidth <= 980){
      if(!sidebar.contains(t) && t !== mobile){
        sidebar.classList.remove('open');
      }
    }
  });
})();
</script>
</body>
</html>
