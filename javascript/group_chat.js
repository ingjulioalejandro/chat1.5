console.log("group_chat.js loaded");

document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded");

    const form = document.querySelector(".typing-area");
    const chatBox = document.querySelector(".chat-box");
    const deleteGroupBtn = document.querySelector("#deleteGroup");

    if (form) {
        const inputField = form.querySelector(".input-field");
        const sendBtn = form.querySelector("button");
        const attachmentInput = form.querySelector("#attachment");

        let attachmentPreview = form.querySelector(".attachment-preview");
        if (!attachmentPreview) {
            attachmentPreview = document.createElement("div");
            attachmentPreview.className = "attachment-preview";
            form.insertBefore(attachmentPreview, sendBtn);
        }

        form.onsubmit = (e) => {
            e.preventDefault();
        }

        if (inputField) {
            inputField.focus();
            inputField.onkeyup = () => {
                if(inputField.value != "" || (attachmentInput && attachmentInput.files.length > 0)){
                    sendBtn.classList.add("active");
                } else {
                    sendBtn.classList.remove("active");
                }
            }
        }

        if (sendBtn) {
            sendBtn.onclick = () => {
                let formData = new FormData(form);
                formData.append('is_group', 'true');
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "php/insert-chat.php", true);
                xhr.onload = () => {
                    if(xhr.readyState === XMLHttpRequest.DONE){
                        if(xhr.status === 200){
                            inputField.value = "";
                            if (attachmentInput) attachmentInput.value = "";
                            if (attachmentPreview) attachmentPreview.innerHTML = "";
                            scrollToBottom();
                        }
                    }
                }
                xhr.send(formData);
            }
        }

        if (attachmentInput) {
            attachmentInput.onchange = () => {
                const file = attachmentInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        let fileIcon = getFileIcon(file.name.split('.').pop().toLowerCase());
                        attachmentPreview.innerHTML = `
                            <div class="attachment-item">
                                <i class="${fileIcon}"></i>
                                <span>${file.name}</span>
                                <button type="button" class="remove-attachment">&times;</button>
                            </div>
                        `;
                        const removeBtn = attachmentPreview.querySelector(".remove-attachment");
                        removeBtn.onclick = () => {
                            attachmentInput.value = "";
                            attachmentPreview.innerHTML = "";
                            if(inputField.value == ""){
                                sendBtn.classList.remove("active");
                            }
                        }
                    }
                    reader.readAsDataURL(file);
                }
                if(inputField.value != "" || attachmentInput.files.length > 0){
                    sendBtn.classList.add("active");
                }
            }
        }
    }

    if (chatBox) {
        chatBox.onmouseenter = () => {
            chatBox.classList.add("active");
        }

        chatBox.onmouseleave = () => {
            chatBox.classList.remove("active");
        }

        setInterval(() =>{
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "php/get-chat.php", true);
            xhr.onload = () => {
                if(xhr.readyState === XMLHttpRequest.DONE){
                    if(xhr.status === 200){
                        let data = xhr.response;
                        chatBox.innerHTML = data;
                        if(!chatBox.classList.contains("active")){
                            scrollToBottom();
                        }
                    }
                }
            }
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("incoming_id=" + roomId + "&is_group=true");
        }, 500);
    }

    if (deleteGroupBtn) {
        console.log("Delete button found:", deleteGroupBtn);
        deleteGroupBtn.addEventListener('click', function(e) {
            console.log("Delete button clicked");
            e.preventDefault();
            const groupId = this.getAttribute('data-group-id');
            console.log("Group ID:", groupId);
            
            if (confirm("Are you sure you want to delete this group? This action cannot be undone.")) {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "php/delete_group.php", true);
                xhr.onload = () => {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            console.log("Server response:", xhr.responseText);
                            try {
                                let data = JSON.parse(xhr.response);
                                if (data.status === "success") {
                                    alert(data.message);
                                    window.location.href = "users.php";
                                } else {
                                    alert(data.message);
                                }
                            } catch (e) {
                                console.error("Error parsing JSON:", e);
                                alert("An error occurred while processing the response.");
                            }
                        } else {
                            alert("An error occurred. Please try again.");
                        }
                    }
                }
                xhr.onerror = () => {
                    alert("An error occurred. Please check your connection and try again.");
                }
                xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhr.send("group_id=" + groupId);
            }
        });
    } else {
        console.log("Delete button not found");
    }
});

function scrollToBottom(){
    const chatBox = document.querySelector(".chat-box");
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
}

function getFileIcon(extension) {
    const iconClasses = {
        'pdf': 'far fa-file-pdf',
        'doc': 'far fa-file-word',
        'docx': 'far fa-file-word',
        'xls': 'far fa-file-excel',
        'xlsx': 'far fa-file-excel',
        'txt': 'far fa-file-alt',
        'csv': 'far fa-file-csv',
        'zip': 'far fa-file-archive',
        'rar': 'far fa-file-archive',
        'mp3': 'far fa-file-audio',
        'mp4': 'far fa-file-video',
        'avi': 'far fa-file-video',
        'mov': 'far fa-file-video',
        'jpg': 'far fa-file-image',
        'jpeg': 'far fa-file-image',
        'png': 'far fa-file-image',
        'gif': 'far fa-file-image'
    };

    return iconClasses[extension] || 'far fa-file';
}
