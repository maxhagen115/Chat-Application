const form = document.querySelector(".typing-area"),
  inputField = form.querySelector(".input-field"),
  inputFile = form.querySelector(".input-file"),
  sendBtn = form.querySelector("button"),
  chatBox = document.querySelector(".chat-box"),
  msg = document.getElementById("msg"),
  file_name = document.querySelector("#file-name");

form.onsubmit = (e) => {
  e.preventDefault();
};

sendBtn.onclick = () => {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/insert-chat.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        inputField.value = "";
        msg.style.display = "block";
        file_name.remove();
      }
    }
  };
  let formData = new FormData(form);
  xhr.send(formData);
};

chatBox.onmouseenter = () => {
  chatBox.classList.add("active");
};
chatBox.onmouseleave = () => {
  chatBox.classList.remove("active");
};

setInterval(() => {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/get-chat.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        let data = xhr.response;
        chatBox.innerHTML = data;

        // ✅ Run lazy loader AFTER content is replaced
        lazyLoadImages();

        if (!chatBox.classList.contains("active")) {
          scrollToBottom();
        }
      }
    }
  };
  let formData = new FormData(form);
  xhr.send(formData);
}, 500);

function scrollToBottom() {
  chatBox.scrollTop = chatBox.scrollHeight;
}

// user is typing
const messageInput = document.querySelector("#msg");

let typingTimer;

messageInput.addEventListener("input", () => {
  clearTimeout(typingTimer);
  updateTypingStatus(true);

  typingTimer = setTimeout(() => {
    updateTypingStatus(false);
  }, 2000);
});

function updateTypingStatus(isTyping) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/typing.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send("typing_to=" + (isTyping ? incomming_id : ""));
}

// modal for delete message

let selectedMsgId = null;

document.addEventListener("click", function (e) {
  const modal = document.getElementById("deleteModal");

  if (e.target.classList.contains("delete-msg")) {
    const msgDiv = e.target.closest(".details");
    selectedMsgId = msgDiv?.getAttribute("data-id");
    modal.classList.remove("hidden");
  }

  if (e.target.classList.contains("modal-cancel") || e.target.id === "deleteModal") {
    modal.classList.add("hidden");
    selectedMsgId = null;
  }

  if (e.target.classList.contains("modal-btn")) {
    const deleteFor = e.target.getAttribute("data-action");

    fetch("php/delete_msg.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `msg_id=${selectedMsgId}&delete_for=${deleteFor}`
    })
      .then(res => res.text())
      .then(response => {
        if (response.trim() === "success") {
          const msgDiv = document.querySelector(`.details[data-id='${selectedMsgId}']`);
          if (deleteFor === "both") {
            msgDiv.innerHTML = '<div><p><em>Dit bericht is verwijderd</em></p></div>';
          } else {
            msgDiv.parentElement.remove();
          }
        }

        modal.classList.add("hidden");
        selectedMsgId = null;
      });
  }
});

// ✅ Lazy loading images (only after they load or complete)
function lazyLoadImages() {
  const lazyImages = document.querySelectorAll('img[loading="lazy"]:not(.loaded)');
  lazyImages.forEach(img => {
    if (img.complete) {
      img.classList.add("loaded");
    } else {
      img.addEventListener("load", () => {
        img.classList.add("loaded");
      });
    }
  });
}

// ✅ Run once on initial load
document.addEventListener("DOMContentLoaded", () => {
  lazyLoadImages();
});

// images navigation

let images = []; // all chat images
let currentIndex = -1;

const modal = document.getElementById("imageModal");
const modalImg = document.getElementById("enlargedImage");
const downloadBtn = document.getElementById("downloadImage");
const prevBtn = document.getElementById("prevImage");
const nextBtn = document.getElementById("nextImage");

// Open modal and store index
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("chat-image")) {
    images = Array.from(document.querySelectorAll(".chat-image"));
    currentIndex = images.indexOf(e.target);
    openImage(images[currentIndex]);
  }
});

function openImage(img) {
  const src = img.getAttribute("src");
  modal.classList.remove("hidden");
  modalImg.src = src;
  downloadBtn.href = src;

  updateNavButtons();
}

function showImage(direction) {
  if (images.length === 0) return;

  const newIndex = currentIndex + direction;

  // Stop navigation if out of bounds
  if (newIndex < 0 || newIndex >= images.length) return;

  currentIndex = newIndex;
  openImage(images[currentIndex]);
}

function updateNavButtons() {
  // Disable or hide nav buttons if at bounds
  prevBtn.style.display = currentIndex <= 0 ? "none" : "block";
  nextBtn.style.display = currentIndex >= images.length - 1 ? "none" : "block";
}

prevBtn.addEventListener("click", () => showImage(-1));
nextBtn.addEventListener("click", () => showImage(1));

// Close on outside click or ESC
modal.addEventListener("click", (e) => {
  if (e.target === modal) {
    modal.classList.add("hidden");
    modalImg.src = "";
    downloadBtn.href = "#";
  }
});

document.addEventListener("keydown", (e) => {
  if (!modal.classList.contains("hidden")) {
    if (e.key === "ArrowRight") showImage(1);
    if (e.key === "ArrowLeft") showImage(-1);
    if (e.key === "Escape") modal.classList.add("hidden");
  }
});
