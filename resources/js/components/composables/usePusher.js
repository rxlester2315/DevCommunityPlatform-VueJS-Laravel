import { ref, onUnmounted } from "vue";
import Pusher from "pusher-js";
import axios from "axios";
import Swal from "sweetalert2";

export function usePusher() {
    const pusher = ref(null);
    const isConnected = ref(false);
    const notifications = ref([]);

    // by this u can call this inside of your vue components or reusable composables

    // function wherein pusher connection with logged in user id is established
    const initPusher = (userId) => {
        // here checking lang tayo if pusher is already connected, if so disconnect first bago create ng new connection
        if (pusher.value) {
            pusher.value.disconnect();
        }
        // now here gawa na tayo ng bagong pusher connection
        pusher.value = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
            // here is where we enable TLS for secure connection communication between front-end and pusher server
            forceTLS: true,
            // eto is yung ginawa natin dun sa Broadcasting Auth sa echo.js para ma authorize yung private channels
            authEndpoint: "/broadcasting/auth",
            auth: {
                // making sure na may authorization header tayo para ma authenticate yung user
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("token")}`,
                    "X-CSRF-TOKEN":
                        document.querySelector('meta[name="csrf-token"]')
                            ?.content || "",
                },
            },
        });
        // eto is yung channel wherein mag subscribe tayo gamit yung user id para sa private user channel eto yung nasa event class na broadcastOn
        const channel = pusher.value.subscribe(`private-user.${userId}`);

        // here is yung event is going to run once may mag comment sa post  yung new.comment is yan yung nasa broadcastAs sa event class
        channel.bind("new.comment", (data) => {
            handleNewComment(data);
        });

        // Connection events
        pusher.value.connection.bind("connected", () => {
            isConnected.value = true;
            console.log("✅ Pusher connected");
        });

        pusher.value.connection.bind("disconnected", () => {
            isConnected.value = false;
            console.log("🔴 Pusher disconnected");
        });

        return pusher.value;
    };
    // this function will handle new comment notifications
    const handleNewComment = (data) => {
        notifications.value.unshift({
            id: Date.now(),
            type: "comment",
            data: data,
            read: false,
            timestamp: new Date(),
        });

        // then call yung showNotification function para display yung notifcation ui
        showNotification(data);
    };
    // eto function na to is mag di display ng notification gamit yung Swal.fire ex.John Doe commented on your post: "Great post!"

    // function to mark notification as read for future use in the notification dropdown
    const markAsRead = (notificationId) => {
        const notification = notifications.value.find(
            (n) => n.id === notificationId
        );
        if (notification) {
            notification.read = true;
        }
    };
    // function to disconnect pusher connection
    const disconnect = () => {
        if (pusher.value) {
            pusher.value.disconnect();
            pusher.value = null;
            isConnected.value = false;
        }
    };

    const showNotification = (data) => {
        if (typeof Swal !== "undefined") {
            // Create custom HTML for Facebook-style notification
            const notificationHTML = `
            <div class="facebook-toast">
                <div class="toast-header">
                    <div class="user-avatar">
                        ${data.comment_author_name
                            .split(" ")
                            .map((n) => n[0])
                            .join("")
                            .toUpperCase()}
                    </div>
                    <div class="toast-content">
                        <div class="user-name">${data.comment_author_name}</div>
                        <div class="action-text">commented on your post</div>
                        <div class="timestamp">${data.created_at}</div>
                    </div>
                </div>
              
            </div>
        `;

            Swal.fire({
                html: notificationHTML,
                showConfirmButton: true,
                showCancelButton: true,
                confirmButtonText: "<span>View</span>",
                cancelButtonText: "<span>✕ Dismiss</span>",
                toast: true,
                position: "top-end",
                width: 420,
                padding: "0",
                background: "#ffffff",
                customClass: {
                    popup: "modern-facebook-toast",
                    confirmButton: "modern-confirm-btn",
                    cancelButton: "modern-cancel-btn",
                    actions: "modern-actions-container",
                },
                timer: 10000,
                showCloseButton: true,
                closeButtonHtml: `
                <div class="close-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </div>
            `,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/comments/${data.post_id}`;
                }
            });

            if (!document.querySelector("#facebook-toast-styles")) {
                const style = document.createElement("style");
                style.id = "facebook-toast-styles";
                style.textContent = `
                .modern-facebook-toast {
                    border-radius: 12px;
                    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15), 0 2px 8px rgba(0, 0, 0, 0.1);
                    border: 1px solid #e4e6ea;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                }
                
                .facebook-toast {
                    padding: 16px;
                }
                
                .toast-header {
                    display: flex;
                    align-items: flex-start;
                    gap: 12px;
                    margin-bottom: 12px;
                }
                
                .user-avatar {
                    width: 44px;
                    height: 44px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-weight: 700;
                    font-size: 15px;
                    flex-shrink: 0;
                }
                
                .toast-content {
                    flex: 1;
                    min-width: 0;
                }
                
                .user-name {
                    font-weight: 700;
                    color: #1c1e21;
                    font-size: 15px;
                    margin-bottom: 2px;
                }
                
                .action-text {
                    color: #65676b;
                    font-size: 14px;
                    margin-bottom: 2px;
                }
                
                .timestamp {
                    color: #8a8d91;
                    font-size: 12px;
                }
                
               
                
                .comment-text {
                    color: #1c1e21;
                    font-size: 13px;
                    line-height: 1.4;
                    font-style: italic;
                }
                
                .modern-actions-container {
                    margin: 0;
                    padding: 12px 16px;
                    border-top: 1px solid #e4e6ea;
                    gap: 8px;
                }
                
                .modern-confirm-btn, .modern-cancel-btn {
                    flex: 1;
                    padding: 8px 16px;
                    border-radius: 6px;
                    font-weight: 600;
                    font-size: 13px;
                    border: none;
                    cursor: pointer;
                    transition: all 0.2s;
                }
                
                .modern-confirm-btn {
                    background: #1877f2;
                    color: white;
                }
                
                .modern-confirm-btn:hover {
                    background: #166fe5;
                }
                
                .modern-cancel-btn {
                    background: #e4e6ea;
                    color: #1c1e21;
                }
                
                .modern-cancel-btn:hover {
                    background: #d8dadf;
                }
                
                .close-btn {
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #65676b;
                    transition: background-color 0.2s;
                    cursor: pointer;
                }
                
                .close-btn:hover {
                    background: #f0f2f5;
                }
                
                .swal2-timer-progress-bar {
                    background: #1877f2;
                }
            `;
                document.head.appendChild(style);
            }
        }
    };

    // Auto cleanup
    onUnmounted(() => {
        disconnect();
    });

    return {
        pusher,
        isConnected,
        notifications,
        initPusher,
        disconnect,
        markAsRead,
    };
}
