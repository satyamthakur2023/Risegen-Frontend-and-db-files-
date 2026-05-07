import streamlit as st
import PyPDF2
import ollama
import re
import tempfile
import os
from typing import List, Dict, Any

def extract_text_from_pdf(uploaded_file):
    """Extract text from uploaded PDF file with robust error handling"""
    try:
        # Save uploaded file temporarily
        with tempfile.NamedTemporaryFile(delete=False, suffix='.pdf') as tmp_file:
            tmp_file.write(uploaded_file.getvalue())
            tmp_path = tmp_file.name
        
        text = ""
        try:
            with open(tmp_path, 'rb') as file:
                pdf_reader = PyPDF2.PdfReader(file)
                
                # Extract text from first few pages to save memory
                max_pages = min(10, len(pdf_reader.pages))  # Increased to 10 pages for more content
                for page_num in range(max_pages):
                    try:
                        page = pdf_reader.pages[page_num]
                        page_text = page.extract_text()
                        if page_text:
                            # Clean the text and handle encoding issues
                            page_text = clean_text(page_text)
                            text += f"{page_text}\n\n"
                    except Exception as e:
                        st.warning(f"Could not extract text from page {page_num + 1}")
                        continue
        
        finally:
            # Clean up temporary file
            if os.path.exists(tmp_path):
                os.unlink(tmp_path)
        
        if not text.strip():
            st.error("No text could be extracted from the PDF. The file might be scanned or image-based.")
            return None
            
        return text
    except Exception as e:
        st.error(f"Error reading PDF: {str(e)}")
        return None

def clean_text(text):
    """Clean and normalize text"""
    # Replace problematic characters
    text = text.encode('utf-8', 'ignore').decode('utf-8')
    # Normalize whitespace
    text = re.sub(r'\s+', ' ', text)
    return text.strip()

def summarize_text(text, model='phi'):
    """Summarize the extracted text using lighter models"""
    try:
        # Aggressive truncation for memory issues
        if len(text) > 3000:
            text = text[:3000] + "... [text truncated]"
        
        response = ollama.chat(
            model=model,
            messages=[
                {'role': 'system', 'content': 'You are an expert at summarizing documents. Create a short summary that captures the main ideas.'},
                {'role': 'user', 'content': f"Please provide a concise summary (3-4 sentences) of this text:\n\n{text}"}
            ]
        )
        return response['message']['content']
    except Exception as e:
        st.error(f"Error generating summary with {model}: {str(e)}")
        return None

def generate_mcq_questions(text, num_questions=10, model='phi'):
    """Generate Multiple Choice Questions with memory optimization"""
    try:
        # More text for better question generation
        if len(text) > 2500:
            text = text[:2500] + "... [text truncated]"
        
        prompt = f"""
        Create {num_questions} multiple choice questions from this text.
        
        Format each question EXACTLY like this:
        Q1: [Question text?]
        A) [Option A]
        B) [Option B]  
        C) [Option C]
        D) [Option D]
        Correct: [A/B/C/D]
        Explanation: [Brief explanation why this is correct]
        
        Text: {text}
        
        Make questions clear and relevant to the text. Only one correct answer per question.
        Provide explanations for why the correct answer is right.
        """
        
        response = ollama.chat(
            model=model,
            messages=[
                {'role': 'system', 'content': 'Create clear multiple choice questions with explanations. Keep them relevant to the text.'},
                {'role': 'user', 'content': prompt}
            ]
        )
        return response['message']['content']
    except Exception as e:
        st.error(f"Error generating questions with {model}: {str(e)}")
        return None

def generate_qa_pairs(text, num_questions=10, model='phi'):
    """Generate Question-Answer pairs (without multiple choice)"""
    try:
        if len(text) > 2500:
            text = text[:2500] + "... [text truncated]"
        
        prompt = f"""
        Create {num_questions} question-answer pairs from this text.
        
        Format each pair EXACTLY like this:
        Q1: [Question text?]
        A1: [Detailed answer explaining the concept]
        
        Text: {text}
        
        Make questions that test understanding of key concepts from the text.
        Provide detailed, informative answers.
        """
        
        response = ollama.chat(
            model=model,
            messages=[
                {'role': 'system', 'content': 'Create insightful question-answer pairs with detailed explanations.'},
                {'role': 'user', 'content': prompt}
            ]
        )
        return response['message']['content']
    except Exception as e:
        st.error(f"Error generating Q&A pairs with {model}: {str(e)}")
        return None

def parse_mcq_questions(questions_text):
    """Parse the generated MCQ questions into a structured format"""
    questions = []
    current_question = {}
    lines = questions_text.split('\n')
    
    for line in lines:
        line = line.strip()
        if line.startswith('Q') and (':' in line or '.' in line):
            if current_question and current_question.get('options'):
                questions.append(current_question)
            # Extract question number and text
            question_parts = re.split(r'[.:]', line, 1)
            if len(question_parts) > 1:
                current_question = {
                    'question': question_parts[1].strip(),
                    'options': {},
                    'correct': '',
                    'explanation': ''
                }
        elif re.match(r'^[A-D]\)', line):
            option_key = line[0]  # A, B, C, or D
            option_text = line[3:].strip()
            current_question['options'][option_key] = option_text
        elif line.lower().startswith('correct:'):
            correct_part = line.split(':', 1)[1].strip()
            current_question['correct'] = correct_part[0].upper()
        elif line.lower().startswith('explanation:'):
            explanation_part = line.split(':', 1)[1].strip()
            current_question['explanation'] = explanation_part
    
    if current_question and current_question.get('options'):
        questions.append(current_question)
    
    return questions

def parse_qa_pairs(qa_text):
    """Parse the generated Q&A pairs into a structured format"""
    qa_pairs = []
    current_question = ""
    current_answer = ""
    lines = qa_text.split('\n')
    
    for line in lines:
        line = line.strip()
        if line.startswith('Q') and (':' in line or '.' in line):
            # Save previous pair if exists
            if current_question and current_answer:
                qa_pairs.append({
                    'question': current_question,
                    'answer': current_answer
                })
            # Start new question
            question_parts = re.split(r'[.:]', line, 1)
            if len(question_parts) > 1:
                current_question = question_parts[1].strip()
                current_answer = ""
        elif line.startswith('A') and (':' in line or '.' in line):
            answer_parts = re.split(r'[.:]', line, 1)
            if len(answer_parts) > 1:
                current_answer = answer_parts[1].strip()
    
    # Add the last pair
    if current_question and current_answer:
        qa_pairs.append({
            'question': current_question,
            'answer': current_answer
        })
    
    return qa_pairs

def evaluate_answers(questions, user_answers):
    """Evaluate user answers and provide score"""
    score = 0
    results = []
    
    for i, question in enumerate(questions):
        user_answer = user_answers.get(f"q{i}", "").strip().upper()
        correct_answer = question.get('correct', '').strip().upper()
        explanation = question.get('explanation', 'No explanation provided.')
        
        is_correct = user_answer == correct_answer
        if is_correct:
            score += 1
        
        results.append({
            'question': question['question'],
            'user_answer': user_answer,
            'correct_answer': correct_answer,
            'is_correct': is_correct,
            'explanation': explanation
        })
    
    return score, results

def check_ollama_models():
    """Check available Ollama models and suggest alternatives"""
    try:
        models = ollama.list()
        available_models = [model['name'] for model in models['models']]
        return available_models
    except:
        return ['phi', 'mistral']  # Fallback

def get_lightweight_model(available_models):
    """Get the lightest available model"""
    lightweight_models = ['phi', 'llama2:3b', 'mistral:7b', 'mistral']
    
    for model in lightweight_models:
        if any(m.startswith(model.split(':')[0]) for m in available_models):
            return model
    return available_models[0] if available_models else 'phi'

def main():
    st.set_page_config(
        page_title="PDF Analyzer - Enhanced",
        page_icon="📚",
        layout="wide"
    )
    
    st.title("📚 PDF Analyzer - Enhanced Version")
    st.markdown("Now with increased question limits (up to 100) and Q&A generation")
    
    # Initialize session state
    if 'questions' not in st.session_state:
        st.session_state.questions = []
    if 'qa_pairs' not in st.session_state:
        st.session_state.qa_pairs = []
    if 'user_answers' not in st.session_state:
        st.session_state.user_answers = {}
    if 'show_test' not in st.session_state:
        st.session_state.show_test = False
    if 'evaluation_results' not in st.session_state:
        st.session_state.evaluation_results = None
    if 'generation_type' not in st.session_state:
        st.session_state.generation_type = "mcq"
    
    # Check available models and select lightest
    available_models = check_ollama_models()
    default_model = get_lightweight_model(available_models)
    
    # Sidebar for configuration
    with st.sidebar:
        st.header("Configuration")
        
        if available_models:
            st.success(f"Available: {', '.join(available_models)}")
            model_name = st.selectbox(
                "Select Model",
                available_models,
                index=available_models.index(default_model) if default_model in available_models else 0,
                help="Phi recommended for 2.8GB RAM"
            )
        else:
            st.warning("No models detected. Using Phi as default.")
            model_name = 'phi'
        
        st.markdown("---")
        st.warning("⚡ **Memory Optimized**")
        st.info("""
        **Enhanced Features:**
        - Up to 100 questions per PDF
        - Multiple Choice Questions
        - Question-Answer pairs
        - Detailed explanations
        """)
        
        if st.button("🔄 Restart Ollama (if stuck)"):
            st.info("Run in terminal: `pkill ollama && ollama serve`")
        
        st.markdown("---")
        st.info("""
        **Instructions:**
        1. Upload PDF (up to 10 pages)
        2. Generate summary
        3. Choose question type (MCQ or Q&A)
        4. Generate questions (up to 100)
        5. Take test & evaluate
        """)
    
    # File upload section
    uploaded_file = st.file_uploader(
        "Choose a PDF file",
        type="pdf",
        help="For best results, use PDFs with clear text content"
    )
    
    if uploaded_file is not None:
        # Display file info
        file_size_mb = uploaded_file.size / (1024 * 1024)
        file_details = {
            "Filename": uploaded_file.name,
            "File size": f"{file_size_mb:.2f} MB"
        }
        st.write("File details:", file_details)
        
        if file_size_mb > 10:
            st.warning("⚠️ Large file detected. Processing may be slow.")
        
        # Extract text from PDF
        with st.spinner("Extracting text from PDF (first 10 pages only)..."):
            extracted_text = extract_text_from_pdf(uploaded_file)
        
        if extracted_text:
            # Display extracted text preview
            with st.expander("View Extracted Text (Preview)"):
                preview_text = extracted_text[:800] + ("..." if len(extracted_text) > 800 else "")
                st.text_area(
                    "Extracted Text",
                    preview_text,
                    height=200,
                    key="extracted_text",
                    disabled=True
                )
            
            col1, col2 = st.columns(2)
            
            with col1:
                # Summary section
                st.subheader("📋 PDF Summary")
                if st.button("Generate Summary", key="summary_btn", type="primary"):
                    with st.spinner("Generating concise summary..."):
                        summary = summarize_text(extracted_text, model_name)
                    
                    if summary:
                        st.success("✓ Summary generated!")
                        st.text_area(
                            "Summary",
                            summary,
                            height=200,
                            key="summary_output"
                        )
                        
                        # Download summary
                        st.download_button(
                            label="Download Summary",
                            data=summary,
                            file_name=f"{uploaded_file.name}_summary.txt",
                            mime="text/plain"
                        )
                    else:
                        st.error("❌ Failed to generate summary. Try Phi model.")
            
            with col2:
                # Question generation section
                st.subheader("🎯 Generate Questions")
                
                # Question type selection
                generation_type = st.radio(
                    "Select question type:",
                    ["Multiple Choice (MCQ)", "Question-Answer Pairs"],
                    key="generation_type"
                )
                
                # Number of questions with increased limit
                num_questions = st.slider(
                    "Number of questions",
                    min_value=5,
                    max_value=100,  # Increased from 10 to 100
                    value=15,
                    key="num_questions",
                    help="Generate up to 100 questions from the PDF content"
                )
                
                if st.button("Generate Questions", key="questions_btn", type="primary"):
                    with st.spinner(f"Generating {num_questions} {generation_type.lower()}..."):
                        if generation_type == "Multiple Choice (MCQ)":
                            questions_text = generate_mcq_questions(extracted_text, num_questions, model_name)
                            if questions_text:
                                st.success("✓ MCQ Questions generated!")
                                
                                # Parse and store questions
                                parsed_questions = parse_mcq_questions(questions_text)
                                if parsed_questions:
                                    st.session_state.questions = parsed_questions
                                    st.session_state.qa_pairs = []
                                    st.session_state.show_test = True
                                    st.session_state.user_answers = {}
                                    st.session_state.evaluation_results = None
                                    st.session_state.generation_type = "mcq"
                                    
                                    # Display generated questions
                                    st.text_area(
                                        "Generated MCQ Questions",
                                        questions_text,
                                        height=300,
                                        key="questions_output"
                                    )
                                    
                                    # Download questions
                                    st.download_button(
                                        label="Download MCQ Questions",
                                        data=questions_text,
                                        file_name=f"{uploaded_file.name}_mcq_questions.txt",
                                        mime="text/plain"
                                    )
                                else:
                                    st.error("Could not parse questions. Try generating again.")
                            else:
                                st.error("❌ Failed to generate MCQ questions. Try with Phi model.")
                        
                        else:  # Q&A pairs
                            qa_text = generate_qa_pairs(extracted_text, num_questions, model_name)
                            if qa_text:
                                st.success("✓ Q&A Pairs generated!")
                                
                                # Parse and store Q&A pairs
                                parsed_qa_pairs = parse_qa_pairs(qa_text)
                                if parsed_qa_pairs:
                                    st.session_state.qa_pairs = parsed_qa_pairs
                                    st.session_state.questions = []
                                    st.session_state.show_test = False
                                    st.session_state.generation_type = "qa"
                                    
                                    # Display generated Q&A pairs
                                    st.text_area(
                                        "Generated Q&A Pairs",
                                        qa_text,
                                        height=300,
                                        key="qa_output"
                                    )
                                    
                                    # Download Q&A pairs
                                    st.download_button(
                                        label="Download Q&A Pairs",
                                        data=qa_text,
                                        file_name=f"{uploaded_file.name}_qa_pairs.txt",
                                        mime="text/plain"
                                    )
                                else:
                                    st.error("Could not parse Q&A pairs. Try generating again.")
                            else:
                                st.error("❌ Failed to generate Q&A pairs. Try with Phi model.")
            
            # Test section for MCQ questions
            if st.session_state.show_test and st.session_state.questions and st.session_state.generation_type == "mcq":
                st.markdown("---")
                st.subheader("📝 MCQ Test")
                
                st.info(f"Answer {len(st.session_state.questions)} questions below:")
                
                with st.form("test_form"):
                    for i, question in enumerate(st.session_state.questions):
                        st.markdown(f"**Q{i+1}: {question['question']}**")
                        
                        options = question.get('options', {})
                        if options:
                            answer_key = f"q{i}"
                            
                            # Create radio buttons for options
                            user_answer = st.radio(
                                f"Choose answer for Q{i+1}:",
                                options=list(options.keys()),
                                format_func=lambda x: f"{x}) {options[x]}",
                                key=answer_key,
                                index=None
                            )
                            
                            if user_answer:
                                st.session_state.user_answers[answer_key] = user_answer
                            st.markdown("---")
                    
                    # Submit button
                    submitted = st.form_submit_button("✅ Submit Answers for Evaluation")
                    
                    if submitted:
                        if len(st.session_state.user_answers) < len(st.session_state.questions):
                            st.warning(f"Please answer all {len(st.session_state.questions)} questions.")
                        else:
                            with st.spinner("Evaluating answers..."):
                                score, results = evaluate_answers(
                                    st.session_state.questions, 
                                    st.session_state.user_answers
                                )
                                
                                st.session_state.evaluation_results = {
                                    'score': score,
                                    'total': len(st.session_state.questions),
                                    'results': results
                                }
                
                # Display evaluation results
                if st.session_state.evaluation_results:
                    results = st.session_state.evaluation_results
                    score_percentage = (results['score'] / results['total']) * 100
                    
                    st.markdown("---")
                    st.subheader("📊 Evaluation Results")
                    
                    # Score display with emojis
                    col1, col2, col3 = st.columns(3)
                    with col1:
                        st.metric("Score", f"{results['score']}/{results['total']}")
                    with col2:
                        st.metric("Percentage", f"{score_percentage:.1f}%")
                    with col3:
                        if score_percentage >= 80:
                            st.metric("Grade", "🎉 Excellent!")
                        elif score_percentage >= 60:
                            st.metric("Grade", "👍 Good")
                        else:
                            st.metric("Grade", "📚 Keep Learning")
                    
                    # Detailed results
                    st.subheader("Detailed Feedback:")
                    
                    for i, result in enumerate(results['results']):
                        with st.expander(f"Q{i+1}: {'✅ Correct' if result['is_correct'] else '❌ Incorrect'}", expanded=False):
                            st.write(f"**Question:** {result['question']}")
                            st.write(f"**Your Answer:** {result['user_answer']}")
                            st.write(f"**Correct Answer:** {result['correct_answer']}")
                            st.write(f"**Explanation:** {result['explanation']}")
            
            # Display Q&A pairs section
            if st.session_state.qa_pairs and st.session_state.generation_type == "qa":
                st.markdown("---")
                st.subheader("📖 Question-Answer Pairs")
                
                st.info(f"Generated {len(st.session_state.qa_pairs)} Q&A pairs for study:")
                
                for i, qa_pair in enumerate(st.session_state.qa_pairs):
                    with st.expander(f"Q{i+1}: {qa_pair['question']}", expanded=False):
                        st.write(f"**Answer:** {qa_pair['answer']}")
        
        else:
            st.error("❌ Failed to extract text from PDF.")
            st.info("""
            **Solutions:**
            - Try a different PDF file
            - Use a simpler, text-based PDF
            - Ensure PDF has selectable text (not scanned images)
            - Try splitting large PDF into smaller parts
            """)
    
    else:
        # Welcome message
        st.markdown("""
        ## 🚀 Enhanced PDF Analyzer
        
        **Now with increased question limits and Q&A generation**
        
        ### What you can do:
        - 📖 **Extract text** from PDFs (up to 10 pages)
        - 📋 **Get AI summaries** using lightweight models
        - 🎯 **Generate up to 100 MCQ questions** for comprehensive testing
        - ❓ **Create Question-Answer pairs** for detailed study
        - 📊 **Evaluate your answers** instantly with explanations
        
        ### 🆕 Enhanced Features:
        - **Increased Limits**: Generate 5-100 questions per PDF
        - **Two Modes**: Multiple Choice Questions AND Question-Answer pairs
        - **Better Explanations**: Detailed answers and explanations
        - **Study Materials**: Download Q&A pairs for offline study
        
        ### 🔧 Setup Commands:
        ```bash
        # Install Ollama models
        ollama pull phi
        ollama pull mistral
        ```
        
        ### 💡 Tips for best results:
        1. Use PDFs with clear text content
        2. Start with Phi model for low memory usage
        3. Generate 15-20 questions initially to test
        4. Use Q&A pairs for detailed study material
        5. Restart Ollama if you see memory errors
        """)

if __name__ == "__main__":
    main()