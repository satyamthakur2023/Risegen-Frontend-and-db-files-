<?php
require_once 'config.php';  
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risegen - Modern Learning & Hiring Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            900: '#312e81',
                        },
                        dark: '#0f172a',
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft': '0 20px 40px -15px rgba(0,0,0,0.05)',
                        'glow': '0 0 20px rgba(99, 102, 241, 0.4)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f8fafc;
            color: #0f172a;
        }
        /* Glassmorphism Navbar */
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        .navbar-scrolled {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        /* Animated Gradient Text */
        .text-gradient {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-image: linear-gradient(90deg, #4f46e5, #ec4899);
        }
        /* Soft Blob Background */
        .bg-blob {
            position: absolute;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.4;
            border-radius: 50%;
        }
    </style>
</head>
<body class="antialiased selection:bg-brand-500 selection:text-white">

    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 glass-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a class="flex items-center space-x-2 text-2xl font-extrabold text-dark tracking-tight" href="#home">
                    <div class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center shadow-glow">
                        <i class="fas fa-graduation-cap text-white text-xl"></i> 
                    </div>
                    <span>Risegen</span>
                </a>
                
                <div class="hidden md:flex items-center space-x-8">
                    <ul class="flex space-x-8 text-sm font-semibold text-slate-600">
                        <li><a class="hover:text-brand-600 transition" href="#home">Home</a></li>
                        <li><a class="hover:text-brand-600 transition" href="#how-it-works">How It Works</a></li>
                        <li><a class="hover:text-brand-600 transition" href="#features">Features</a></li>
                        <li><a class="hover:text-brand-600 transition" href="#pricing">Pricing</a></li>
                    </ul>
                   <div class="flex items-center space-x-4 pl-4 border-l border-slate-200">
    <a href="login.php" class="text-sm font-semibold text-slate-700 hover:text-brand-600 transition">
        Log in
    </a>
    <a href="User_Registration.php" class="px-5 py-2.5 text-sm font-bold text-white rounded-full bg-dark hover:bg-slate-800 shadow-lg transition transform hover:-translate-y-0.5">
        Get Started
    </a>
</div>
                </div>

                <button id="mobile-menu-button" class="md:hidden text-2xl text-dark p-2" aria-label="Toggle Menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <section id="home" class="relative pt-32 pb-20 md:pt-48 md:pb-32 overflow-hidden">
        <div class="bg-blob bg-brand-500 w-96 h-96 top-0 left-10"></div>
        <div class="bg-blob bg-pink-400 w-96 h-96 top-20 right-10"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-brand-50 border border-brand-100 text-brand-600 text-sm font-bold tracking-wide mb-6">
                🚀 The new standard in tech education
            </span>
            <h1 class="text-5xl sm:text-6xl md:text-7xl font-extrabold tracking-tight mb-8 text-dark">
                Master skills.<br> <span class="text-gradient">Land the job.</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                Your all-in-one platform for expert-led courses, smart testing, and direct access to curated job listings. Start evolving today.
            </p>
            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="gamebox.php" class="px-8 py-4 text-base font-bold text-white bg-brand-600 rounded-full shadow-glow hover:bg-brand-500 transition duration-300 transform hover:-translate-y-1 w-full sm:w-auto">
                    Take Assessment
                </a>
                <a href="https://risegen.onrender.com/" class="px-8 py-4 text-base font-bold text-slate-700 bg-white border border-slate-200 rounded-full shadow-sm hover:shadow-md transition duration-300 w-full sm:w-auto flex items-center justify-center">
                    Browse Jobs <i class="fas fa-arrow-right ml-2 text-sm"></i>
                </a>
            </div>

            <div class="mt-20 grid grid-cols-2 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div class="p-6 bg-white rounded-3xl shadow-soft border border-slate-100">
                    <span class="text-4xl font-extrabold text-brand-600 block mb-1">50+</span>
                    <span class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Free Features</span>
                </div>
                <div class="p-6 bg-white rounded-3xl shadow-soft border border-slate-100">
                    <span class="text-4xl font-extrabold text-brand-600 block mb-1">999+</span>
                    <span class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Job Listings</span>
                </div>
                <div class="col-span-2 md:col-span-1 p-6 bg-white rounded-3xl shadow-soft border border-slate-100">
                    <span class="text-4xl font-extrabold text-brand-600 block mb-1">500+</span>
                    <span class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Expert Courses</span>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-dark text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#4f46e5 1px, transparent 1px); background-size: 32px 32px;"></div>
        
        <div class="max-w-4xl mx-auto px-4 relative z-10">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-5xl font-extrabold mb-4">Knowledge Rush</h2>
                <p class="text-slate-400 text-lg">Stop watching. Start testing. Master your subject under pressure.</p>
            </div>

            <div class="bg-slate-900 border border-slate-800 p-8 rounded-[2rem] shadow-2xl relative" id="game-box">
                
                <div class="flex justify-between items-center mb-8 pb-6 border-b border-slate-800">
                    <div>
                        <div class="text-sm text-slate-400 font-semibold uppercase tracking-wider mb-1">Score</div>
                        <div id="score" class="text-4xl font-black text-emerald-400">0</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-400 font-semibold uppercase tracking-wider mb-1">Progress</div>
                        <div id="q-progress-text" class="text-xl font-bold text-white">0 / 0</div>
                    </div>
                </div>

                <div id="status-panel" class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-slate-800/50 p-4 rounded-2xl border border-slate-700/50 text-center">
                        <div class="text-xs font-semibold uppercase text-slate-400 mb-1">Points Value</div>
                        <div id="q-points" class="text-2xl font-bold text-yellow-400">--</div>
                    </div>
                    <div class="bg-slate-800/50 p-4 rounded-2xl border border-slate-700/50 text-center">
                        <div class="text-xs font-semibold uppercase text-slate-400 mb-1">Difficulty</div>
                        <div id="q-difficulty" class="text-2xl font-bold text-white">--</div>
                    </div>
                </div>

                <div id="quiz-area" class="min-h-[250px] flex flex-col justify-center">
                    <div id="start-screen" class="text-center">
                        <div class="w-20 h-20 bg-brand-600/20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-bolt text-3xl text-brand-500"></i>
                        </div>
                        <p class="text-lg text-slate-300 mb-8 max-w-md mx-auto">Ready to test your limits? Answer quickly for speed multipliers and climb the leaderboard.</p>
                        <button id="start-button" class="bg-brand-600 hover:bg-brand-500 text-white px-10 py-4 rounded-full font-bold text-lg shadow-glow transition transform hover:scale-105">
                            Start Challenge
                        </button>
                    </div>
                </div>

                <div id="feedback" class="mt-6 p-5 rounded-2xl text-center font-bold text-lg hidden border"></div>
            </div>
        </div>
    </section>

    
    <section id="infrastructure" class="py-24 bg-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: linear-gradient(to right, #0f172a 1px, transparent 1px), linear-gradient(to bottom, #0f172a 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-20">
                <span class="inline-block py-1 px-3 rounded-full bg-brand-50 border border-brand-100 text-brand-600 text-sm font-bold tracking-wide mb-4 shadow-sm">
                    Our Core Engine ⚙️
                </span>
                <h2 class="text-4xl md:text-5xl font-extrabold text-dark mb-4">The Learn, Earn, Repeat Ecosystem</h2>
                <p class="text-xl text-slate-500 max-w-3xl mx-auto">
                    We don't just teach you. We provide the infrastructure to turn your new skills directly into income through our exclusive Gigs Program.
                </p>
            </div>

            <div class="relative">
                <div class="hidden lg:block absolute top-1/2 left-0 w-full h-1 bg-gradient-to-r from-brand-100 via-brand-500 to-pink-500 transform -translate-y-1/2 z-0 rounded-full opacity-30"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative z-10">
                    
                    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-soft hover:-translate-y-2 hover:shadow-xl transition-all duration-300 relative group">
                        <div class="w-14 h-14 bg-slate-50 border-2 border-slate-100 rounded-2xl flex items-center justify-center mb-6 group-hover:border-brand-500 group-hover:bg-brand-50 transition-colors">
                            <i class="fas fa-route text-2xl text-brand-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-dark mb-3">1. Choose Your Path</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-4">
                            Enter the platform and pick your learning style. Start from scratch with <strong>Direct Learning</strong> (expert courses), or jump straight into <strong>Test-Based Learning</strong> if you already know the basics.
                        </p>
                        <div class="flex space-x-2">
                            <span class="text-xs font-bold px-2 py-1 bg-slate-100 text-slate-600 rounded">Courses</span>
                            <span class="text-xs font-bold px-2 py-1 bg-slate-100 text-slate-600 rounded">MCQs</span>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-soft hover:-translate-y-2 hover:shadow-xl transition-all duration-300 relative group">
                        <div class="w-14 h-14 bg-slate-50 border-2 border-slate-100 rounded-2xl flex items-center justify-center mb-6 group-hover:border-brand-500 group-hover:bg-brand-50 transition-colors">
                            <i class="fas fa-award text-2xl text-brand-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-dark mb-3">2. Prove Mastery</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-4">
                            Once your training is complete, take the final skill assessment. Pass the pressure-tested exam to prove your competence and unlock your verified, industry-recognized <strong>Certificate</strong>.
                        </p>
                    </div>

                    <div class="bg-dark p-8 rounded-3xl border border-slate-800 shadow-2xl hover:-translate-y-2 transition-all duration-300 relative group overflow-hidden transform lg:scale-105 z-20">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500 blur-[60px] opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        
                        <div class="w-14 h-14 bg-brand-600 border-2 border-brand-500 rounded-2xl flex items-center justify-center mb-6 shadow-glow">
                            <i class="fas fa-briefcase text-2xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">3. Enter Gigs Program</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-4">
                            Your certificate is your key. Certified users get exclusive access to our <strong>Gigs Dashboard</strong>, loaded daily with fresh freelance work, remote tasks, and full-time job details tailored to your exact skills.
                        </p>
                        <a href="#" class="inline-flex items-center text-sm font-bold text-brand-400 hover:text-brand-300 transition-colors">
                            Explore Gigs <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>

                    <div class="bg-gradient-to-br from-brand-600 to-pink-500 p-8 rounded-3xl shadow-glow hover:-translate-y-2 transition-all duration-300 relative group text-white">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-sm border-2 border-white/30 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-sync-alt text-2xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">4. Learn, Earn, Repeat</h3>
                        <p class="text-white/80 text-sm leading-relaxed mb-4">
                            Complete gigs, earn money, and identify new skills required for higher-paying jobs. Use your earnings to reinvest in advanced courses on our platform. The cycle of growth never stops.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </section>
    
    <section id="infrastructure" class="py-24 bg-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: linear-gradient(to right, #0f172a 1px, transparent 1px), linear-gradient(to bottom, #0f172a 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                
                <div class="order-2 lg:order-1">
                    <span class="inline-block py-1 px-3 rounded-full bg-brand-50 border border-brand-100 text-brand-600 text-sm font-bold tracking-wide mb-6 shadow-sm">
                        The Philosophy 🧠
                    </span>
                    <h2 class="text-4xl md:text-5xl font-extrabold text-dark mb-6 leading-tight">
                        Escape the <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-pink-500">"No Experience"</span> Trap
                    </h2>
                    <p class="text-lg text-slate-500 mb-10 leading-relaxed">
                        Traditional education leaves you with a piece of paper and zero real-world leverage. We engineered a self-sustaining engine designed to get you your first paycheck faster.
                    </p>
                    
                    <div class="space-y-6">
                        <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:border-brand-300 hover:bg-brand-50/30 transition-all duration-300 group">
                            <h4 class="text-xl font-bold text-dark mb-2 flex items-center">
                                <i class="fas fa-bolt text-brand-500 w-8"></i> Instant R.O.I.
                            </h4>
                            <p class="text-slate-500 text-sm leading-relaxed pl-8">
                                Don't wait years for graduation. Monetize your skills through our exclusive micro-gigs the exact same day you pass your assessment.
                            </p>
                        </div>

                        <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:border-emerald-300 hover:bg-emerald-50/30 transition-all duration-300 group">
                            <h4 class="text-xl font-bold text-dark mb-2 flex items-center">
                                <i class="fas fa-chart-line text-emerald-500 w-8"></i> Self-Funding Growth
                            </h4>
                            <p class="text-slate-500 text-sm leading-relaxed pl-8">
                                Broke? Use your freelance gig earnings from the platform to unlock premium courses and advanced tests. Your career education literally pays for itself.
                            </p>
                        </div>

                        <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 hover:border-pink-300 hover:bg-pink-50/30 transition-all duration-300 group">
                            <h4 class="text-xl font-bold text-dark mb-2 flex items-center">
                                <i class="fas fa-shield-alt text-pink-500 w-8"></i> Verified Proof of Work
                            </h4>
                            <p class="text-slate-500 text-sm leading-relaxed pl-8">
                                Recruiters don't trust standard resumes anymore. They trust our platform's global ranking system and your successfully completed gig history.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="order-1 lg:order-2 relative">
                    <div class="absolute inset-0 bg-gradient-to-tr from-brand-400 to-pink-400 rounded-full blur-[100px] opacity-20"></div>
                    
                    <div class="relative transform hover:-translate-y-2 transition-transform duration-500 shadow-2xl rounded-[2.5rem] overflow-hidden ring-1 ring-slate-800">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 450" class="w-full h-auto block bg-slate-900">
                            <defs>
                                <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#0f172a"/>
                                    <stop offset="100%" stop-color="#1e1b4b"/>
                                </linearGradient>
                                
                                <linearGradient id="lineGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#4f46e5"/>
                                    <stop offset="50%" stop-color="#10b981"/>
                                    <stop offset="100%" stop-color="#ec4899"/>
                                </linearGradient>

                                <style>
                                    @keyframes dash {
                                        to { stroke-dashoffset: -1000; }
                                    }
                                    .path-anim {
                                        stroke-dasharray: 15 25;
                                        animation: dash 20s linear infinite;
                                    }

                                    /* Synchronized Pulsing */
                                    @keyframes pulseLearn {
                                        0%, 100% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(79, 70, 229, 0)); }
                                        10% { transform: scale(1.15); filter: drop-shadow(0 0 30px rgba(79, 70, 229, 0.9)); }
                                        33% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(79, 70, 229, 0)); }
                                    }
                                    @keyframes pulseEarn {
                                        0%, 100% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(16, 185, 129, 0)); }
                                        33% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(16, 185, 129, 0)); }
                                        43% { transform: scale(1.15); filter: drop-shadow(0 0 30px rgba(16, 185, 129, 0.9)); }
                                        66% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(16, 185, 129, 0)); }
                                    }
                                    @keyframes pulseRepeat {
                                        0%, 100% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(236, 72, 153, 0)); }
                                        66% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(236, 72, 153, 0)); }
                                        76% { transform: scale(1.15); filter: drop-shadow(0 0 30px rgba(236, 72, 153, 0.9)); }
                                        100% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(236, 72, 153, 0)); }
                                    }

                                    /* Mathematically exact transform origins */
                                    .node-learn { animation: pulseLearn 6s infinite; transform-origin: 179px 295px; }
                                    .node-earn { animation: pulseEarn 6s infinite; transform-origin: 300px 85px; }
                                    .node-repeat { animation: pulseRepeat 6s infinite; transform-origin: 421px 295px; }
                                </style>
                            </defs>

                            <rect width="600" height="450" fill="url(#bgGrad)"/>

                            <circle cx="300" cy="225" r="140" fill="none" stroke="#1e293b" stroke-width="8"/>
                            <circle cx="300" cy="225" r="140" fill="none" stroke="url(#lineGrad)" stroke-width="4" class="path-anim"/>

                            <g class="node-learn">
                                <circle cx="179" cy="295" r="45" fill="#1e1b4b" stroke="#4f46e5" stroke-width="4"/>
                                <text x="179" y="307" fill="#a5b4fc" font-size="28" text-anchor="middle">🎓</text>
                            </g>
                            <rect x="129" y="360" width="100" height="28" rx="14" fill="#4f46e5"/>
                            <text x="179" y="379" fill="#fff" font-family="'Plus Jakarta Sans', sans-serif" font-size="13" font-weight="bold" text-anchor="middle">1. LEARN</text>

                            <g class="node-earn">
                                <circle cx="300" cy="85" r="50" fill="#064e3b" stroke="#10b981" stroke-width="4"/>
                                <text x="300" y="98" fill="#a7f3d0" font-size="32" text-anchor="middle">💰</text>
                            </g>
                            <rect x="250" y="155" width="100" height="28" rx="14" fill="#10b981"/>
                            <text x="300" y="174" fill="#fff" font-family="'Plus Jakarta Sans', sans-serif" font-size="13" font-weight="bold" text-anchor="middle">2. EARN</text>

                            <g class="node-repeat">
                                <circle cx="421" cy="295" r="45" fill="#500724" stroke="#ec4899" stroke-width="4"/>
                                <text x="421" y="307" fill="#fbcfe8" font-size="28" text-anchor="middle">🚀</text>
                            </g>
                            <rect x="371" y="360" width="100" height="28" rx="14" fill="#ec4899"/>
                            <text x="421" y="379" fill="#fff" font-family="'Plus Jakarta Sans', sans-serif" font-size="13" font-weight="bold" text-anchor="middle">3. REPEAT</text>
                        </svg>
                    </div>
                </div>

            </div>
        </div>
    </section>
    
    
    
    <section id="live-gigs" class="py-24 bg-slate-50 relative border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center py-1 px-3 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-600 text-sm font-bold tracking-wide mb-4">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse mr-2"></span> Live Opportunities
                    </span>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-dark mb-4">Fresh Gigs, Posted Daily</h2>
                    <p class="text-lg text-slate-500">Get certified and unlock instant access to paid tasks and full-time roles.</p>
                </div>
                <a href="#" class="mt-6 md:mt-0 px-6 py-3 text-sm font-bold text-brand-600 bg-brand-50 border border-brand-200 rounded-full hover:bg-brand-100 transition-colors">
                    View All Gigs &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:shadow-soft transition-all duration-300 group cursor-pointer">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                            <i class="fab fa-react"></i>
                        </div>
                        <span class="text-xs font-bold px-3 py-1 bg-slate-100 text-slate-500 rounded-full">Freelance</span>
                    </div>
                    <h4 class="text-xl font-bold text-dark mb-2 group-hover:text-brand-600 transition-colors">Frontend React Fixes</h4>
                    <p class="text-slate-500 text-sm mb-6 line-clamp-2">Looking for a certified frontend dev to fix UI bugs on an e-commerce dashboard.</p>
                    <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                        <div class="font-black text-lg text-dark">₹2,500 <span class="text-xs text-slate-400 font-medium">/task</span></div>
                        <div class="text-xs font-bold text-brand-600 flex items-center">
                            <i class="fas fa-lock mr-1"></i> Requires Cert
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:shadow-soft transition-all duration-300 group cursor-pointer">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-xl">
                            <i class="fab fa-aws"></i>
                        </div>
                        <span class="text-xs font-bold px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full">Full-Time</span>
                    </div>
                    <h4 class="text-xl font-bold text-dark mb-2 group-hover:text-brand-600 transition-colors">Junior Cloud Architect</h4>
                    <p class="text-slate-500 text-sm mb-6 line-clamp-2">Join our fast-growing startup. Must pass the Risegen Advanced Cloud Assessment.</p>
                    <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                        <div class="font-black text-lg text-dark">₹6L - 8L <span class="text-xs text-slate-400 font-medium">/year</span></div>
                        <div class="text-xs font-bold text-brand-600 flex items-center">
                            <i class="fas fa-lock mr-1"></i> Requires Cert
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:shadow-soft transition-all duration-300 group cursor-pointer">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                            <i class="fas fa-database"></i>
                        </div>
                        <span class="text-xs font-bold px-3 py-1 bg-slate-100 text-slate-500 rounded-full">Contract</span>
                    </div>
                    <h4 class="text-xl font-bold text-dark mb-2 group-hover:text-brand-600 transition-colors">PHP/MySQL Scripting</h4>
                    <p class="text-slate-500 text-sm mb-6 line-clamp-2">Need a quick backend script to migrate inventory data into our new database.</p>
                    <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                        <div class="font-black text-lg text-dark">₹5,000 <span class="text-xs text-slate-400 font-medium">/project</span></div>
                        <div class="text-xs font-bold text-brand-600 flex items-center">
                            <i class="fas fa-lock mr-1"></i> Requires Cert
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-dark relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.05]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 24px 24px;"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col lg:flex-row items-center gap-16">
            
            <div class="lg:w-1/2">
                <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6">Rise to the top. <br><span class="text-brand-400">Get noticed faster.</span></h2>
                <p class="text-lg text-slate-400 mb-8 leading-relaxed">
                    Our real-time testing engine doesn't just score you—it ranks you. Top performers on the global leaderboard get their profiles highlighted directly to recruiters and hiring partners.
                </p>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-center text-slate-300 font-medium"><i class="fas fa-bolt text-yellow-400 mr-3"></i> Speed and accuracy multipliers</li>
                    <li class="flex items-center text-slate-300 font-medium"><i class="fas fa-globe text-blue-400 mr-3"></i> Real-time global ranking</li>
                    <li class="flex items-center text-slate-300 font-medium"><i class="fas fa-eye text-emerald-400 mr-3"></i> Top 10% get recruiter visibility</li>
                </ul>
                <a href="#game-box" class="px-8 py-4 text-base font-bold text-dark bg-white rounded-full shadow-glow hover:bg-slate-200 transition duration-300 inline-block">
                    Take a Practice Test
                </a>
            </div>

            <div class="lg:w-1/2 w-full">
                <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 shadow-2xl relative">
                    <div class="absolute -top-3 -right-3">
                        <span class="flex h-6 w-6">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-6 w-6 bg-emerald-500 border-2 border-slate-900"></span>
                        </span>
                    </div>
                    
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6 pb-4 border-b border-slate-800">Live Global Ranks</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-brand-900/40 border border-brand-500/30 rounded-2xl">
                            <div class="flex items-center space-x-4">
                                <div class="w-8 text-center font-black text-yellow-400 text-xl">1</div>
                                <img src="https://ui-avatars.com/api/?name=Arjun+K&background=fbbf24&color=000&rounded=true" alt="User" class="w-10 h-10 rounded-full border border-yellow-400/50">
                                <div>
                                    <div class="font-bold text-white">Arjun K.</div>
                                    <div class="text-xs text-brand-300">Grand Master</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-black text-white text-lg">9,420</div>
                                <div class="text-[10px] text-slate-400 uppercase">Points</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-slate-800/50 rounded-2xl hover:bg-slate-800 transition-colors cursor-pointer">
                            <div class="flex items-center space-x-4">
                                <div class="w-8 text-center font-bold text-slate-300 text-lg">2</div>
                                <img src="https://ui-avatars.com/api/?name=Neha+S&background=94a3b8&color=fff&rounded=true" alt="User" class="w-10 h-10 rounded-full">
                                <div>
                                    <div class="font-bold text-white">Neha S.</div>
                                    <div class="text-xs text-slate-400">Subject Specialist</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-white">8,150</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-slate-800/50 rounded-2xl hover:bg-slate-800 transition-colors cursor-pointer">
                            <div class="flex items-center space-x-4">
                                <div class="w-8 text-center font-bold text-orange-300 text-lg">3</div>
                                <img src="https://ui-avatars.com/api/?name=Vikram+D&background=fdba74&color=000&rounded=true" alt="User" class="w-10 h-10 rounded-full">
                                <div>
                                    <div class="font-bold text-white">Vikram D.</div>
                                    <div class="text-xs text-slate-400">Subject Specialist</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-white">7,890</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    
    <section id="pricing" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-extrabold text-dark mb-4">Simple, transparent pricing</h2>
                <p class="text-xl text-slate-500">Choose the path that accelerates your career.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div class="p-10 bg-slate-50 rounded-[2.5rem] border border-slate-200 flex flex-col hover:shadow-soft transition-all">
                    <h3 class="text-2xl font-bold text-dark mb-2">Starter</h3>
                    <p class="text-slate-500 mb-6 h-12">Essential tools to begin your learning journey.</p>
                    <div class="text-5xl font-black text-dark mb-8">₹0 <span class="text-lg font-medium text-slate-500">/forever</span></div>
                    
                    <ul class="space-y-4 mb-10 flex-grow">
                        <li class="flex items-center text-slate-700 font-medium"><i class="fas fa-check-circle text-brand-500 mr-3 text-lg"></i> 50 Core Test Features</li>
                        <li class="flex items-center text-slate-700 font-medium"><i class="fas fa-check-circle text-brand-500 mr-3 text-lg"></i> Basic Job Access</li>
                        <li class="flex items-center text-slate-700 font-medium"><i class="fas fa-check-circle text-brand-500 mr-3 text-lg"></i> Community Forum Support</li>
                    </ul>
                    
                    <a href="#" class="w-full py-4 px-6 text-center rounded-full font-bold bg-white text-dark border-2 border-slate-200 hover:border-dark transition">Start for free</a>
                </div>

                <div class="p-10 bg-dark rounded-[2.5rem] border border-slate-800 flex flex-col relative shadow-2xl transform md:-translate-y-4">
                    <div class="absolute -top-4 right-10 bg-brand-500 text-white text-xs font-bold px-4 py-2 rounded-full uppercase tracking-wider">Most Popular</div>
                    
                    <h3 class="text-2xl font-bold text-white mb-2">Credit Pack</h3>
                    <p class="text-slate-400 mb-6 h-12">Unlock premium mock tests and recruiter priority.</p>
                    <div class="text-5xl font-black text-white mb-8">₹299 <span class="text-lg font-medium text-slate-500">/pack</span></div>
                    
                    <ul class="space-y-4 mb-10 flex-grow">
                        <li class="flex items-center text-slate-300 font-medium"><i class="fas fa-check-circle text-emerald-400 mr-3 text-lg"></i> 100+ Premium Test Features</li>
                        <li class="flex items-center text-white font-bold"><i class="fas fa-check-circle text-emerald-400 mr-3 text-lg"></i> Priority Job Notifications</li>
                        <li class="flex items-center text-slate-300 font-medium"><i class="fas fa-check-circle text-emerald-400 mr-3 text-lg"></i> Advanced Skill Analytics</li>
                    </ul>
                    
                    <a href="#" class="w-full py-4 px-6 text-center rounded-full font-bold bg-brand-600 text-white hover:bg-brand-500 shadow-glow transition">Buy Credits Now</a>
                </div>
            </div>
        </div>
    </section>

    
    <section id="testimonials" class="py-24 bg-slate-50 relative overflow-hidden">
        <div class="bg-blob bg-brand-200 w-96 h-96 top-0 -left-32 opacity-30"></div>
        <div class="bg-blob bg-pink-200 w-96 h-96 bottom-0 -right-32 opacity-30"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <span class="inline-block py-1 px-3 rounded-full bg-white border border-slate-200 text-brand-600 text-sm font-bold tracking-wide mb-4 shadow-sm">
                    Wall of Love ❤️
                </span>
                <h2 class="text-4xl md:text-5xl font-extrabold text-dark mb-4">What our learners say</h2>
                <p class="text-xl text-slate-500 max-w-2xl mx-auto">Don't just take our word for it. Here's how Risegen is transforming careers.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-soft hover:-translate-y-2 hover:shadow-xl transition-all duration-300 flex flex-col h-full relative">
                    <i class="fas fa-quote-left text-4xl text-brand-100 absolute top-6 right-8"></i>
                    <div class="text-yellow-400 mb-6 flex text-sm space-x-1">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="text-slate-600 text-lg leading-relaxed mb-8 flex-grow">
                        "Risegen completely changed how I prepare for interviews. The Knowledge Rush feature builds actual reflexes, not just memorization. I landed my first tech role within two months."
                    </p>
                    <div class="flex items-center space-x-4">
                        <img src="https://ui-avatars.com/api/?name=Rahul+Sharma&background=4f46e5&color=fff&rounded=true&bold=true" alt="Rahul Sharma" class="w-12 h-12 rounded-full border-2 border-brand-100 shadow-sm">
                        <div>
                            <h4 class="font-bold text-dark">Rahul Sharma</h4>
                            <p class="text-sm font-semibold text-brand-600">Software Developer</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-soft hover:-translate-y-2 hover:shadow-xl transition-all duration-300 flex flex-col h-full relative">
                    <i class="fas fa-quote-left text-4xl text-brand-100 absolute top-6 right-8"></i>
                    <div class="text-yellow-400 mb-6 flex text-sm space-x-1">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="text-slate-600 text-lg leading-relaxed mb-8 flex-grow">
                        "The expert courses are incredibly affordable with the credit system. I was able to target my exact weak points in data analytics and fast-track my promotion."
                    </p>
                    <div class="flex items-center space-x-4">
                        <img src="https://ui-avatars.com/api/?name=Priya+Patel&background=ec4899&color=fff&rounded=true&bold=true" alt="Priya Patel" class="w-12 h-12 rounded-full border-2 border-pink-100 shadow-sm">
                        <div>
                            <h4 class="font-bold text-dark">Priya Patel</h4>
                            <p class="text-sm font-semibold text-brand-600">Data Analyst</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-soft hover:-translate-y-2 hover:shadow-xl transition-all duration-300 flex flex-col h-full relative">
                    <i class="fas fa-quote-left text-4xl text-brand-100 absolute top-6 right-8"></i>
                    <div class="text-yellow-400 mb-6 flex text-sm space-x-1">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="text-slate-600 text-lg leading-relaxed mb-8 flex-grow">
                        "The UI is gorgeous and the platform is so fast. The 50 free test features gave me enough room to realize how powerful this tool is before I bought a credit pack."
                    </p>
                    <div class="flex items-center space-x-4">
                        <img src="https://ui-avatars.com/api/?name=Amit+Kumar&background=0f172a&color=fff&rounded=true&bold=true" alt="Amit Kumar" class="w-12 h-12 rounded-full border-2 border-slate-200 shadow-sm">
                        <div>
                            <h4 class="font-bold text-dark">Amit Kumar</h4>
                            <p class="text-sm font-semibold text-brand-600">Marketing Executive</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    
    
   <footer class="bg-dark text-slate-300 py-16 md:py-20 border-t border-slate-800 relative overflow-hidden">
        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-3/4 h-24 bg-brand-600/10 blur-[100px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-12 mb-16">
                
                <div class="lg:col-span-2 space-y-6">
                    <a class="flex items-center space-x-3 text-2xl font-extrabold text-white tracking-tight" href="#home">
                        <div class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center shadow-glow">
                            <i class="fas fa-graduation-cap text-white text-xl"></i> 
                        </div>
                        <span>Risegen</span>
                    </a>
                    <p class="text-slate-400 text-sm leading-relaxed max-w-sm">
                        Empowering the next generation of tech talent. Master your skills, ace your interviews, and land your dream job with our advanced learning platform.
                    </p>
                    <div class="flex space-x-4 pt-2">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-500 hover:text-white transition-all duration-300 transform hover:-translate-y-1">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-500 hover:text-white transition-all duration-300 transform hover:-translate-y-1">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-500 hover:text-white transition-all duration-300 transform hover:-translate-y-1">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-brand-500 hover:text-white transition-all duration-300 transform hover:-translate-y-1">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6 tracking-wide text-sm uppercase">Platform</h4>
                    <ul class="space-y-4 text-sm font-medium">
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Free Tests</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Job Listings</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Course Catalog</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Instructors</a></li>
                        <li><a href="#pricing" class="text-brand-400 hover:text-brand-300 inline-block transition-colors duration-200 mt-2">Pricing & Credits &rarr;</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6 tracking-wide text-sm uppercase">Support</h4>
                    <ul class="space-y-4 text-sm font-medium">
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Help Center</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Contact Us</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">FAQ</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Community</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6 tracking-wide text-sm uppercase">Resources</h4>
                    <ul class="space-y-4 text-sm font-medium">
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Tech Blog</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">E-books</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Interview Prep</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Case Studies</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold mb-6 tracking-wide text-sm uppercase">Company</h4>
                    <ul class="space-y-4 text-sm font-medium">
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">About Us</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Careers</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Privacy Policy</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Terms of Service</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="text-slate-500 text-sm font-medium">
                    &copy; 2026 Risegen. All rights reserved.
                </div>
                <div class="flex space-x-6 text-sm font-medium text-slate-500">
                    <a href="#" class="hover:text-white transition-colors duration-200">Security</a>
                    <a href="#" class="hover:text-white transition-colors duration-200">Cookies</a>
                    <a href="admin_login.php" class="hover:text-brand-400 transition-colors duration-200">Admin Login</a>
                </div>
            </div>
        </div>
    </footer>
    <script>
        // Navbar Scrolled State
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });

        // Mobile Menu
        const mobileMenuBtn = document.getElementById('mobile-menu-button');
        mobileMenuBtn.addEventListener('click', () => {
            // Implement mobile menu slide-out logic here
            alert('Mobile menu toggled');
        });

        // Game Logic Data
        const QUIZ_DATA = [
            { "q": "What is the capital city of France?", "a": "Paris", "d": "Easy", "points": 10 },
            { "q": "What is the square root of 64?", "a": "8", "d": "Easy", "points": 10 },
            { "q": "Primary function of the CPU?", "a": "Process Data", "d": "Medium", "points": 20 },
            { "q": "Python keyword to define a function?", "a": "def", "d": "Medium", "points": 20 }
        ];

        let currentScore = 0;
        let currentQuestionIndex = 0;
        let shuffledQuestions = [];
        let timerInterval;
        const TIME_LIMIT_PER_Q = 10;
        let timeLeft = TIME_LIMIT_PER_Q;

        // Game Elements
        const quizArea = document.getElementById('quiz-area');
        const feedbackEl = document.getElementById('feedback');
        const scoreEl = document.getElementById('score');
        const qPointsEl = document.getElementById('q-points');
        const qDifficultyEl = document.getElementById('q-difficulty');
        const qProgressTextEl = document.getElementById('q-progress-text');

        function startGame() {
            currentScore = 0;
            currentQuestionIndex = 0;
            shuffledQuestions = [...QUIZ_DATA].sort(() => Math.random() - 0.5);
            updateScoreboard();
            loadQuestion();
        }

        function updateScoreboard() {
            scoreEl.textContent = currentScore;
            qProgressTextEl.textContent = `${currentQuestionIndex} / ${shuffledQuestions.length}`;
        }

        function getDifficultyColorClass(d) {
            return d === 'Easy' ? 'text-emerald-400' : d === 'Medium' ? 'text-yellow-400' : 'text-rose-400';
        }

        function loadQuestion() {
            if (currentQuestionIndex >= shuffledQuestions.length) return endGame();

            const qData = shuffledQuestions[currentQuestionIndex];
            
            qPointsEl.textContent = `${qData.points} pts`;
            qDifficultyEl.textContent = qData.d;
            qDifficultyEl.className = `text-2xl font-bold ${getDifficultyColorClass(qData.d)}`;
            feedbackEl.classList.add('hidden');

            quizArea.innerHTML = `
                <div class="w-full bg-slate-800 rounded-full h-1.5 mb-6 overflow-hidden">
                    <div id="timer-bar" class="bg-brand-500 h-1.5 rounded-full transition-all duration-100 w-full"></div>
                </div>
                <h3 class="text-2xl md:text-3xl font-extrabold text-white mb-6 leading-tight">${qData.q}</h3>
                <input type="text" id="answer-input" placeholder="Type answer & press Enter..." 
                       class="w-full p-5 bg-slate-800/50 border border-slate-700 rounded-2xl text-xl text-white focus:outline-none focus:ring-2 focus:ring-brand-500 mb-4 placeholder-slate-500 transition shadow-inner" autocomplete="off" />
                <button id="submit-button" class="w-full bg-white text-dark py-4 rounded-2xl font-bold text-lg hover:bg-slate-200 transition duration-200 shadow-lg">
                    Submit Answer
                </button>
            `;

            const inputEl = document.getElementById('answer-input');
            const submitBtn = document.getElementById('submit-button');

            submitBtn.addEventListener('click', checkAnswer);
            inputEl.addEventListener('keypress', (e) => { if(e.key === 'Enter') checkAnswer(); });
            
            inputEl.focus();
            startTimer();
            updateScoreboard();
        }

        function startTimer() {
            timeLeft = TIME_LIMIT_PER_Q;
            const timerBarEl = document.getElementById('timer-bar');
            
            timerInterval = setInterval(() => {
                timeLeft -= 0.1;
                const percentage = (timeLeft / TIME_LIMIT_PER_Q) * 100;
                timerBarEl.style.width = `${percentage}%`;

                if (timeLeft < 3) timerBarEl.classList.replace('bg-brand-500', 'bg-rose-500');

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    checkAnswer(); 
                }
            }, 100);
        }

        function checkAnswer() {
            clearInterval(timerInterval);
            const inputEl = document.getElementById('answer-input');
            const submitBtn = document.getElementById('submit-button');
            const userAnswer = inputEl.value.trim();
            const qData = shuffledQuestions[currentQuestionIndex];
            
            inputEl.disabled = true;
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50');

            const timeSpent = TIME_LIMIT_PER_Q - timeLeft;
            let isCorrect = userAnswer.toLowerCase() === qData.a.toLowerCase();
            
            if (isCorrect) {
                let speedBonus = timeSpent <= 3 ? 15 : 0;
                let earned = qData.points + speedBonus;
                currentScore += earned;
                feedbackEl.className = 'mt-6 p-5 rounded-2xl font-bold text-lg border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 block';
                feedbackEl.innerHTML = `🔥 Correct! +${earned} pts ${speedBonus ? '(Speed Bonus!)' : ''}`;
            } else {
                currentScore -= 5;
                feedbackEl.className = 'mt-6 p-5 rounded-2xl font-bold text-lg border border-rose-500/30 bg-rose-500/10 text-rose-400 block';
                feedbackEl.innerHTML = `❌ Incorrect. Answer: ${qData.a}`;
            }

            currentQuestionIndex++;
            updateScoreboard();
            setTimeout(loadQuestion, 2000);
        }

        function endGame() {
            quizArea.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-24 h-24 bg-yellow-400/20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-trophy text-4xl text-yellow-400"></i>
                    </div>
                    <h3 class="text-4xl font-extrabold text-white mb-2">Challenge Complete</h3>
                    <p class="text-xl text-slate-400 mb-8">Final Score: <span class="text-brand-400 font-black">${currentScore}</span></p>
                    <button onclick="startGame()" class="bg-brand-600 hover:bg-brand-500 text-white px-10 py-4 rounded-full font-bold text-lg shadow-glow transition transform hover:scale-105">
                        Play Again
                    </button>
                </div>
            `;
            qPointsEl.textContent = '--';
            qDifficultyEl.textContent = '--';
            feedbackEl.classList.add('hidden');
        }

        document.getElementById('start-button')?.addEventListener('click', startGame);
    </script>
</body>
</html>