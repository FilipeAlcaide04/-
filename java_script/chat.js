$(document).ready(function () {
    function loadMessages() {
    $.get("load_messages.php", function (data) {
        let messages = JSON.parse(data);
        let chatBox = $("#chat-box");

        let isAtBottom = chatBox[0].scrollHeight - chatBox.scrollTop() === chatBox.outerHeight();
        
        let existingMessages = new Map();
        $(".message-container").each(function () {
            existingMessages.set($(this).attr("data-id"), $(this));
        });

        let newMessageIds = new Set();
        
        messages.forEach(msg => {
            newMessageIds.add(msg.id);
            
            if (!existingMessages.has(msg.id)) { // Apenas mensagens novas
                let isMine = msg.user === currentUser;
                let messageClass = isMine ? "my-message" : "other-message";
                let usernameClass = isMine ? "my-username" : "username";

                let messageContent = msg.message;
                if (msg.type === "image") {
                    messageContent = `<img src="uploads_chat/${msg.message}" class="chat-media" onclick="openImage(this.src)">`;
                } else if (msg.type === "video") {
                    messageContent = `<video controls class="chat-media"><source src="uploads_chat/${msg.message}" type="video/mp4"></video>`;
                }

                let messageElement = `
                    <div class="message-container ${messageClass}" data-id="${msg.id}">
                        <div class="message">
                            <strong class="${usernameClass}">${msg.user}:</strong>
                            <span class="text">${messageContent}</span>
                            <small class="timestamp">${formatTimestamp(msg.timestamp)}</small>
                        </div>
                    </div>`;
                chatBox.append(messageElement);
            }
        });

        existingMessages.forEach((element, id) => {
            if (!newMessageIds.has(id)) {
                element.remove();
            }
        });

        if (isAtBottom) {
            chatBox.scrollTop(chatBox[0].scrollHeight);
        }
    
    }).fail(function () {
        console.log("Erro ao carregar mensagens.");
    });
}
    function sendMessage() {
        let message = $("#message").val().trim();
        let fileInput = $("#file-input")[0].files[0];

        if (fileInput) {
            let formData = new FormData();
            formData.append("file", fileInput);

            $.ajax({
                url: "upload_file.php",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.trim() !== "error") {
                        $("#file-input").val("");
                        loadMessages();
                    } else {
                        alert("Erro ao enviar arquivo.");
                    }
                }
            });
        } else if (message) {
            $.post("send_message.php", { message: message }, function (response) {
                if (response.trim() === "success") {
                    $("#message").val("");
                    loadMessages();
                } else {
                    console.log("Erro ao enviar mensagem.");
                    $("#message").val("");
                    loadMessages();
                }
            }).fail(function () {
                alert("Erro ao enviar mensagem.");
            });
        }
    }

    $("#send-btn").click(function () {
        sendMessage();
    });

    $("#message").keypress(function (e) {
        if (e.which === 13) {
            e.preventDefault();
            sendMessage();
        }
    });

    function formatTimestamp(timestamp) {
        let date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    setInterval(loadMessages, 500);
    loadMessages();
});
