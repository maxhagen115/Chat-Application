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
