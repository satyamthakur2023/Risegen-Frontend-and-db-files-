from flask import Flask, request, jsonify
from flask_cors import CORS
import PyPDF2
import nltk
import random
import re
import math
from collections import Counter
from db_connection import MCQDatabase

app = Flask(__name__)
CORS(app)

db = MCQDatabase()
pdf_texts = {}

try:
    nltk.download('punkt', quiet=True)
    nltk.download('stopwords', quiet=True)
    nltk.download('averaged_perceptron_tagger', quiet=True)
    nltk.download('punkt_tab', quiet=True)
except:
    pass

from nltk.corpus import stopwords
from nltk.tokenize import sent_tokenize, word_tokenize
from nltk.tag import pos_tag


class AdvancedMCQGenerator:
    def __init__(self):
        self.stop_words = set(stopwords.words('english'))
        self.topic_keywords = {
            'Technology': ['software', 'computer', 'algorithm', 'programming', 'code', 'system', 'digital', 'technology', 'data', 'network', 'internet', 'hardware'],
            'Science': ['research', 'experiment', 'hypothesis', 'theory', 'scientific', 'study', 'analysis', 'method', 'biology', 'chemistry', 'physics', 'laboratory'],
            'Business': ['management', 'strategy', 'market', 'business', 'company', 'profit', 'revenue', 'customer', 'organization', 'enterprise', 'industry'],
            'Education': ['learning', 'student', 'teacher', 'education', 'knowledge', 'skill', 'training', 'course', 'university', 'school', 'curriculum'],
            'Health': ['health', 'medical', 'patient', 'treatment', 'disease', 'medicine', 'therapy', 'clinical', 'hospital', 'diagnosis', 'symptoms'],
            'Finance': ['financial', 'money', 'investment', 'bank', 'economic', 'cost', 'budget', 'finance', 'capital', 'asset', 'portfolio', 'stock'],
            'Engineering': ['engineering', 'design', 'construction', 'technical', 'mechanical', 'electrical', 'civil', 'structure', 'material', 'process'],
            'Mathematics': ['mathematical', 'equation', 'formula', 'calculation', 'number', 'statistics', 'probability', 'function', 'variable', 'theorem']
        }

    def extract_text_from_pdf(self, pdf_file):
        try:
            pdf_reader = PyPDF2.PdfReader(pdf_file)
            text = ""
            for page in pdf_reader.pages:
                extracted = page.extract_text()
                if extracted:
                    text += extracted + "\n"
            return text.strip()
        except Exception as e:
            return f"Error extracting text: {str(e)}"

    def clean_sentence(self, sentence):
        sentence = re.sub(r'\s+', ' ', sentence).strip()
        sentence = re.sub(r'[^\w\s.,;:!?\'"-]', '', sentence)
        return sentence

    def is_good_sentence(self, sentence):
        words = word_tokenize(sentence)
        real_words = [w for w in words if w.isalpha() and len(w) > 2]
        return 10 <= len(real_words) <= 50 and len(sentence) >= 40

    # ── TF-IDF Extractive Summarizer ──────────────────────────────────────────

    def compute_tfidf(self, sentences):
        """Compute TF-IDF scores for each sentence"""
        tokenized = []
        for s in sentences:
            words = [w.lower() for w in word_tokenize(s) if w.isalpha() and w.lower() not in self.stop_words]
            tokenized.append(words)

        # Term frequency per sentence
        tf = [Counter(words) for words in tokenized]

        # Document frequency across sentences
        df = Counter()
        for words in tokenized:
            for word in set(words):
                df[word] += 1

        n = len(sentences)
        scores = []
        for i, words in enumerate(tokenized):
            if not words:
                scores.append(0)
                continue
            score = sum(
                (tf[i][w] / len(words)) * math.log((n + 1) / (df[w] + 1))
                for w in words
            )
            scores.append(score / len(words))
        return scores

    def summarize(self, text, num_sentences=6):
        """Extractive summarization using TF-IDF sentence scoring"""
        raw_sentences = sent_tokenize(text)
        sentences = [self.clean_sentence(s) for s in raw_sentences]
        good = [(i, s) for i, s in enumerate(sentences) if self.is_good_sentence(s)]

        if len(good) <= num_sentences:
            return ' '.join(s for _, s in good)

        indices, clean_sents = zip(*good)
        scores = self.compute_tfidf(list(clean_sents))

        # Pick top sentences, preserve original order
        ranked = sorted(range(len(scores)), key=lambda i: scores[i], reverse=True)[:num_sentences]
        ranked_sorted = sorted(ranked)
        summary = ' '.join(clean_sents[i] for i in ranked_sorted)
        return summary

    def extract_key_terms(self, text, top_n=10):
        """Extract top key terms using TF-IDF on words"""
        words = [w.lower() for w in word_tokenize(text)
                 if w.isalpha() and w.lower() not in self.stop_words and len(w) > 3]
        freq = Counter(words)
        return [{'term': w, 'count': c} for w, c in freq.most_common(top_n)]

    def detect_topics(self, text):
        text_lower = text.lower()
        word_count = max(len(text.split()), 1)
        topic_scores = {}
        for topic, keywords in self.topic_keywords.items():
            score = sum(text_lower.count(k) for k in keywords)
            if score > 0:
                topic_scores[topic] = min(score / word_count * 1000, 1.0)
        sorted_topics = sorted(topic_scores.items(), key=lambda x: x[1], reverse=True)
        return [{'name': t, 'score': round(s, 2)} for t, s in sorted_topics[:8]]

    # ── Question Generation ───────────────────────────────────────────────────

    def get_candidate_sentences(self, text, topics):
        raw = sent_tokenize(text)
        sentences = [self.clean_sentence(s) for s in raw]
        sentences = [s for s in sentences if self.is_good_sentence(s)]

        if topics:
            filtered = []
            for s in sentences:
                sl = s.lower()
                if any(any(kw in sl for kw in self.topic_keywords.get(t, [])) for t in topics):
                    filtered.append(s)
            if filtered:
                sentences = filtered

        random.shuffle(sentences)
        return sentences

    def generate_questions(self, text, topics, difficulty='medium', count=20):
        sentences = self.get_candidate_sentences(text, topics)
        all_words = [w.lower() for w in word_tokenize(text)
                     if w.isalpha() and w.lower() not in self.stop_words and len(w) > 3]
        word_freq = Counter(all_words)

        questions = []
        used_sentences = set()
        attempts = 0
        q_types = ['fill_blank', 'true_false', 'definition', 'comprehension']

        while len(questions) < count and attempts < count * 4:
            attempts += 1
            if not sentences:
                break
            sentence = sentences[attempts % len(sentences)]
            if sentence in used_sentences:
                continue

            q_type = q_types[len(questions) % len(q_types)]
            if q_type == 'fill_blank':
                q = self.generate_fill_blank(sentence, difficulty, word_freq)
            elif q_type == 'true_false':
                q = self.generate_true_false(sentence, difficulty)
            elif q_type == 'definition':
                q = self.generate_definition(sentence, difficulty)
            else:
                q = self.generate_comprehension(sentence, difficulty)

            if q:
                questions.append(q)
                used_sentences.add(sentence)

        return questions[:count]

    def get_distractors_from_freq(self, target, word_freq, n=3):
        """Pick distractors that are common words but not the target"""
        candidates = [w for w, _ in word_freq.most_common(60)
                      if w != target.lower() and w not in self.stop_words and len(w) > 3]
        random.shuffle(candidates)
        return candidates[:n]

    def generate_fill_blank(self, sentence, difficulty, word_freq):
        words = word_tokenize(sentence)
        tagged = pos_tag(words)

        pos_filter = ['NN', 'NNS', 'NNP', 'NNPS', 'VB', 'VBD', 'VBN', 'JJ']
        if difficulty == 'easy':
            pos_filter = ['NN', 'NNS', 'NNP']

        candidates = [w for w, pos in tagged
                      if pos in pos_filter and w.isalpha()
                      and w.lower() not in self.stop_words and len(w) > 3]
        if not candidates:
            return None

        target = random.choice(candidates)
        blanked = sentence.replace(target, '______', 1)
        distractors = self.get_distractors_from_freq(target, word_freq)

        if len(distractors) < 3:
            return None

        options = [target] + distractors[:3]
        random.shuffle(options)

        return {
            'question': f"Fill in the blank: {blanked}",
            'options': options,
            'correct': options.index(target),
            'type': 'fill_blank',
            'difficulty': difficulty
        }

    def generate_true_false(self, sentence, difficulty):
        """Generate true/false by optionally negating or swapping a key word"""
        words = word_tokenize(sentence)
        tagged = pos_tag(words)
        nouns = [w for w, pos in tagged if pos in ['NN', 'NNS', 'NNP'] and len(w) > 3]

        is_true = random.choice([True, False])

        if is_true or not nouns:
            question_text = sentence
            correct_label = 'True'
        else:
            # Swap a noun with a generic wrong word to make it false
            target = random.choice(nouns)
            replacements = ['system', 'process', 'method', 'concept', 'factor']
            replacement = random.choice([r for r in replacements if r != target.lower()])
            question_text = sentence.replace(target, replacement, 1)
            correct_label = 'False'

        options = ['True', 'False']
        return {
            'question': f"True or False: {question_text}",
            'options': options,
            'correct': options.index(correct_label),
            'type': 'true_false',
            'difficulty': difficulty
        }

    def generate_definition(self, sentence, difficulty):
        words = word_tokenize(sentence)
        tagged = pos_tag(words)
        nouns = [w for w, pos in tagged
                 if pos in ['NN', 'NNS', 'NNP', 'NNPS']
                 and w.isalpha() and len(w) > 4 and w.lower() not in self.stop_words]
        if not nouns:
            return None

        target = random.choice(nouns)
        # Build a contextual definition from surrounding words
        idx = [i for i, (w, _) in enumerate(tagged) if w == target]
        if not idx:
            return None
        i = idx[0]
        context_words = [w for w, pos in tagged[max(0, i-3):i+4]
                         if w.isalpha() and w != target and w.lower() not in self.stop_words]
        if context_words:
            correct = f"Related to {' and '.join(context_words[:2]).lower()} in this context"
        else:
            correct = f"A key concept discussed in the passage"

        distractors = [
            "An unrelated technical term not mentioned in the text",
            "A general term with no specific relevance here",
            "A concept from a completely different domain"
        ]
        options = [correct] + distractors
        random.shuffle(options)

        return {
            'question': f"What best describes '{target}' in the following context?\n\nContext: {sentence}",
            'options': options,
            'correct': options.index(correct),
            'type': 'definition',
            'difficulty': difficulty
        }

    def generate_comprehension(self, sentence, difficulty):
        starters = [
            "What is the main idea conveyed in the following statement?",
            "Which of the following best summarizes the given statement?",
            "According to the passage, what does the following statement indicate?",
            "What conclusion can be drawn from the following?"
        ]
        words = word_tokenize(sentence)
        tagged = pos_tag(words)
        nouns = [w for w, pos in tagged if pos in ['NN', 'NNS', 'NNP']
                 and w.isalpha() and w.lower() not in self.stop_words and len(w) > 3]

        if not nouns:
            return None

        key = random.choice(nouns).lower()
        correct = f"It discusses the concept of '{key}' and its significance"
        distractors = [
            f"It argues that '{key}' is irrelevant to the topic",
            f"It provides a counter-argument against '{key}'",
            f"It introduces an unrelated idea about a different subject"
        ]
        options = [correct] + distractors
        random.shuffle(options)

        return {
            'question': f"{random.choice(starters)}\n\nContext: {sentence}",
            'options': options,
            'correct': options.index(correct),
            'type': 'comprehension',
            'difficulty': difficulty
        }


mcq_generator = AdvancedMCQGenerator()


@app.route('/')
def index():
    try:
        with open('advanced-mcq-generator-v2.html', 'r', encoding='utf-8') as f:
            return f.read()
    except FileNotFoundError:
        return '<h1>MCQ Generator</h1><p>HTML file not found</p>'


@app.route('/api/upload', methods=['POST'])
def upload_pdf():
    try:
        if 'pdf' not in request.files:
            return jsonify({'error': 'No PDF file provided'}), 400
        pdf_file = request.files['pdf']
        if pdf_file.filename == '':
            return jsonify({'error': 'No file selected'}), 400

        text = mcq_generator.extract_text_from_pdf(pdf_file)
        if text.startswith('Error'):
            return jsonify({'error': text}), 400

        pdf_id = db.insert_pdf_upload(1, pdf_file.filename, f"uploads/{pdf_file.filename}")
        pdf_texts[pdf_id] = text

        topics = mcq_generator.detect_topics(text)
        db.insert_topics(pdf_id, topics)

        summary = mcq_generator.summarize(text, num_sentences=6)
        key_terms = mcq_generator.extract_key_terms(text, top_n=10)
        word_count = len(text.split())
        reading_time = max(1, round(word_count / 200))

        return jsonify({
            'success': True,
            'pdf_id': pdf_id,
            'text_length': len(text),
            'word_count': word_count,
            'reading_time': reading_time,
            'topics': topics,
            'summary': summary,
            'key_terms': key_terms
        })
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/generate', methods=['POST'])
def generate_questions():
    try:
        data = request.json
        pdf_id = data.get('pdf_id')
        selected_topics = data.get('topics', [])
        difficulty = data.get('difficulty', 'medium')
        count = data.get('count', 20)

        text = pdf_texts.get(pdf_id, "")
        if not text:
            return jsonify({'error': 'PDF text not found. Please upload again.'}), 400

        questions = mcq_generator.generate_questions(text, selected_topics, difficulty, count)

        for i, question in enumerate(questions):
            topic_id = i % max(len(selected_topics), 1) + 1
            db.insert_questions(pdf_id, topic_id, [question])

        return jsonify({'success': True, 'questions': questions, 'count': len(questions)})
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/session', methods=['POST'])
def create_session():
    try:
        data = request.json
        session_id = db.create_test_session(data.get('user_id', 1), data.get('pdf_id'), data.get('total_questions'))
        return jsonify({'success': True, 'session_id': session_id})
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/answer', methods=['POST'])
def submit_answer():
    try:
        data = request.json
        db.save_answer(data.get('session_id'), data.get('question_id'), data.get('user_answer'), data.get('is_correct'))
        score = db.get_live_score(data.get('session_id'))
        return jsonify({'success': True, 'score': score})
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/api/score/<int:session_id>')
def get_score(session_id):
    try:
        return jsonify(db.get_live_score(session_id))
    except Exception as e:
        return jsonify({'error': str(e)}), 500


if __name__ == '__main__':
    app.run(debug=True, port=5002)
