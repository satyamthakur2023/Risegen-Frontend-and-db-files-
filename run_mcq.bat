@echo off
echo ============================================
echo  RiseGen MCQ Server - Starting...
echo ============================================
echo Installing required packages...
pip install flask flask-cors PyPDF2 nltk mysql-connector-python
echo.
echo Starting MCQ server on http://localhost:5002
echo Press Ctrl+C to stop
echo.
python advanced_mcq_server_v2.py
pause
