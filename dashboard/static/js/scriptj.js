document.getElementById("resumeForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent page reload

    let formData = new FormData();
    formData.append("job_title", document.getElementById("job_title").value);
    formData.append("job_description", document.getElementById("job_description").value);

    let files = document.getElementById("resumes").files;
    for (let i = 0; i < files.length; i++) {
        formData.append("resumes", files[i]);
    }

    fetch("http://localhost:5001/matcher", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        let resultsDiv = document.getElementById("results");
        resultsDiv.innerHTML = "<h3>Matched Resumes:</h3>";
        if (data.matched_resumes.length === 0) {
            resultsDiv.innerHTML += "<p>No suitable matches found.</p>";
        } else {
            let list = "<ul>";
            data.matched_resumes.forEach(resume => {
                list += `<li>${resume.name} - Score: ${resume.similarity_score}</li>`;
            });
            list += "</ul>";
            resultsDiv.innerHTML += list;
        }
    })
    .catch(error => console.error("Error:", error));
});
