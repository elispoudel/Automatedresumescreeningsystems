from flask import Flask, request, jsonify
from flask_cors import CORS  # Import CORS
import os
from model import match_resumes

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

app.config['UPLOAD_FOLDER'] = 'uploads/'

# Ensure the upload folder exists
if not os.path.exists(app.config['UPLOAD_FOLDER']):
    os.makedirs(app.config['UPLOAD_FOLDER'])

@app.route('/matcher', methods=['POST', 'OPTIONS'])  # Add OPTIONS for preflight
def matcher():
    try:
        job_title = request.form.get('job_title', '').strip()
        job_description = request.form.get('job_description', '').strip()
        resume_files = request.files.getlist('resumes')

        print("Job Title:", job_title)  # Debug log
        print("Job Description:", job_description)  # Debug log
        print("Resume Files:", resume_files)  # Debug log

        if not job_description or not resume_files:
            return jsonify({"error": "Please upload resumes and enter a job description."}), 400

        if job_title and job_title.lower() not in job_description.lower():
            job_description = f"Job Title: {job_title}\n{job_description}"

        saved_files = []
        for resume_file in resume_files:
            filename = os.path.join(app.config['UPLOAD_FOLDER'], resume_file.filename)
            resume_file.save(filename)
            saved_files.append(filename)

        results, message = match_resumes(job_description, saved_files)
        top_resumes = [{"name": name, "similarity_score": score} for _, name, score in results]

        return jsonify({
            "message": message,
            "matched_resumes": top_resumes
        })
    except Exception as e:
        print("Error in /matcher:", str(e))  # Debug log
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5001)