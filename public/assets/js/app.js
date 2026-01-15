
// public/assets/js/app.js
(function(){
  const $ = (s) => document.querySelector(s);
  const $$ = (s) => Array.from(document.querySelectorAll(s));

  const rowsEl = $("#rows");
  const hero = $("#hero");
  const btnShuffle = $("#btnShuffle");
  const searchInput = $("#searchInput");
  const spinner = $("#spinner");
  const loadText = $("#loadText");

  const apiUrl = rowsEl?.dataset.api || "";
  const limit = parseInt(rowsEl?.dataset.limit || "24", 10) || 24;
  let offset = parseInt(rowsEl?.dataset.offset || "0", 10) || 0;

  // cards list (DOM) for hero + local filtering visibility
  function allCards(){ return $$(".card"); }

  function pick(list){ return list.length ? list[Math.floor(Math.random()*list.length)] : null; }

  function setHeroFromCard(card){
    if(!card || !hero) return;
    const src = card.dataset.src || "";
    const caption = card.dataset.caption || "Scroll kayak Netflix, tapi isinya kita 💞";
    const tags = (card.dataset.tags || "").split(",").map(s=>s.trim()).filter(Boolean);

    hero.style.setProperty("--hero-bg", `url('${src}')`);
  }

  function shuffle(){
    const list = allCards().filter(c => c.style.display !== "none");
    setHeroFromCard(pick(list));
  }

  btnShuffle?.addEventListener("click", shuffle);

  // Create row section if not exists
  function ensureRow(rowName){
    const existing = document.querySelector(`.row[data-row="${CSS.escape(rowName)}"]`);
    if(existing) return existing;

    const section = document.createElement("section");
    section.className = "row";
    section.dataset.row = rowName;
    section.innerHTML = `
      <div class="row-head">
        <h2 class="row-title"></h2>
        <div class="row-meta">0 item</div>
      </div>
      <div class="cards" aria-label=""></div>
    `;
    section.querySelector(".row-title").textContent = rowName;
    section.querySelector(".cards").setAttribute("aria-label", rowName);

    rowsEl.insertBefore(section, rowsEl.querySelector(".infinite"));
    return section;
  }

  function updateRowMeta(section){
    const cards = section.querySelectorAll(".card").length;
    const meta = section.querySelector(".row-meta");
    if(meta) meta.textContent = `${cards} item`;
  }

  function cardHtml(item){
    const url = `photo.php?id=${encodeURIComponent(item.id)}`;
    const tags = item.tags || "";
    const caption = item.caption || "";
    const title = item.title || "Untitled";
    const src = item.file_url || "";

    return `
      <a class="card"
         href="${url}"
         data-src="${src}"
         data-title="${escapeHtml(title)}"
         data-caption="${escapeHtml(caption)}"
         data-tags="${escapeHtml(tags)}">
        <img loading="lazy" src="${src}" alt="${escapeHtml(title)}"/>
        <div class="badge">${escapeHtml(item.created_at_pretty || "")}</div>
        <div class="grad"></div>
        <div class="cap">${escapeHtml(title)}</div>
      </a>
    `;
  }

  function escapeHtml(s){
    return String(s).replace(/[&<>"']/g, (c)=>({ "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;" }[c]));
  }

  let loading = false;
  let hasMore = true;
  let currentQuery = "";

  async function fetchNext(){
    if(loading || !hasMore || !apiUrl) return;
    loading = true;
    spinner?.style && (spinner.style.display = "");
    loadText && (loadText.textContent = "Memuat lagi…");

    const params = new URLSearchParams();
    params.set("offset", String(offset));
    params.set("limit", String(limit));
    if(currentQuery) params.set("q", currentQuery);

    try{
      const res = await fetch(`${apiUrl}?${params.toString()}`, { headers: { "Accept":"application/json" }});
      if(!res.ok) throw new Error("HTTP " + res.status);
      const data = await res.json();

      hasMore = !!data.hasMore;
      offset = Number(data.nextOffset || (offset + limit));

      const items = Array.isArray(data.items) ? data.items : [];
      // append items to corresponding rows
      for(const it of items){
        const rowName = it.row_name || "Moments";
        const section = ensureRow(rowName);
        const cardsWrap = section.querySelector(".cards");
        cardsWrap.insertAdjacentHTML("beforeend", cardHtml(it));
        updateRowMeta(section);
      }

      // re-bind: nothing required because cards are links; hero uses dataset
      shuffle();

      if(!hasMore){
        spinner?.style && (spinner.style.display = "none");
        loadText && (loadText.textContent = "Sudah sampai akhir 💘");
      }
    }catch(e){
      loadText && (loadText.textContent = "Gagal memuat. Scroll lagi untuk coba ulang.");
    }finally{
      loading = false;
    }
  }

  // Infinite scroll via IntersectionObserver
  const sentinel = rowsEl?.querySelector(".infinite");
  if(sentinel && "IntersectionObserver" in window){
    const io = new IntersectionObserver((entries)=>{
      for(const ent of entries){
        if(ent.isIntersecting) fetchNext();
      }
    }, { root:null, rootMargin:"800px 0px", threshold:0.01 });
    io.observe(sentinel);
  } else {
    // fallback
    window.addEventListener("scroll", ()=>{
      if((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1200){
        fetchNext();
      }
    });
  }

  // Server-side search (resets list then infinite loads again)
  function clearAllRows(){
    // remove existing rows except sentinel
    const all = Array.from(rowsEl.querySelectorAll(".row"));
    all.forEach(n => n.remove());
  }

  let searchTimer = null;
  function applySearch(q){
    currentQuery = (q || "").trim();
    // reset paging
    offset = 0;
    hasMore = true;
    clearAllRows();
    spinner?.style && (spinner.style.display = "");
    loadText && (loadText.textContent = "Memuat lagi…");
    fetchNext();
  }

  searchInput?.addEventListener("input", (e)=>{
    const v = e.target.value;
    if(searchTimer) clearTimeout(searchTimer);
    searchTimer = setTimeout(()=>applySearch(v), 250);
  });

  // initial hero
  shuffle();
})();
