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
        // this for debug purposes lang
        console.log("🔧 INIT PUSHER DEBUG:");
        console.log("🔧 User ID received:", userId);
        console.log("🔧 User ID type:", typeof userId);
        console.log("🔧 Token exists:", !!localStorage.getItem("token"));

        // Check if userId is valid
        if (!userId || userId === "undefined" || userId === "null") {
            console.error("❌ Invalid user ID for Pusher:", userId);
            return;
        }

        userId = String(userId).trim();
        console.log("🔧 User ID after conversion:", userId);

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

        //  Listen for vote notifications if meron mag vote
        channel.bind("new.vote", (data) => {
            handleNewVote(data);
        });

        // listen if may mag follow sayo
        channel.bind("user.followed", (data) => {
            handleNewFollower(data);
        });

        // here we notified yung user if they become friends
        channel.bind("became.friends", (data) => {
            handleBecomeFriends(data);
        });

        // Connection events
        // this debug purposes only
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

    const handleNewFollower = (data) => {
        notifications.value.unshift({
            id: Date.now(),
            type: "follow",
            data: data,
            read: false,
            timestamp: new Date(),
        });

        // Show the notification UI
        showFollowNotification(data);
    };

    const handleBecomeFriends = (data) => {
        notifications.value.unshift({
            id: Date.now(),
            type: "Friendship",
            data: data,
            read: false,
            timestamp: new Date(),
        });

        showFriendshipNotification(data);
    };

    const showFriendshipNotification = (data) => {
        if (typeof Swal === "undefined") return;

        const initials = data.friend.name
            .split(" ")
            .map((n) => n[0])
            .join("")
            .toUpperCase();

        const notificationHTML = `
    <div class="modern-toast">
      <div class="toast-body">
        <div class="avatar" style="background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);">
          ${initials}
        </div>
        <div class="toast-info">
          <div class="user-name">${data.friend.name}</div>
          <div class="toast-action">You are now friends!</div>
          <div class="friendship-message">${data.message}</div>
          <div class="timestamp">${new Date().toLocaleTimeString()}</div>
        </div>
        <div class="friends-icon">🤝</div>
      </div>
    </div>
  `;

        Swal.fire({
            html: notificationHTML,
            toast: true,
            position: "top-end",
            width: 400,
            padding: 0,
            background: "rgba(30, 30, 40, 0.7)",
            color: "#fff",
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonText: "View Profile",
            cancelButtonText: "Dismiss",
            timer: 8000,
            timerProgressBar: true,
            showCloseButton: true,
            customClass: {
                popup: "glass-toast friendship-toast",
                confirmButton: "glass-confirm-btn",
                cancelButton: "glass-cancel-btn",
                actions: "glass-actions",
            },
            didOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
            },
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/profile/visit/${data.friend.id}`;
            }
        });

        // Inject global glass styles once
        if (!document.querySelector("#glass-toast-styles")) {
            const style = document.createElement("style");
            style.id = "glass-toast-styles";
            style.textContent = `
      .glass-toast {
        border-radius: 16px !important;
        backdrop-filter: blur(12px) saturate(180%) !important;
        background: rgba(30, 30, 40, 0.75) !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35) !important;
        overflow: hidden;
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      }

      .modern-toast {
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #fff;
      }

      .toast-body {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 14px;
      }

      .avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        color: #fff;
        flex-shrink: 0;
      }

      .toast-info {
        flex: 1;
        min-width: 0;
      }

      .user-name {
        font-weight: 600;
        font-size: 14px;
      }

      .toast-action {
        font-size: 13px;
        opacity: 0.9;
      }

      .timestamp {
        font-size: 11px;
        opacity: 0.6;
        margin-top: 3px;
      }

      .friends-icon {
        font-size: 24px;
        flex-shrink: 0;
        opacity: 0.85;
      }

      .friendship-message {
        font-size: 12px;
        opacity: 0.85;
        font-style: italic;
        margin-top: 3px;
        color: #4ade80;
      }

      .glass-actions {
        padding: 10px 16px !important;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        gap: 8px;
      }

      .glass-confirm-btn,
      .glass-cancel-btn {
        flex: 1;
        border: none;
        border-radius: 8px;
        padding: 8px 14px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
      }

      .glass-confirm-btn {
        background: #22c55e;
        color: white;
      }

      .glass-confirm-btn:hover {
        background: #16a34a;
      }

      .glass-cancel-btn {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
      }

      .glass-cancel-btn:hover {
        background: rgba(255, 255, 255, 0.25);
      }

      .swal2-timer-progress-bar {
        background: linear-gradient(90deg, #4ade80, #22c55e);
      }
    `;
            document.head.appendChild(style);
        }
    };

    const showFollowNotification = (data) => {
        if (typeof Swal === "undefined") return;

        const initials = data.follower.name
            .split(" ")
            .map((n) => n[0])
            .join("")
            .toUpperCase();

        const notificationHTML = `
    <div class="modern-toast">
      <div class="toast-body">
        <div class="avatar" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);">
          ${initials}
        </div>
        <div class="toast-info">
          <div class="user-name">${data.follower.name}</div>
          <div class="toast-action">started following you</div>
          <div class="timestamp">${new Date(
              data.followed_at
          ).toLocaleTimeString()}</div>
        </div>
        <div class="follow-icon">👤</div>
      </div>
    </div>
  `;

        Swal.fire({
            html: notificationHTML,
            toast: true,
            position: "top-end",
            width: 400,
            padding: 0,
            background: "rgba(30, 30, 40, 0.7)",
            color: "#fff",
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonText: "View Profile",
            cancelButtonText: "Dismiss",
            timer: 8000,
            timerProgressBar: true,
            showCloseButton: true,
            customClass: {
                popup: "glass-toast",
                confirmButton: "glass-confirm-btn",
                cancelButton: "glass-cancel-btn",
                actions: "glass-actions",
            },
            didOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
            },
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/profile/visit/${data.follower.id}`;
            }
        });

        // Inject global glass styles (shared across all notifications)
        if (!document.querySelector("#glass-toast-styles")) {
            const style = document.createElement("style");
            style.id = "glass-toast-styles";
            style.textContent = `
      .glass-toast {
        border-radius: 16px !important;
        backdrop-filter: blur(12px) saturate(180%) !important;
        background: rgba(30, 30, 40, 0.75) !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35) !important;
        overflow: hidden;
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      }

      .modern-toast {
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #fff;
      }

      .toast-body {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 14px;
      }

      .avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        color: #fff;
        flex-shrink: 0;
      }

      .toast-info {
        flex: 1;
        min-width: 0;
      }

      .user-name {
        font-weight: 600;
        font-size: 14px;
      }

      .toast-action {
        font-size: 13px;
        opacity: 0.9;
      }

      .timestamp {
        font-size: 11px;
        opacity: 0.6;
        margin-top: 3px;
      }

      .follow-icon {
        font-size: 22px;
        flex-shrink: 0;
        opacity: 0.8;
      }

      .glass-actions {
        padding: 10px 16px !important;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        gap: 8px;
      }

      .glass-confirm-btn,
      .glass-cancel-btn {
        flex: 1;
        border: none;
        border-radius: 8px;
        padding: 8px 14px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
      }

      .glass-confirm-btn {
        background: #4f46e5;
        color: white;
      }

      .glass-confirm-btn:hover {
        background: #4338ca;
      }

      .glass-cancel-btn {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
      }

      .glass-cancel-btn:hover {
        background: rgba(255, 255, 255, 0.25);
      }

      .swal2-timer-progress-bar {
        background: linear-gradient(90deg, #f97316, #fb923c);
      }
    `;
            document.head.appendChild(style);
        }
    };

    const handleNewComment = (data) => {
        notifications.value.unshift({
            id: Date.now(),
            type: "comment",
            data: data,
            read: false,
            timestamp: new Date(),
        });

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
        if (typeof Swal === "undefined") return;

        const initials = data.comment_author_name
            .split(" ")
            .map((n) => n[0])
            .join("")
            .toUpperCase();

        const notificationHTML = `
    <div class="modern-toast">
      <div class="toast-body">
        <div class="avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
          ${initials}
        </div>
        <div class="toast-info">
          <div class="user-name">${data.comment_author_name}</div>
          <div class="toast-action">commented on your post</div>
          <div class="timestamp">${data.created_at}</div>
        </div>
        <div class="comment-icon">💬</div>
      </div>
    </div>
  `;

        Swal.fire({
            html: notificationHTML,
            toast: true,
            position: "top-end",
            width: 400,
            padding: 0,
            background: "rgba(30, 30, 40, 0.7)",
            color: "#fff",
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonText: "View",
            cancelButtonText: "Dismiss",
            timer: 9000,
            timerProgressBar: true,
            showCloseButton: true,
            customClass: {
                popup: "glass-toast",
                confirmButton: "glass-confirm-btn",
                cancelButton: "glass-cancel-btn",
                actions: "glass-actions",
            },
            didOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
            },
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/comments/${data.post_id}`;
            }
        });

        // Inject styles once
        if (!document.querySelector("#modern-toast-styles")) {
            const style = document.createElement("style");
            style.id = "modern-toast-styles";
            style.textContent = `
      .glass-toast {
        border-radius: 16px !important;
        backdrop-filter: blur(12px) saturate(180%) !important;
        background: rgba(30, 30, 40, 0.75) !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35) !important;
        overflow: hidden;
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      }

      .modern-toast {
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #fff;
      }

      .toast-body {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 14px;
      }

      .avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        color: #fff;
        flex-shrink: 0;
      }

      .toast-info {
        flex: 1;
        min-width: 0;
      }

      .user-name {
        font-weight: 600;
        font-size: 14px;
      }

      .toast-action {
        font-size: 13px;
        opacity: 0.9;
      }

      .timestamp {
        font-size: 11px;
        opacity: 0.6;
        margin-top: 3px;
      }

      .comment-icon {
        font-size: 22px;
        flex-shrink: 0;
        opacity: 0.8;
      }

      .glass-actions {
        padding: 10px 16px !important;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        gap: 8px;
      }

      .glass-confirm-btn,
      .glass-cancel-btn {
        flex: 1;
        border: none;
        border-radius: 8px;
        padding: 8px 14px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
      }

      .glass-confirm-btn {
        background: #4f46e5;
        color: white;
      }

      .glass-confirm-btn:hover {
        background: #4338ca;
      }

      .glass-cancel-btn {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
      }

      .glass-cancel-btn:hover {
        background: rgba(255, 255, 255, 0.25);
      }

      .swal2-timer-progress-bar {
        background: linear-gradient(90deg, #60a5fa, #818cf8);
      }
    `;
            document.head.appendChild(style);
        }
    };

    const handleNewVote = (data) => {
        notifications.value.unshift({
            id: Date.now(),
            type: "vote",
            data: data,
            read: false,
            timestamp: new Date(),
        });

        // Then call the showVoteNotification function to display the notification UI
        showVoteNotification(data);
    };

    const showVoteNotification = (data) => {
        if (typeof Swal === "undefined") return;

        const voteConfig = {
            upvote: {
                icon: "👍",
                action: "upvoted",
                color: "#4ade80",
                gradient: "linear-gradient(135deg, #42a5f5 0%, #478ed1 100%)",
            },
            downvote: {
                icon: "👎",
                action: "downvoted",
                color: "#f87171",
                gradient: "linear-gradient(135deg, #ef5350 0%, #e53935 100%)",
            },
        };

        const config = voteConfig[data.vote_type] || voteConfig.upvote;

        const initials = data.voter_name
            .split(" ")
            .map((n) => n[0])
            .join("")
            .toUpperCase();

        const notificationHTML = `
    <div class="modern-toast">
      <div class="toast-body">
        <div class="avatar" style="background: ${config.gradient};">
          ${initials}
        </div>
        <div class="toast-info">
          <div class="user-name">${data.voter_name}</div>
          <div class="toast-action">${config.action} your post</div>
          <div class="post-title">"${data.post_title}"</div>
          <div class="timestamp">${data.created_at}</div>
        </div>
        <div class="vote-icon">${config.icon}</div>
      </div>
    </div>
  `;

        Swal.fire({
            html: notificationHTML,
            toast: true,
            position: "top-end",
            width: 400,
            padding: 0,
            background: "rgba(30, 30, 40, 0.7)",
            color: "#fff",
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonText: "View",
            cancelButtonText: "Dismiss",
            timer: 7000,
            timerProgressBar: true,
            showCloseButton: true,
            customClass: {
                popup: "glass-toast",
                confirmButton: "glass-confirm-btn",
                cancelButton: "glass-cancel-btn",
                actions: "glass-actions",
            },
            didOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
            },
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/comments/${data.post_id}`;
            }
        });

        // Inject styles once
        if (!document.querySelector("#modern-toast-styles")) {
            const style = document.createElement("style");
            style.id = "modern-toast-styles";
            style.textContent = `
      .glass-toast {
        border-radius: 16px !important;
        backdrop-filter: blur(12px) saturate(160%) !important;
        background: rgba(30, 30, 40, 0.75) !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35) !important;
        overflow: hidden;
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      }

      .modern-toast {
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #fff;
      }

      .toast-body {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 14px;
      }

      .avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        color: #fff;
        flex-shrink: 0;
      }

      .toast-info {
        flex: 1;
        min-width: 0;
      }

      .user-name {
        font-weight: 600;
        font-size: 14px;
      }

      .toast-action {
        font-size: 13px;
        opacity: 0.9;
      }

      .post-title {
        font-size: 12px;
        opacity: 0.8;
        font-style: italic;
        margin-top: 4px;
      }

      .timestamp {
        font-size: 11px;
        opacity: 0.6;
        margin-top: 2px;
      }

      .vote-icon {
        font-size: 22px;
        flex-shrink: 0;
      }

      .glass-actions {
        padding: 10px 16px !important;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        gap: 8px;
      }

      .glass-confirm-btn,
      .glass-cancel-btn {
        flex: 1;
        border: none;
        border-radius: 8px;
        padding: 8px 14px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
      }

      .glass-confirm-btn {
        background: #4f46e5;
        color: white;
      }

      .glass-confirm-btn:hover {
        background: #4338ca;
      }

      .glass-cancel-btn {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
      }

      .glass-cancel-btn:hover {
        background: rgba(255, 255, 255, 0.25);
      }

      .swal2-timer-progress-bar {
        background: linear-gradient(90deg, #4ade80, #60a5fa);
      }
    `;
            document.head.appendChild(style);
        }
    };

    // ... rest of your existing code ...

    return {
        pusher,
        isConnected,
        notifications,
        initPusher,
        disconnect,
        markAsRead,
        handleNewFollower,
        handleBecomeFriends,
        showFollowNotification,
        showVoteNotification,
        showFriendshipNotification,
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
