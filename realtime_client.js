// Real-time database client for MCQ Generator
class MCQRealTimeDB {
    constructor(apiUrl = 'realtime_api.php') {
        this.apiUrl = apiUrl;
    }

    // Upload PDF data
    async uploadPDF(userId, filename, filePath) {
        const response = await fetch(`${this.apiUrl}?action=upload_pdf`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({user_id: userId, filename, file_path: filePath})
        });
        return await response.json();
    }

    // Fetch topics in real-time
    async getTopics(pdfId) {
        const response = await fetch(`${this.apiUrl}?action=get_topics&pdf_id=${pdfId}`);
        return await response.json();
    }

    // Fetch questions by topic
    async getQuestions(topicId, limit = 10) {
        const response = await fetch(`${this.apiUrl}?action=get_questions&topic_id=${topicId}&limit=${limit}`);
        return await response.json();
    }

    // Save user answer in real-time
    async saveAnswer(sessionId, questionId, userAnswer, isCorrect) {
        const response = await fetch(`${this.apiUrl}?action=save_answer`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({session_id: sessionId, question_id: questionId, user_answer: userAnswer, is_correct: isCorrect})
        });
        return await response.json();
    }

    // Get live score updates
    async getLiveScore(sessionId) {
        const response = await fetch(`${this.apiUrl}?action=live_score&session_id=${sessionId}`);
        return await response.json();
    }

    // Real-time score polling
    startScorePolling(sessionId, callback, interval = 2000) {
        return setInterval(async () => {
            const score = await this.getLiveScore(sessionId);
            callback(score);
        }, interval);
    }
}

// Usage example
const db = new MCQRealTimeDB();

// Real-time operations
async function handleAnswerSubmit(sessionId, questionId, answer, correctAnswer) {
    const isCorrect = answer === correctAnswer;
    await db.saveAnswer(sessionId, questionId, answer, isCorrect);
    
    // Get updated score
    const score = await db.getLiveScore(sessionId);
    updateScoreDisplay(score);
}

function updateScoreDisplay(score) {
    document.getElementById('score').textContent = `${score.correct}/${score.total}`;
}