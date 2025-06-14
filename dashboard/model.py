import os
import docx2txt
import PyPDF2
import re
import numpy as np
import nltk
import spacy
from nltk.corpus import stopwords
from nltk.tokenize import word_tokenize
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from sentence_transformers import SentenceTransformer, util

# Download NLTK stopwords if not already downloaded
nltk.download('stopwords')
nltk.download('punkt')

# Load SBERT model
sbert_model = SentenceTransformer('all-MiniLM-L6-v2')

# Load spaCy's English model for Named Entity Recognition (NER)
nlp = spacy.load('en_core_web_sm')

# Function to clean extracted text
def clean_text(text):
    """
    Cleans extracted text by:
    - Lowercasing
    - Removing special characters, punctuation, and extra spaces
    - Removing stopwords
    """
    text = text.lower()  # Convert to lowercase
    text = re.sub(r'\s+', ' ', text)  # Remove extra spaces
    text = re.sub(r'[^a-zA-Z0-9\s]', '', text)  # Remove special characters
    words = word_tokenize(text)  # Tokenize words
    cleaned_text = ' '.join([word for word in words if word not in stopwords.words('english')])  # Remove stopwords
    return cleaned_text

# Function to extract user name using Named Entity Recognition (NER)
def extract_name(text):
    """
    Extracts the first detected PERSON entity from the resume text.
    """
    doc = nlp(text)
    for ent in doc.ents:
        if ent.label_ == "PERSON":
            return ent.text  # Return the first detected name
    return "Unknown"  # Default if no name is found

# Function to extract text from PDF
def extract_text_from_pdf(file_path):
    text = ""
    with open(file_path, 'rb') as file:
        reader = PyPDF2.PdfReader(file)
        for page in reader.pages:
            page_text = page.extract_text()
            if page_text:
                text += page_text + " "
    cleaned_text = clean_text(text)
    return cleaned_text, extract_name(text)

# Function to extract text from DOCX
def extract_text_from_docx(file_path):
    text = docx2txt.process(file_path)
    cleaned_text = clean_text(text)
    return cleaned_text, extract_name(text)

# Function to extract text from TXT
def extract_text_from_txt(file_path):
    with open(file_path, 'r', encoding='utf-8') as file:
        text = file.read()
    cleaned_text = clean_text(text)
    return cleaned_text, extract_name(text)

# General function to extract text and name from different file formats
def extract_text(file_path):
    if file_path.endswith('.pdf'):
        return extract_text_from_pdf(file_path)
    elif file_path.endswith('.docx'):
        return extract_text_from_docx(file_path)
    elif file_path.endswith('.txt'):
        return extract_text_from_txt(file_path)
    else:
        return "", "Unknown"

# Function to match resumes with job description using SBERT + TF-IDF.
# Now accepts an optional job_title parameter.
def match_resumes(job_description, resume_files, job_title=None):
    resumes = []
    names = []
    
    # If a job title is provided and it's not already present in the job description,
    # prepend it with the label "Job Title:".
    if job_title and "job title" not in job_description.lower():
        job_description = f"Job Title: {job_title}\n{job_description}"
    
    for resume in resume_files:
        text, name = extract_text(resume)
        resumes.append(text)
        names.append(name)

    if not resumes or not job_description:
        return [], "Please upload resumes and enter a job description."

    # Clean job description before processing
    job_description = clean_text(job_description)

    # TF-IDF Similarity
    vectorizer = TfidfVectorizer().fit_transform([job_description] + resumes)
    vectors = vectorizer.toarray()
    job_vector = vectors[0]
    resume_vectors = vectors[1:]
    tfidf_similarities = cosine_similarity([job_vector], resume_vectors)[0]

    # SBERT Similarity
    job_embedding = sbert_model.encode(job_description, convert_to_tensor=True)
    resume_embeddings = sbert_model.encode(resumes, convert_to_tensor=True)
    sbert_similarities = np.array(util.pytorch_cos_sim(job_embedding, resume_embeddings).cpu().numpy())[0]

    # Combined Score (80% SBERT + 20% TF-IDF)
    combined_scores = (sbert_similarities * 0.8) + (tfidf_similarities * 0.2)

    # Sort all resumes by score in descending order
    top_indices = combined_scores.argsort()[::-1]
    top_resumes = [(resume_files[i], names[i], round(combined_scores[i], 2)) for i in top_indices]

    return top_resumes, "Top matching resumes:"
