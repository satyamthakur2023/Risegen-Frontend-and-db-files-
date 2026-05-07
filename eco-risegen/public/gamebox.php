<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RiseGen | Pro Entrance Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;500;700&display=swap');
        body { font-family: 'Space Grotesk', sans-serif; }
        .glass-card {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        .option-btn { transition: all 0.2s ease; }
        .option-btn:hover {
            transform: translateX(10px);
            background: rgba(59, 130, 246, 0.15);
            border-color: #3b82f6;
        }
        .selected-answer {
            background: rgba(16, 185, 129, 0.2) !important;
            border-color: #10b981 !important;
        }
        .hidden { display: none !important; }
    </style>
</head>
<body class="bg-[#020617] text-slate-200 min-h-screen flex items-center justify-center p-4">

    <div id="cheat-warning" class="hidden fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-red-600 text-white px-6 py-2 rounded-full font-bold animate__animated animate__shakeX">
        ⚠️ Warning: Tab Switching Detected!
    </div>

    <div id="main-container" class="w-full max-w-2xl glass-card rounded-3xl p-8 lg:p-12 relative overflow-hidden">
        
        <div id="state-welcome" class="text-center animate__animated animate__fadeIn">
            <h1 class="text-4xl font-bold text-white mb-2">RiseGen Entrance</h1>
            <p class="text-slate-400 mb-8">Select your examination parameters</p>
            
            <div class="space-y-6 max-w-sm mx-auto">
                <input type="text" id="username" placeholder="Full Name" 
                    class="w-full bg-slate-800/50 border border-slate-700 p-4 rounded-2xl outline-none focus:border-blue-500 text-center text-lg font-medium">
                
                <div class="flex gap-2">
                    <button onclick="setLimit(10)" id="btn-10" class="flex-1 py-3 rounded-xl border-2 border-slate-700 hover:border-blue-500 transition-all font-bold bg-slate-800 text-sm">10 Qs</button>
                    <button onclick="setLimit(20)" id="btn-20" class="flex-1 py-3 rounded-xl border-2 border-blue-500 transition-all font-bold bg-slate-800 text-sm">20 Qs</button>
                    <button onclick="setLimit(40)" id="btn-40" class="flex-1 py-3 rounded-xl border-2 border-slate-700 hover:border-blue-500 transition-all font-bold bg-slate-800 text-sm">40 Qs</button>
                </div>

                <button onclick="initiateTest()" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-2xl shadow-xl shadow-blue-900/20 active:scale-95 transition-transform">
                    START EXAMINATION
                </button>
            </div>
        </div>

        <div id="state-testing" class="hidden animate__animated animate__fadeIn">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <span id="question-count" class="text-xs font-bold text-blue-400 uppercase tracking-widest">Question 1 of --</span>
                    <h2 id="question-text" class="text-2xl font-bold text-white mt-2 leading-tight">...</h2>
                </div>
                <div class="bg-slate-800 px-4 py-2 rounded-xl border border-slate-700">
                    <div id="timer" class="text-xl font-mono text-orange-400 font-bold">10:00</div>
                </div>
            </div>
            <div id="options-grid" class="grid grid-cols-1 gap-3"></div>
            <div class="mt-12">
                <div class="h-1.5 bg-slate-800 rounded-full overflow-hidden">
                    <div id="progress-bar" class="h-full bg-blue-500 transition-all duration-500" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <div id="state-results" class="hidden text-center animate__animated animate__zoomIn">
            <div id="result-visual" class="text-7xl mb-6"></div>
            <h2 id="result-title" class="text-5xl font-black mb-4 uppercase"></h2>
            <p id="result-score" class="text-slate-400 text-xl mb-10"></p>
            
            <div class="grid grid-cols-1 gap-4">
                <button id="download-cert" class="hidden bg-emerald-600 hover:bg-emerald-500 py-4 rounded-2xl font-bold text-white shadow-lg transition-all">
                    DOWNLOAD CERTIFICATE
                </button>
                <button onclick="window.location.reload()" class="py-4 rounded-2xl border border-slate-700 text-slate-400 hover:bg-slate-800 transition-all font-medium">
                    EXIT PORTAL
                </button>
            </div>
        </div>
    </div>

    <script>
        let testData = [];
        let currentIdx = 0;
        let userAnswers = [];
        let timerSeconds = 600;
        let timerInterval;
        let questionLimit = 20;

        function setLimit(num) {
            questionLimit = num;
            [10, 20, 40].forEach(n => {
                const btn = document.getElementById(`btn-${n}`);
                if(btn) {
                    btn.classList.toggle('border-blue-500', n === num);
                    btn.classList.toggle('border-slate-700', n !== num);
                }
            });
        }

        async function initiateTest() {
            const name = document.getElementById('username').value.trim();
            if(!name) return alert("Please enter your name.");

            try {
                const res = await fetch(`api.php?limit=${questionLimit}`);
                testData = await res.json();
                if(testData.error) throw new Error(testData.error);

                document.getElementById('state-welcome').classList.add('hidden');
                document.getElementById('state-testing').classList.remove('hidden');
                startTimer();
                renderQuestion();
            } catch (err) {
                alert("Infrastructure Error: Database connection failed.");
            }
        }

        function startTimer() {
            timerInterval = setInterval(() => {
                timerSeconds--;
                let mins = Math.floor(timerSeconds / 60);
                let secs = timerSeconds % 60;
                document.getElementById('timer').innerText = `${mins}:${secs < 10 ? '0' : ''}${secs}`;
                if(timerSeconds <= 0) finishTest();
            }, 1000);
        }

        function renderQuestion() {
            const q = testData[currentIdx];
            const grid = document.getElementById('options-grid');
            document.getElementById('question-text').innerText = q.question_text;
            document.getElementById('question-count').innerText = `Question ${currentIdx + 1} of ${testData.length}`;
            grid.innerHTML = '';

            let options = JSON.parse(q.options);
            options.forEach(opt => {
                const btn = document.createElement('button');
                btn.className = "option-btn w-full text-left p-5 bg-slate-800/40 border border-slate-700 rounded-2xl transition-all font-medium";
                btn.innerText = opt;
                btn.onclick = (e) => {
                    e.target.classList.add('selected-answer');
                    setTimeout(() => processAnswer(opt), 300);
                };
                grid.appendChild(btn);
            });
            document.getElementById('progress-bar').style.width = `${(currentIdx / testData.length) * 100}%`;
        }

        function processAnswer(answer) {
            let correctAnswer = testData[currentIdx].correct_answer;
            userAnswers.push({ selected: answer, correct: correctAnswer });
            if(currentIdx + 1 < testData.length) {
                currentIdx++;
                renderQuestion();
            } else {
                finishTest();
            }
        }

        async function finishTest() {
            clearInterval(timerInterval);
            const name = document.getElementById('username').value;
            let correctCount = userAnswers.filter(a => a.selected === a.correct).length;
            let finalScore = Math.round((correctCount / testData.length) * 100);

            const res = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: name, score: finalScore })
            });
            const result = await res.json();

            document.getElementById('state-testing').classList.add('hidden');
            document.getElementById('state-results').classList.remove('hidden');
            
            const title = document.getElementById('result-title');
            const visual = document.getElementById('result-visual');
            
            if(finalScore >= 70) {
                visual.innerText = '🏆';
                title.innerText = 'EXAMINATION PASSED';
                title.className = "text-5xl font-black mb-4 text-emerald-500";
                document.getElementById('download-cert').classList.remove('hidden');
                document.getElementById('download-cert').onclick = () => {
                    window.location.href = `cert.php?cert_id=${result.cert_id}&name=${encodeURIComponent(name)}&score=${finalScore}`;
                };
            } else {
                visual.innerText = '📉';
                title.innerText = 'REJECTED';
                title.className = "text-5xl font-black mb-4 text-red-500";
            }
            document.getElementById('result-score').innerText = `Infrastructure Score: ${finalScore}% (70% Required)`;
        }
    </script>
</body>
</html>