
    document.getElementById("file").addEventListener("change", function () {
    const fileInput = this;
    const fileName = document.getElementById("fileName");
    const fileSize = document.getElementById("fileSize");
    const labelText = fileInput.files.length ? fileInput.files[0].name : "Aucun fichier sélectionné";
    const labelSize = fileInput.files.length ? (fileInput.files[0].size / 1024).toFixed(2) + " KB" : "";

    fileName.textContent = labelText;
    fileSize.textContent = labelSize;


    const labelParagraph = document.querySelector("label.footer > p");
    labelParagraph.textContent = fileInput.files.length ? "Fichier prêt à être déposé ✅" : "Aucun fichier sélectionné";



    function redirectToResult() {
            window.location.href = "../../candidat/Resultat.php";
        }
});
