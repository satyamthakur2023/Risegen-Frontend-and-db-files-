<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RiseGen | Intelligence Portal v4.2</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700;900&display=swap');
        
        :root { 
            --accent: #3b82f6; 
            --bg-dark: #050505;
            --card-bg: rgba(255, 255, 255, 0.01);
        }

        body { 
            background-color: var(--bg-dark); 
            color: #ffffff; 
            font-family: 'Outfit', sans-serif; 
            scroll-behavior: smooth; 
            overflow-x: hidden;
        }

        ::-webkit-scrollbar { width: 3px; }
        ::-webkit-scrollbar-track { background: var(--bg-dark); }
        ::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }

        .text-gradient {
            background: linear-gradient(to right, #ffffff, #64748b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        #reading-mode {
            position: fixed; inset: 0; 
            background: rgba(0, 0, 0, 0.96);
            backdrop-filter: blur(35px); 
            z-index: 2000; 
            display: none;
            overflow-y: auto; 
            padding: 5%;
        }

        .blog-card {
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.04);
            transform: translateZ(0);
            backface-visibility: hidden;
        }

        .blog-card:hover {
            transform: translateY(-20px) scale(1.03);
            border-color: var(--accent);
            background: rgba(59, 130, 246, 0.05);
            box-shadow: 0 40px 80px rgba(0,0,0,0.7);
        }

        .img-pan { 
            transition: transform 15s ease-out, filter 1s ease; 
            filter: grayscale(100%) brightness(0.5);
        }

        .blog-card:hover .img-pan { 
            transform: scale(1.3); 
            filter: grayscale(0%) brightness(1);
        }

        .search-container input:focus {
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.15);
            border-color: var(--accent);
        }

        .loader-ring {
            border: 3px solid rgba(59, 130, 246, 0.1);
            border-top: 3px solid var(--accent);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* Skeleton shimmer */
        .skeleton {
            background: linear-gradient(90deg, rgba(255,255,255,0.03) 25%, rgba(255,255,255,0.07) 50%, rgba(255,255,255,0.03) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 3rem;
            border: 1px solid rgba(255,255,255,0.04);
        }

        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        /* Category filter pills */
        .cat-pill {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.08);
            color: #64748b;
        }

        .cat-pill:hover, .cat-pill.active {
            border-color: var(--accent);
            color: #3b82f6;
            background: rgba(59,130,246,0.08);
        }
    </style>
</head>
<body>

    <div id="reading-mode" class="animate__animated">
        <div class="max-w-5xl mx-auto relative">
            <button onclick="closeReader()" class="fixed top-8 right-8 text-slate-400 hover:text-white transition-all z-[2001] bg-white/5 p-3 rounded-full border border-white/10 backdrop-blur-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div id="reader-content"></div>
        </div>
    </div>

    <nav class="p-6 flex justify-between items-center border-b border-white/5 sticky top-0 bg-black/90 backdrop-blur-xl z-[1000]">
        <div class="flex items-center gap-6">
            <div class="text-2xl font-black tracking-tighter">RISE<span class="text-blue-600">GEN.</span></div>
            <div class="hidden lg:flex items-center gap-3 text-[10px] text-blue-500 font-bold uppercase tracking-[0.3em]">
                <span class="loader-ring !w-3 !h-3 !border-1"></span>
                Node Matrix Sync
            </div>
        </div>
        <div class="flex gap-4">
            <a href="welcome.php" class="px-5 py-2 text-[10px] font-bold tracking-widest text-slate-400 border border-white/5 rounded-lg hover:text-blue-500 transition">DASHBOARD</a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-6 md:p-12">
        <header class="py-16 md:py-28">
            <div class="inline-flex items-center gap-3 px-4 py-1.5 rounded-full bg-blue-600/10 border border-blue-500/20 text-blue-500 text-[10px] font-black mb-8 tracking-[0.4em] uppercase">
                Active Global Intelligence Protocol
            </div>
            <h1 class="text-7xl md:text-9xl font-black text-gradient leading-[0.85] mb-8 tracking-tighter">
                SECURE<br>INSIGHTS.
            </h1>
            <p class="max-w-xl text-slate-500 text-lg font-light leading-relaxed border-l-2 border-blue-600/30 pl-8">
                Monitoring 400+ terrestrial nodes. Synchronizing high-fidelity data streams via RiseGen Neural Logic.
            </p>
        </header>

        <div class="search-container mb-8 sticky top-28 z-[900]">
            <input type="text" id="search-db" oninput="initiateSearch()" placeholder="SEARCH NODE BY TITLE, CATEGORY, OR CONTENT..." 
                class="w-full bg-black/60 border border-white/10 rounded-[2rem] py-8 pl-12 pr-8 text-[11px] tracking-[0.3em] focus:outline-none transition-all uppercase font-black backdrop-blur-2xl">
        </div>

        <!-- Category Filter Pills -->
        <div id="cat-filters" class="flex flex-wrap gap-3 mb-16">
            <span class="cat-pill active px-5 py-2 rounded-full text-[10px] font-black tracking-widest uppercase" onclick="filterCategory('')">All</span>
            <span class="cat-pill px-5 py-2 rounded-full text-[10px] font-black tracking-widest uppercase" onclick="filterCategory('Infrastructure')">Infrastructure</span>
            <span class="cat-pill px-5 py-2 rounded-full text-[10px] font-black tracking-widest uppercase" onclick="filterCategory('Cyber-Security')">Cyber-Security</span>
            <span class="cat-pill px-5 py-2 rounded-full text-[10px] font-black tracking-widest uppercase" onclick="filterCategory('Neural-Logic')">Neural-Logic</span>
            <span class="cat-pill px-5 py-2 rounded-full text-[10px] font-black tracking-widest uppercase" onclick="filterCategory('Data-Science')">Data-Science</span>
            <span class="cat-pill px-5 py-2 rounded-full text-[10px] font-black tracking-widest uppercase" onclick="filterCategory('SaaS')">SaaS</span>
        </div>

        <div id="blog-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 mb-20"></div>

        <!-- Empty state -->
        <div id="empty-state" class="hidden flex-col items-center justify-center py-28 text-center">
            <div class="text-6xl mb-6">🔍</div>
            <div class="text-[11px] font-black tracking-[0.4em] text-slate-500 uppercase mb-3">No Nodes Found</div>
            <div class="text-slate-600 text-sm">Try a different search term or category</div>
        </div>

        <div id="sync-status" class="flex flex-col items-center justify-center py-20 opacity-50">
            <div class="loader-ring mb-6"></div>
            <span id="status-text" class="text-[10px] font-black tracking-[0.5em] text-slate-500 uppercase">Synchronizing...</span>
        </div>
    </main>

    <script>
        let blogData = [];
        let offset = 0;
        const limit = 12;
        let isFetching = false;
        let allNodesSynced = false;
        let searchQuery = "";
        let categoryFilter = "";
        let searchTimeout;

        const assetMap = {
            'Infrastructure': ['datacenter','server','network','fiber','hardware','rack'],
            'Cyber-Security':  ['security','hacker','encryption','firewall','cyber','lock'],
            'Neural-Logic':    ['artificial intelligence','neural network','chip','circuit','robot','machine learning'],
            'Data-Science':    ['data','analytics','graph','binary','statistics','visualization'],
            'SaaS':            ['software','cloud','api','dashboard','saas','interface']
        };

        // Fallback gradient covers per category if image fails
        const fallbackGradients = {
            'Infrastructure': 'linear-gradient(135deg,#1e3a5f,#0f172a)',
            'Cyber-Security':  'linear-gradient(135deg,#1a1a2e,#16213e)',
            'Neural-Logic':    'linear-gradient(135deg,#0d1b2a,#1b263b)',
            'Data-Science':    'linear-gradient(135deg,#0f2027,#203a43)',
            'SaaS':            'linear-gradient(135deg,#141e30,#243b55)'
        };

        function imgSeed(id, title) {
            let h = 0;
            const s = String(id) + String(title);
            for (let i = 0; i < s.length; i++) h = (Math.imul(31, h) + s.charCodeAt(i)) | 0;
            return Math.abs(h);
        }

        function getCardImg(node, w, h) {
            const seed = imgSeed(node.id, node.title);
            // picsum.photos is stable, always online, unique image per seed
            return `https://picsum.photos/seed/${seed}/${w}/${h}`;
        }

        function readTime(content) {
            const words = content ? content.replace(/<[^>]+>/g, '').split(/\s+/).length : 0;
            return Math.max(1, Math.round(words / 200)) + ' min read';
        }

        function resetGrid() {
            offset = 0;
            allNodesSynced = false;
            blogData = [];
            document.getElementById('blog-grid').innerHTML = '';
            document.getElementById('empty-state').classList.add('hidden');
            document.getElementById('empty-state').classList.remove('flex');
            document.getElementById('status-text').innerText = 'Synchronizing...';
            document.querySelector('#sync-status .loader-ring').style.display = 'block';
            document.getElementById('sync-status').style.display = 'flex';
        }

        function showSkeletons() {
            const grid = document.getElementById('blog-grid');
            for (let i = 0; i < 6; i++) {
                const s = document.createElement('div');
                s.className = 'skeleton h-96';
                s.dataset.skeleton = true;
                grid.appendChild(s);
            }
        }

        function removeSkeletons() {
            document.querySelectorAll('[data-skeleton]').forEach(el => el.remove());
        }

        async function fetchIntelligence() {
            if (isFetching || allNodesSynced) return;
            isFetching = true;

            if (offset === 0) showSkeletons();

            try {
                const url = `fetch_blogs.php?limit=${limit}&offset=${offset}&search=${encodeURIComponent(searchQuery)}&category=${encodeURIComponent(categoryFilter)}`;
                const response = await fetch(url);
                const json = await response.json();
                const data = json.nodes || json; // support both old and new response shape

                removeSkeletons();

                if (data.length < limit) {
                    allNodesSynced = true;
                    document.getElementById('status-text').innerText = 'All Nodes Fully Decrypted';
                    document.querySelector('#sync-status .loader-ring').style.display = 'none';
                }

                if (offset === 0 && data.length === 0) {
                    document.getElementById('sync-status').style.display = 'none';
                    document.getElementById('empty-state').classList.remove('hidden');
                    document.getElementById('empty-state').classList.add('flex');
                    isFetching = false;
                    return;
                }

                blogData = [...blogData, ...data];
                renderNodes(data);
                offset += limit;
            } catch (err) {
                removeSkeletons();
                console.error('Critical: Link to Node Master lost.', err);
            } finally {
                isFetching = false;
            }
        }

        function initiateSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const input = document.getElementById('search-db').value;
                if (input === searchQuery) return;
                searchQuery = input;
                resetGrid();
                fetchIntelligence();
            }, 500);
        }

        function filterCategory(cat) {
            if (cat === categoryFilter) return;
            categoryFilter = cat;
            // Update active pill
            document.querySelectorAll('.cat-pill').forEach(p => {
                p.classList.toggle('active', p.textContent.trim().toLowerCase() === (cat || 'all').toLowerCase());
            });
            resetGrid();
            fetchIntelligence();
        }

        function renderNodes(nodes) {
            const grid = document.getElementById('blog-grid');
            const fallback = node => fallbackGradients[node.category] || 'linear-gradient(135deg,#0f172a,#1e293b)';

            nodes.forEach(node => {
                const img = getCardImg(node, 600, 400);
                const card = document.createElement('div');
                card.className = 'blog-card rounded-[3rem] overflow-hidden group cursor-pointer animate__animated animate__fadeInUp';
                card.onclick = () => decryptNode(node.id);

                card.innerHTML = `
                    <div class="h-64 overflow-hidden relative bg-slate-900" style="background:${fallback(node)}">
                        <img src="${img}" loading="lazy" class="img-pan absolute inset-0 w-full h-full object-cover"
                             onerror="this.style.display='none'">
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-80"></div>
                        <div class="absolute top-6 right-6">
                            <span class="bg-blue-600/20 backdrop-blur-md border border-blue-500/30 px-4 py-1.5 rounded-full text-[9px] font-black tracking-widest text-blue-400 uppercase">
                                ${node.category}
                            </span>
                        </div>
                    </div>
                    <div class="p-10">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="text-[9px] font-bold text-slate-500 tracking-[0.3em] uppercase">${node.date}</span>
                            <span class="text-[9px] font-bold text-slate-600 tracking-[0.2em] uppercase">· ${readTime(node.content)}</span>
                        </div>
                        <h3 class="text-2xl font-bold leading-tight group-hover:text-blue-400 transition-colors">${node.title}</h3>
                        <div class="mt-8 flex items-center text-[10px] font-black uppercase tracking-[0.3em] text-blue-600 opacity-0 group-hover:opacity-100 transition-all">
                            View Intelligence Report <span class="ml-3 group-hover:ml-5 transition-all">→</span>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        function decryptNode(id) {
            const node = blogData.find(n => n.id == id);
            if (!node) return;
            const modal = document.getElementById('reading-mode');
            document.getElementById('reader-content').innerHTML = `
                <div class="animate__animated animate__fadeInUp">
                    <header class="mb-16">
                        <div class="text-blue-500 font-black tracking-[0.5em] text-xs mb-6 uppercase">Source: Node-${node.id} // ${node.category}</div>
                        <h1 class="text-6xl md:text-8xl font-black mb-12 tracking-tighter leading-none">${node.title}</h1>
                        <div class="flex items-center gap-6 mb-12 text-[10px] font-bold text-slate-500 tracking-[0.3em] uppercase">
                            <span>${node.date}</span>
                            <span>·</span>
                            <span>${readTime(node.content)}</span>
                            <span>·</span>
                            <span>${node.category}</span>
                        </div>
                        <img src="${getCardImg(node, 1200, 600)}" class="w-full h-[500px] object-cover rounded-[3rem] border border-white/5 shadow-2xl"
                             onerror="this.style.display='none'">
                    </header>
                    <article class="text-slate-300 text-xl md:text-2xl leading-relaxed space-y-12 max-w-4xl font-light">
                        ${node.content}
                    </article>
                    <div class="mt-20 pt-10 border-t border-white/5 text-slate-600 text-[10px] tracking-[0.3em] uppercase">
                        Node Transmission Date: ${node.date} | Security Protocol: RiseGen v4.2
                    </div>
                </div>
            `;
            modal.style.display = 'block';
            modal.scrollTop = 0;
            document.body.style.overflow = 'hidden';
        }

        function closeReader() {
            document.getElementById('reading-mode').classList.add('animate__fadeOut');
            setTimeout(() => {
                const modal = document.getElementById('reading-mode');
                modal.style.display = 'none';
                modal.classList.remove('animate__fadeOut');
                document.body.style.overflow = 'auto';
            }, 600);
        }

        // ESC key closes modal
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeReader(); });

        window.addEventListener('scroll', () => {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 900) {
                fetchIntelligence();
            }
        });

        window.onload = fetchIntelligence;
    </script>
</body>
</html>
