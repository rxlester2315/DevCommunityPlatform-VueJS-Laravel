# 🧑‍💻 DevFeed – Social Media Platform for Devs

A real-time social media web app built with **Laravel**, **Vue 3**, and **Pusher**, providing instant notifications when users interact with posts (e.g., comments, upvotes, downvotes).

---

## 🚀 Features

-   🧩 **SPA (Single Page Application)** using Vue 3 Composition API
-   🔐 **Token-based authentication** (Laravel Sanctum / JWT)
-   🧱 **MVC architecture** (Laravel backend)
-   💬 **Real-time notifications** using **Pusher**
-   💅 **Modern UI** with **TailwindCSS** + **SweetAlert2**
-   🔑 **Custom login** + **Google OAuth login**
-   🗄️ **Activity logs** for tracking user sessions

---

## 🏗️ System Architecture

| Layer                 | Description                                                           |
| --------------------- | --------------------------------------------------------------------- |
| **Backend (Laravel)** | Handles authentication, events, and broadcasting                      |
| **Frontend (Vue.js)** | Listens for real-time events and updates UI dynamically               |
| **Pusher**            | Acts as the broker between backend and frontend for real-time updates |

---

## 🔄 Data Flow

1. User registers or logs in (Custom or Google OAuth).
2. User performs actions like posting, commenting, upvoting, or downvoting.
3. Laravel triggers a **broadcast event** through **Pusher**.
4. Vue frontend **listens on private channels** and shows a live notification instantly.

---

## 🧰 Tech Stack

**Frontend**

-   Vue 3 (Composition API)
-   Axios
-   TailwindCSS
-   SweetAlert2

**Backend**

-   Laravel 10+
-   Laravel Sanctum / Passport
-   Socialite (Google Login)
-   Pusher Channels

**Database**

-   MySQL

---

## ⚙️ Installation

### Prerequisites

-   PHP 8.2+
-   Composer
-   Node.js 18+
-   MySQL
-   Pusher account

---

## 👨‍💻 Author

**Rex Lester Bastaoang**  
📧 @rexlesterbastaoang2002@gmail.com
🌐 https://rxlesterdevv.ct.ws/
💼 https://www.linkedin.com/in/rex-lester-bastaoang-300400294/
