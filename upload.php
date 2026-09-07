<?php

session_start();

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload File</title>
</head>
<body>

<h1>Upload File</h1>

<form action="upload_process.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="file">
    <input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>"
>
    <button type="submit">Upload</button>
</form>

<h2>My Files</h2>

<div id="files"></div>

<script>
async function loadFiles() {

    const response = await fetch("api/my-files.php");

    const result = await response.json();

    const filesContainer = document.getElementById("files");

    if (!result.success) {
        filesContainer.textContent = result.message;
        return;
    }

    filesContainer.innerHTML = "";

    result.data.forEach(file => {

        const fileElement = document.createElement("div");

        const name = document.createElement("span");
        name.textContent = file.original_name;
        const info = document.createElement("span");

        const sizeKB = (file.size / 1024).toFixed(2);

        info.textContent =
            ` | ${file.mime_type} | ${sizeKB} KB | ${file.created_at}`;

        fileElement.appendChild(name);
        fileElement.appendChild(info);

        const link = document.createElement("a");
        link.href = "file.php?file=" + encodeURIComponent(file.stored_name);
        link.textContent = " Download";

        const deleteButton = document.createElement("button");
        deleteButton.textContent = " Delete";

        deleteButton.addEventListener("click", async () => {

            const formData = new FormData();
            formData.append("id", file.id);
            formData.append(
                "csrf_token",
                "<?= htmlspecialchars($_SESSION["csrf_token"]) ?>"
            );

            const response = await fetch("api/delete-file.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                loadFiles();
            } else {
                alert(result.message);
            }
    });

    fileElement.appendChild(name);
    fileElement.appendChild(link);
    fileElement.appendChild(deleteButton);

    filesContainer.appendChild(fileElement);
    });
}

loadFiles();
</script>

</body>
</html>