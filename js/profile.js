const form = document.getElementById("create-post-form");
const message = document.getElementById("form-message");

form.addEventListener("submit", async (event) => {
  event.preventDefault();

  const formData = new FormData(form);

  try {
    const response = await fetch("api/create-post.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (!response.ok) {
      console.log(data);

      message.textContent = data.message || "Something went wrong.";
      return;
    }

    message.textContent = data.message;

    form.reset();

    await loadPosts();
  } catch (error) {
    console.error(error);
    message.textContent = "Request failed.";
  }
});

async function loadPosts() {
  try {
    const response = await fetch("api/my-post.php");

    if (!response.ok) {
      throw new Error(`HTTP error: ${response.status}`);
    }

    const data = await response.json();

    const container = document.getElementById("posts-container");

    container.innerHTML = "";

    if (!data.success || data.posts.length === 0) {
      container.innerHTML = "<p>No posts found.</p>";
      return;
    }

    data.posts.forEach((post) => {
      const article = document.createElement("article");

      const title = document.createElement("h2");
      title.textContent = post.title;

      const content = document.createElement("p");
      const deleteButton = document.createElement("button");

      deleteButton.textContent = "Delete";

      deleteButton.addEventListener("click", () => {
        deletePost(post.id, article);
      });

      const editButton = document.createElement("button");

      editButton.textContent = "Edit";

      editButton.addEventListener("click", () => {
        editPost(post);
      });

      container.appendChild(article);
      content.textContent = post.content;

      article.appendChild(title);
      article.appendChild(content);
      article.appendChild(deleteButton);
      article.appendChild(editButton);

      container.appendChild(article);
    });
  } catch (error) {
    console.error("Failed to load posts:", error);
  }
}
async function deletePost(postId, element) {
  const csrfToken = document.querySelector('input[name="csrf_token"]').value;

  const formData = new FormData();

  formData.append("id", postId);
  formData.append("csrf_token", csrfToken);

  try {
    const response = await fetch("api/delete-post.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (!response.ok) {
      console.error(data);
      return;
    }

    if (data.success) {
      element.remove();
    }
  } catch (error) {
    console.error("Delete failed:", error);
  }
}

async function editPost(post) {
  const newTitle = prompt("Enter new title:", post.title);

  if (newTitle === null) {
    return;
  }

  const newContent = prompt("Enter new content:", post.content);

  if (newContent === null) {
    return;
  }

  const csrfToken = document.querySelector('input[name="csrf_token"]').value;

  const formData = new FormData();

  formData.append("id", post.id);
  formData.append("title", newTitle);
  formData.append("content", newContent);
  formData.append("csrf_token", csrfToken);

  try {
    const response = await fetch("api/update-post.php", {
      method: "POST",
      body: formData,
    });

    const data = await response.json();

    if (!response.ok) {
      console.error(data);
      return;
    }

    if (data.success) {
      await loadPosts();
    }
  } catch (error) {
    console.error("Update failed:", error);
  }
}

loadPosts();
