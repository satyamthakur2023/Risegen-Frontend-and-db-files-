class MCQDatabase:
    def __init__(self):
        self._pdfs = {}
        self._topics = {}
        self._questions = {}
        self._sessions = {}
        self._answers = {}
        self._pdf_counter = 1
        self._session_counter = 1

    def insert_pdf_upload(self, user_id, filename, file_path):
        pdf_id = self._pdf_counter
        self._pdfs[pdf_id] = {'user_id': user_id, 'filename': filename, 'file_path': file_path}
        self._pdf_counter += 1
        return pdf_id

    def insert_topics(self, pdf_id, topics_data):
        self._topics[pdf_id] = topics_data

    def insert_questions(self, pdf_id, topic_id, questions_data):
        self._questions.setdefault(pdf_id, []).extend(questions_data)

    def get_topics_by_pdf(self, pdf_id):
        return self._topics.get(pdf_id, [])

    def get_questions_by_topic(self, topic_id, limit=None):
        questions = self._questions.get(topic_id, [])
        return questions[:limit] if limit else questions

    def create_test_session(self, user_id, pdf_id, total_questions):
        session_id = self._session_counter
        self._sessions[session_id] = {'user_id': user_id, 'pdf_id': pdf_id, 'total': total_questions}
        self._answers[session_id] = []
        self._session_counter += 1
        return session_id

    def save_answer(self, session_id, question_id, user_answer, is_correct):
        self._answers.setdefault(session_id, []).append({'question_id': question_id, 'user_answer': user_answer, 'is_correct': is_correct})

    def get_live_score(self, session_id):
        answers = self._answers.get(session_id, [])
        correct = sum(1 for a in answers if a['is_correct'])
        return {'total': len(answers), 'correct': correct}