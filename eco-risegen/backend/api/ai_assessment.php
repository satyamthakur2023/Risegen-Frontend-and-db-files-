<?php
session_start();
require_once 'config.php';

// --- PDO Connection ---
$pdo = connectDatabase();

// --- Get User Info ---
$user_name = 'Guest';
$user_initial = 'G';
$user_id = null;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $user_name = $user['username'];
        $user_initial = strtoupper(substr($user_name, 0, 1));
        $user_id = $_SESSION['user_id'];
    }
}

// --- Handle AJAX Requests ---
if (isset($_GET['action'])) {
    header('Content-Type: application/json');

    // 1️⃣ Generate Questions
    if ($_GET['action'] === 'generate_questions') {
        $subject = $_GET['subject'] ?? 'General Knowledge';
        $difficulty = $_GET['difficulty'] ?? 'medium';
        $count = (int)($_GET['count'] ?? 50);

        $questions = [];
        $templates = [
            "What is the main concept of {subject}?",
            "Which principle is fundamental to {subject}?",
            "What defines {subject} in its core aspects?",
            "Which element is most important in {subject}?",
            "What characterizes the study of {subject}?",
            "Which factor influences {subject} the most?",
            "What is the primary focus of {subject}?",
            "Which aspect distinguishes {subject}?",
            "What represents the essence of {subject}?",
            "Which component is vital to {subject}?"
        ];

        for ($i = 0; $i < $count; $i++) {
            $template = $templates[$i % count($templates)];
            $question_text = str_replace('{subject}', $subject, $template);

            $options = [];
            if ($difficulty === 'easy') {
                $options = [
                    "Basic understanding of $subject",
                    "Simple concept in $subject",
                    "Elementary principle of $subject",
                    "Fundamental idea of $subject"
                ];
            } elseif ($difficulty === 'hard') {
                $options = [
                    "Advanced theoretical framework of $subject",
                    "Complex analytical approach to $subject",
                    "Sophisticated methodology in $subject",
                    "Comprehensive understanding of $subject"
                ];
            } else {
                $options = [
                    "Core principle of $subject",
                    "Key concept in $subject",
                    "Important aspect of $subject",
                    "Main element of $subject"
                ];
            }

            shuffle($options);

            $questions[] = [
                'id' => uniqid() . $i,
                'type' => 'multiple_choice',
                'question' => $question_text,
                'options' => $options,
                'correct_answer' => 0,
                'subject' => $subject,
                'difficulty' => $difficulty
            ];
        }

        echo json_encode(['success' => true, 'questions' => $questions]);
        exit;
    }

    // 2️⃣ Submit Test
    if ($_GET['action'] === 'submit_test') {
        $data = json_decode(file_get_contents('php://input'), true);
        $answers = $data['answers'] ?? [];
        $questions = $data['questions'] ?? [];

        $correct = 0;
        $total = count($questions);

        foreach ($questions as $i => $question) {
            if (isset($answers[$i]) && $answers[$i] == $question['correct_answer']) {
                $correct++;
            }
        }

        $percentage = round(($correct / $total) * 100);
        $passed = $percentage >= 65;

        // --- Save to Database ---
        if ($user_id) {
            try {
                $stmt = $pdo->prepare("
                    CREATE TABLE IF NOT EXISTS assessment_results (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT,
                        subject VARCHAR(100),
                        total_questions INT,
                        correct_answers INT,
                        percentage DECIMAL(5,2),
                        passed BOOLEAN,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ");
                $stmt->execute();

                $stmt = $pdo->prepare("
                    INSERT INTO assessment_results
                    (user_id, subject, total_questions, correct_answers, percentage, passed, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$user_id, $data['subject'], $total, $correct, $percentage, $passed ? 1 : 0]);
            } catch (PDOException $e) {
                error_log("Saving assessment failed: " . $e->getMessage());
            }
        }

        echo json_encode([
            'success' => true,
            'results' => [
                'correct' => $correct,
                'total' => $total,
                'percentage' => $percentage,
                'passed' => $passed,
                'grade' => $passed ? 'PASS' : 'FAIL'
            ]
        ]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Assessment - RiseGen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .loading { animation: pulse 1.5s infinite; }
    </style>
</head>
<body class="gradient-bg min-h-screen">

<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-white mb-4">🤖 AI Assessment</h1>
        <p class="text-white/80 text-lg">Generate questions about any topic</p>
        <div class="mt-4 text-white/60">Welcome, <?= htmlspecialchars($user_name) ?> | Pass Rate: 65%</div>
    </div>

    <!-- Setup Screen -->
    <div id="setup-screen" class="max-w-2xl mx-auto bg-white/95 backdrop-blur rounded-2xl p-8 shadow-2xl">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Start Assessment</h2>
        
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">Enter Topic</label>
                <input type="text" id="custom-topic" placeholder="Enter any topic: Programming, Science, History, etc." 
                       class="w-full p-4 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
                <div class="text-sm text-gray-500 mt-2">AI will generate questions about your topic</div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">Difficulty</label>
                <div class="grid grid-cols-3 gap-3">
                    <button class="difficulty-btn p-3 border-2 border-gray-300 rounded-lg hover:border-green-500" data-level="easy">
                        <div class="text-green-600 font-semibold">Easy</div>
                    </button>
                    <button class="difficulty-btn p-3 border-2 border-blue-500 bg-blue-50 rounded-lg" data-level="medium">
                        <div class="text-blue-600 font-semibold">Medium</div>
                    </button>
                    <button class="difficulty-btn p-3 border-2 border-gray-300 rounded-lg hover:border-red-500" data-level="hard">
                        <div class="text-red-600 font-semibold">Hard</div>
                    </button>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">Questions</label>
                <input type="range" id="question-count" min="50" max="75" value="60" class="w-full">
                <div class="flex justify-between text-sm text-gray-500 mt-1">
                    <span>50</span>
                    <span id="count-display" class="font-bold text-blue-600">60</span>
                    <span>75</span>
                </div>
            </div>
            
            <button id="start-assessment" class="w-full bg-blue-600 text-white py-4 px-6 rounded-lg font-bold text-lg hover:bg-blue-700">
                Generate Assessment
            </button>
        </div>
    </div>

    <!-- Loading Screen -->
    <div id="loading-screen" class="hidden max-w-2xl mx-auto bg-white/95 backdrop-blur rounded-2xl p-8 shadow-2xl text-center">
        <div class="loading bg-blue-100 w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center">
            <div class="text-2xl">🤖</div>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Generating Questions...</h3>
        <p class="text-gray-600" id="loading-text">Creating your assessment</p>
    </div>

    <!-- Test Screen -->
    <div id="test-screen" class="hidden max-w-4xl mx-auto bg-white/95 backdrop-blur rounded-2xl p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Assessment</h2>
            <div class="text-right">
                <div class="text-lg font-semibold">Question <span id="current-q">1</span> of <span id="total-q">60</span></div>
                <div class="w-64 bg-gray-200 rounded-full h-2 mt-1">
                    <div id="progress-bar" class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                </div>
            </div>
        </div>
        
        <div id="question-container" class="mb-8"></div>
        
        <div class="flex justify-between">
            <button id="prev-btn" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50" disabled>← Previous</button>
            <button id="next-btn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Next →</button>
            <button id="submit-btn" class="hidden px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Submit</button>
        </div>
    </div>

    <!-- Results Screen -->
    <div id="results-screen" class="hidden max-w-2xl mx-auto bg-white/95 backdrop-blur rounded-2xl p-8 shadow-2xl text-center">
        <div id="results-content"></div>
        <div class="mt-8 space-y-4">
            <button onclick="location.reload()" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold">Take Another</button>
            <a href="welcome.php" class="block w-full bg-gray-600 text-white py-3 px-6 rounded-lg font-semibold">Dashboard</a>
        </div>
    </div>
</div>

<script>
let currentQuestions = [];
let currentAnswers = [];
let currentQuestionIndex = 0;
let selectedDifficulty = 'medium';
let questionCount = 60;

document.addEventListener('DOMContentLoaded', function() {
    // Difficulty selection
    document.querySelectorAll('.difficulty-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.difficulty-btn').forEach(b => {
                b.classList.remove('border-blue-500', 'bg-blue-50', 'border-green-500', 'bg-green-50', 'border-red-500', 'bg-red-50');
                b.classList.add('border-gray-300');
            });
            
            const level = this.dataset.level;
            if (level === 'easy') {
                this.classList.add('border-green-500', 'bg-green-50');
            } else if (level === 'hard') {
                this.classList.add('border-red-500', 'bg-red-50');
            } else {
                this.classList.add('border-blue-500', 'bg-blue-50');
            }
            selectedDifficulty = level;
        });
    });
    
    // Question count
    document.getElementById('question-count').addEventListener('input', function() {
        questionCount = this.value;
        document.getElementById('count-display').textContent = this.value;
    });
    
    // Start assessment
    document.getElementById('start-assessment').addEventListener('click', startAssessment);
    
    // Navigation
    document.getElementById('prev-btn').addEventListener('click', previousQuestion);
    document.getElementById('next-btn').addEventListener('click', nextQuestion);
    document.getElementById('submit-btn').addEventListener('click', submitTest);
});

async function startAssessment() {
    const topic = document.getElementById('custom-topic').value.trim();
    
    if (!topic || topic.length < 2) {
        alert('Please enter a topic');
        return;
    }
    
    showScreen('loading-screen');
    document.getElementById('loading-text').textContent = `Generating ${questionCount} questions about "${topic}"`;
    
    try {
        const response = await fetch(`?action=generate_questions&subject=${encodeURIComponent(topic)}&difficulty=${selectedDifficulty}&count=${questionCount}`);
        const data = await response.json();
        
        if (data.success) {
            currentQuestions = data.questions;
            currentAnswers = new Array(currentQuestions.length).fill(null);
            currentQuestionIndex = 0;
            
            document.getElementById('total-q').textContent = currentQuestions.length;
            
            setTimeout(() => {
                showScreen('test-screen');
                displayQuestion();
            }, 1500);
        } else {
            alert('Error generating questions');
            showScreen('setup-screen');
        }
    } catch (error) {
        alert('Error generating questions');
        showScreen('setup-screen');
    }
}

function displayQuestion() {
    const question = currentQuestions[currentQuestionIndex];
    const container = document.getElementById('question-container');
    
    document.getElementById('current-q').textContent = currentQuestionIndex + 1;
    const progress = ((currentQuestionIndex + 1) / currentQuestions.length) * 100;
    document.getElementById('progress-bar').style.width = progress + '%';
    
    let optionsHtml = '';
    question.options.forEach((option, index) => {
        const isSelected = currentAnswers[currentQuestionIndex] === index;
        optionsHtml += `
            <div class="option p-4 border-2 ${isSelected ? 'border-blue-500 bg-blue-50' : 'border-gray-200'} rounded-lg cursor-pointer hover:border-blue-300 mb-3" onclick="selectAnswer(${index})">
                <div class="flex items-center">
                    <div class="w-5 h-5 rounded-full border-2 ${isSelected ? 'border-blue-500 bg-blue-500' : 'border-gray-300'} mr-3"></div>
                    <span>${String.fromCharCode(65 + index)}. ${option}</span>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = `
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="text-xl font-semibold text-gray-800">${question.question}</h3>
        </div>
        <div>${optionsHtml}</div>
    `;
    
    document.getElementById('prev-btn').disabled = currentQuestionIndex === 0;
    
    if (currentQuestionIndex === currentQuestions.length - 1) {
        document.getElementById('next-btn').classList.add('hidden');
        document.getElementById('submit-btn').classList.remove('hidden');
    } else {
        document.getElementById('next-btn').classList.remove('hidden');
        document.getElementById('submit-btn').classList.add('hidden');
    }
}

function selectAnswer(answerIndex) {
    currentAnswers[currentQuestionIndex] = answerIndex;
    displayQuestion();
}

function previousQuestion() {
    if (currentQuestionIndex > 0) {
        currentQuestionIndex--;
        displayQuestion();
    }
}

function nextQuestion() {
    if (currentQuestionIndex < currentQuestions.length - 1) {
        currentQuestionIndex++;
        displayQuestion();
    }
}

async function submitTest() {
    showScreen('loading-screen');
    document.getElementById('loading-text').textContent = 'Processing results...';
    
    try {
        const response = await fetch('?action=submit_test', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                answers: currentAnswers,
                questions: currentQuestions,
                subject: document.getElementById('custom-topic').value
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            setTimeout(() => {
                displayResults(data.results);
                showScreen('results-screen');
            }, 1000);
        }
    } catch (error) {
        alert('Error submitting test');
    }
}

function displayResults(results) {
    const passed = results.passed;
    const container = document.getElementById('results-content');
    
    container.innerHTML = `
        <div class="text-6xl mb-4">${passed ? '🎉' : '📚'}</div>
        <h2 class="text-3xl font-bold ${passed ? 'text-green-600' : 'text-red-600'} mb-2">${results.grade}</h2>
        <div class="text-4xl font-bold text-gray-800 mb-4">${results.percentage}%</div>
        <div class="text-lg text-gray-600 mb-6">
            ${results.correct} out of ${results.total} questions correct
        </div>
        <div class="p-4 ${passed ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'} border rounded-lg">
            <div class="${passed ? 'text-green-800' : 'text-red-800'} font-semibold">
                ${passed ? 'Congratulations! You passed!' : 'Keep studying and try again!'}
            </div>
        </div>
    `;
}

function showScreen(screenId) {
    document.querySelectorAll('[id$="-screen"]').forEach(screen => {
        screen.classList.add('hidden');
    });
    document.getElementById(screenId).classList.remove('hidden');
}
</script>

</body>
</html>