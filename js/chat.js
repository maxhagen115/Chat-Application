const form = document.querySelector(".typing-area"),
  inputField = form.querySelector(".input-field"),
  inputFile = form.querySelector(".input-file"),
  sendBtn = form.querySelector("button"),
  chatBox = document.querySelector(".chat-box"),
  msg = document.getElementById("msg"),
  file_name = document.querySelector("#file-name");

let isEditingMessage = false;
let currentlyEditingId = null;
let chatRefreshInterval = null;
let isDropdownOpen = false;

form.onsubmit = (e) => {
  e.preventDefault(); // prevent full page reload

  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/insert-chat.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      inputField.value = "";
      inputFile.value = "";
      file_name.textContent = "";
      file_name.style.display = "none";
      msg.style.display = "block";
    }
  };
  xhr.send(new FormData(form));
};


inputFile.addEventListener("change", () => {
  const file = inputFile.files[0];
  if (file) {
    const name = file.name.length > 20 ? file.name.slice(0, 17) + "..." : file.name;
    file_name.textContent = name;
    file_name.style.display = "inline";
    msg.style.display = "none";
  } else {
    file_name.textContent = "";
    file_name.style.display = "none";
    msg.style.display = "block";
  }
});

chatBox.onmouseenter = () => chatBox.classList.add("active");
chatBox.onmouseleave = () => chatBox.classList.remove("active");

function startChatInterval() {
  if (!chatRefreshInterval) {
    chatRefreshInterval = setInterval(loadChatMessages, 500);
  }
}

function stopChatInterval() {
  clearInterval(chatRefreshInterval);
  chatRefreshInterval = null;
}

function loadChatMessages() {
  if (isEditingMessage || isDropdownOpen) return;

  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/get-chat.php", true);
  xhr.onload = () => {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
      chatBox.innerHTML = xhr.response;
      lazyLoadImages();
      if (!chatBox.classList.contains("active")) {
        scrollToBottom();
      }
    }
  };
  xhr.send(new FormData(form));
}

startChatInterval();

function scrollToBottom() {
  chatBox.scrollTop = chatBox.scrollHeight;
}

const messageInput = document.querySelector("#msg");
let typingTimer;
messageInput.addEventListener("input", () => {
  clearTimeout(typingTimer);
  updateTypingStatus(true);
  typingTimer = setTimeout(() => updateTypingStatus(false), 2000);
});

function updateTypingStatus(isTyping) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "php/typing.php", true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.send("typing_to=" + (isTyping ? incomming_id : ""));
}

// DELETE MESSAGE
let selectedMsgId = null;
document.addEventListener("click", function (e) {
  const deletemodal = document.getElementById("deleteModal");

  if (e.target.classList.contains("delete-msg")) {
    const msgDiv = e.target.closest(".details");
    selectedMsgId = msgDiv?.getAttribute("data-id");
    deletemodal.classList.remove("hidden");
  }

  if (e.target.classList.contains("modal-cancel") || e.target.id === "deleteModal") {
    deletemodal.classList.add("hidden");
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
        deletemodal.classList.add("hidden");
        selectedMsgId = null;
      });
  }
});

// LAZY IMAGE LOADING
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
document.addEventListener("DOMContentLoaded", lazyLoadImages);

// IMAGE MODAL + NAVIGATION
let images = [], currentIndex = -1;

const modal = document.getElementById("imageModal"),
  modalImg = document.getElementById("enlargedImage"),
  downloadBtn = document.getElementById("downloadImage"),
  prevBtn = document.getElementById("prevImage"),
  nextBtn = document.getElementById("nextImage");

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
  downloadBtn.href = src;
  const filename = src.split("/").pop();
  downloadBtn.setAttribute("download", filename);

  updateNavButtons();

}

function showImage(direction) {
  if (!images.length) return;
  const newIndex = currentIndex + direction;
  if (newIndex < 0 || newIndex >= images.length) return;
  currentIndex = newIndex;
  openImage(images[currentIndex]);
}

function updateNavButtons() {
  prevBtn.style.display = currentIndex <= 0 ? "none" : "block";
  nextBtn.style.display = currentIndex >= images.length - 1 ? "none" : "block";
}

prevBtn.addEventListener("click", () => showImage(-1));
nextBtn.addEventListener("click", () => showImage(1));

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

// EDIT MESSAGE
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("edit-msg")) {
    if (currentlyEditingId) return;
    isEditingMessage = true;
    stopChatInterval();

    const msgDiv = e.target.closest(".details");
    const msgId = msgDiv.getAttribute("data-id");
    const p = msgDiv.querySelector("p");
    const originalText = p.textContent.replace(" (bewerkt)", "").trim();
    currentlyEditingId = msgId;

    const input = document.createElement("input");
    input.type = "text";
    input.className = "edit-inline-input";
    input.value = originalText;
    input.setAttribute("data-original", originalText);
    e.target.remove();

    requestAnimationFrame(() => {
      p.replaceWith(input);
      input.focus();
    });

    input.addEventListener("keydown", (event) => {
      if (event.key === "Enter") {
        const newText = input.value.trim();
        if (newText && newText !== originalText) {
          fetch("php/edit_msg.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `msg_id=${msgId}&new_msg=${encodeURIComponent(newText)}`
          })
            .then(res => res.text())
            .then(response => {
              if (response === "success") {
                const newP = document.createElement("p");
                newP.innerHTML = newText + ' <small>(bewerkt)</small>';
                input.replaceWith(newP);
                isEditingMessage = false;
                currentlyEditingId = null;
                startChatInterval();
              }
            });
        } else {
          cancelEdit(input, originalText);
        }
      }
      if (event.key === "Escape") {
        cancelEdit(input, originalText);
      }
    });

    setTimeout(() => {
      document.addEventListener("click", function clickOutside(ev) {
        if (!msgDiv.contains(ev.target)) {
          cancelEdit(input, originalText);
          document.removeEventListener("click", clickOutside);
        }
      }, { once: true });
    }, 0);
  }
});

function cancelEdit(input, originalText) {
  const newP = document.createElement("p");
  newP.textContent = originalText;
  input.replaceWith(newP);
  isEditingMessage = false;
  currentlyEditingId = null;
  startChatInterval();
}

// dropdown toggle
document.addEventListener("click", function (e) {
  const isMenuTrigger = e.target.classList.contains("menu-trigger");
  const isEditOption = e.target.classList.contains("edit-option");
  const isDeleteOption = e.target.classList.contains("delete-option");

  // Toggle dropdown
  if (isMenuTrigger) {
    e.stopPropagation();
    const container = e.target.closest(".dropdown-container");
    const menu = container.querySelector(".dropdown-menu");

    // Close all others
    document.querySelectorAll(".dropdown-menu").forEach((m) => {
      if (m !== menu) m.classList.add("hidden");
    });

    menu.classList.toggle("hidden");
    isDropdownOpen = !menu.classList.contains("hidden");
    return;
  }

  // Edit clicked
  if (isEditOption) {
    const msgDiv = e.target.closest(".details");
    const msgId = msgDiv.getAttribute("data-id");
    const p = msgDiv.querySelector("p");
    const originalText = p.textContent.replace(" (bewerkt)", "").trim();
    currentlyEditingId = msgId;

    const input = document.createElement("input");
    input.type = "text";
    input.className = "edit-inline-input";
    input.value = originalText;
    input.setAttribute("data-original", originalText);

    p.replaceWith(input);
    input.focus();

    isEditingMessage = true;
    stopChatInterval();

    input.addEventListener("keydown", (event) => {
      if (event.key === "Enter") {
        const newText = input.value.trim();
        if (newText && newText !== originalText) {
          fetch("php/edit_msg.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `msg_id=${msgId}&new_msg=${encodeURIComponent(newText)}`
          })
            .then(res => res.text())
            .then(response => {
              if (response === "success") {
                const newP = document.createElement("p");
                newP.innerHTML = newText + ' <small>(bewerkt)</small>';
                input.replaceWith(newP);
                isEditingMessage = false;
                currentlyEditingId = null;
                startChatInterval();
              }
            });
        } else {
          cancelEdit(input, originalText);
        }
      }

      if (event.key === "Escape") {
        cancelEdit(input, originalText);
      }
    });

    setTimeout(() => {
      document.addEventListener("click", function clickOutside(ev) {
        if (!msgDiv.contains(ev.target)) {
          cancelEdit(input, originalText);
          document.removeEventListener("click", clickOutside);
        }
      }, { once: true });
    }, 0);

    document.querySelectorAll(".dropdown-menu").forEach(m => m.classList.add("hidden"));
    isDropdownOpen = false;
    return;
  }

  // Delete clicked
  if (isDeleteOption) {
    const msgDiv = e.target.closest(".details");
    selectedMsgId = msgDiv.getAttribute("data-id");
    document.getElementById("deleteModal").classList.remove("hidden");

    document.querySelectorAll(".dropdown-menu").forEach(m => m.classList.add("hidden"));
    isDropdownOpen = false;
    return;
  }

  // Click outside
  document.querySelectorAll(".dropdown-menu").forEach((menu) => {
    menu.classList.add("hidden");
  });
  isDropdownOpen = false;
});


// emoticon picker

const emojiBtn = document.getElementById('emoji-btn');
const emojiPickerDiv = document.getElementById('emoji-picker');

let picker;

emojiBtn.addEventListener('click', () => {
  // Lazy init
  if (!picker) {
    picker = new EmojiMart.Picker({
      onEmojiSelect: (emoji) => {
        inputField.value += emoji.native;
        inputField.dispatchEvent(new Event("input", { bubbles: true })); 
        emojiPickerDiv.style.display = 'none'; 
      },
    });
    emojiPickerDiv.appendChild(picker);
  }

  emojiPickerDiv.style.display = emojiPickerDiv.style.display === 'none' ? 'block' : 'none';
});

// Optional: Hide picker if clicking elsewhere
document.addEventListener('click', (e) => {
  if (!emojiPickerDiv.contains(e.target) && e.target !== emojiBtn) {
    emojiPickerDiv.style.display = 'none';
  }
});

