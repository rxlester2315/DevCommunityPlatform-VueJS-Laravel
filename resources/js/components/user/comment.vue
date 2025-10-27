<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";
import { auth } from "../../utils/auth";

const route = useRoute();
const router = useRouter();
const user = ref(null);

const post = ref(null);
const comments = ref([]);
const newComment = ref("");
const isLoading = ref(false);
const loading = ref(true);
const profilePic = ref(null);
const imageSelect = ref(null);

const isDropdownVisible = ref(false);
function toggleDropdown() {
    isDropdownVisible.value = !isDropdownVisible.value;
}

async function fetchProfilePic() {
    try {
        const response = await axios.get("/api/profile/photo");
        profilePic.value = response.data.photo_profile;
    } catch (error) {
        console.error("Error fetching profile picture:", error);
    }
}

async function submitComment() {
    if (!newComment.value.trim()) {
        await Swal.fire({
            icon: "warning",
            title: "Warning",
            text: "Comment cannot be empty.",
            timer: 3000,
            showConfirmButton: true,
        });

        return;
    }

    if (imageSelect.value && imageSelect.value.files.length > 0) {
        const file = imageSelect.value.files[0];
        const allowedTypes = ["image/jpeg", "image/png", "image/gif"];
        const MaxSize = 2 * 1024 * 1024; // 2MB

        if (!allowedTypes.includes(file.type) || file.size > MaxSize) {
            await Swal.fire({
                icon: "warning",
                title: "Warning",
                text: "Invalid image type or size exceeds 2MB.",
                timer: 3000,
                showConfirmButton: true,
            });
            return;
        }
    }

    isLoading.value = true;
    try {
        const formData = new FormData();

        formData.append("post_id", postId.value);
        formData.append("content", newComment.value.trim());

        if (imageSelect.value && imageSelect.value.files.length > 0) {
            formData.append("image", imageSelect.value.files[0]);
        }

        const response = await axios.post(
            "/api/post/submit-comment",
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            }
        );

        Swal.fire({
            icon: "success",
            title: "Success",
            text: "Comment posted successfully.",
            timer: 3000,
            showConfirmButton: true,
        });
        comments.value.unshift(response.data.comment);
        newComment.value = "";
        imageSelect.value.value = null;
    } catch (error) {
        console.error("Error posting comment with image:", error);
    } finally {
        isLoading.value = false;
    }
}

const logout = async () => {
    try {
        await auth.logout();
    } catch (error) {
        console.error("There's something wrong", error);

        let errorMessage = "Failed to logout. Please Try Again";

        if (error.response?.data?.errors) {
            const errors = error.response.data.errors;
            errorMessage = Object.values(errors).flat().join(" ");
        } else if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        }

        await Swal.fire({
            icon: "error",
            title: "Failed!",
            text: errorMessage,
            timer: 5000,
            showConfirmButton: true,
        });

        auth.clearAuth();
        window.location.replace("/login");
    }
};

async function loadUserData() {
    try {
        const userData = auth.getUser();
        console.log("User data from auth:", userData);

        if (userData) {
            user.value = userData;
        } else {
            const response = await axios.get("/api/user");
            user.value = response.data;
            console.log("User data from API:", response.data);
        }
    } catch (error) {
        console.error("Error loading user data:", error);
        user.value = null;
    }
}

const postId = ref(route.params.id);

const fetchPost = async () => {
    try {
        const response = await axios.get(`/api/post/view/${postId.value}`);
        post.value = response.data.post;
    } catch (error) {
        console.error("Error fetching post:", error);
    }
};

const fetchComments = async () => {
    try {
        loading.value = true;
        // Use query parameter instead of URL parameter
        const response = await axios.get(
            `/api/post/comments?post_id=${postId.value}`
        );
        comments.value = response.data.comments;
    } catch (error) {
        console.error("Error fetching comments:", error);
    } finally {
        loading.value = false;
    }
};

const getUserInitials = (user) => {
    if (!user) return "U";

    if (user.name) {
        const names = user.name.split(" ");
        let initials = "";

        if (names[0]) {
            initials += names[0][0].toUpperCase();
        }

        if (names[1]) {
            initials += names[1][0].toUpperCase();
        }

        return initials;
    }

    if (user.username) {
        return user.username.substring(0, 2).toUpperCase();
    }

    return "U";
};

const formatTimeAgo = (date) => {
    const now = new Date();
    const postDate = new Date(date);
    const diffInSeconds = Math.floor((now - postDate) / 1000);

    if (diffInSeconds < 60) return "just now";
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
    if (diffInSeconds < 86400)
        return `${Math.floor(diffInSeconds / 3600)}h ago`;
    return `${Math.floor(diffInSeconds / 86400)}d ago`;
};
const userInitials = computed(() => {
    if (!user.value) return "GU";

    if (user.value.name) {
        return user.value.name
            .split(" ")
            .map((n) => n[0])
            .join("")
            .toUpperCase()
            .substring(0, 2);
    }

    return user.value.email
        ? user.value.email.substring(0, 2).toUpperCase()
        : "GU";
});

onMounted(async () => {
    await fetchPost();
    await fetchComments();
    loadUserData();
    fetchProfilePic();
});
const backToHome = () => {
    router.push("/home");
};
</script>

<template>
    <body class="min-h-screen">
        <!-- Header -->
        <header
            class="sticky top-0 z-50 bg-black/95 backdrop-blur-sm border-b border-zinc-800"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-8">
                        <a
                            href="index.html"
                            class="text-xl font-bold text-white hover:text-orange-500 transition-colors"
                        >
                            <i class="fas fa-code mr-2"></i>DevFeed
                        </a>
                        <nav class="hidden md:flex items-center gap-6">
                            <button
                                @click="backToHome"
                                class="text-zinc-400 hover:text-white transition-colors"
                            >
                                Feed
                            </button>
                            <a
                                href="#"
                                class="text-zinc-400 hover:text-white transition-colors"
                                >Explore</a
                            >
                            <a
                                href="#"
                                class="text-zinc-400 hover:text-white transition-colors"
                                >Communities</a
                            >
                        </nav>
                    </div>
                    <div class="flex items-center gap-4">
                        <button
                            class="p-2 text-zinc-400 hover:text-white transition-colors"
                        >
                            <i class="fas fa-bell"></i>
                        </button>
                        <button
                            class="p-2 text-zinc-400 hover:text-white transition-colors"
                        >
                            <i class="fas fa-envelope"></i>
                        </button>
                        <div class="relative inline-block text-left">
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-orange-500 to-red-500 rounded-full flex items-center justify-center text-white text-sm font-medium cursor-pointer"
                                @click="toggleDropdown"
                            >
                                <img
                                    v-if="profilePic"
                                    :src="`/storage/${profilePic}`"
                                    alt="User Profile Picture"
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-semibold bg-blue-500 overflow-hidden border-2 border-orange-500"
                                />

                                <div
                                    v-else
                                    class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-full flex items-center justify-center text-white text-sm font-medium"
                                >
                                    {{ userInitials }}
                                </div>
                            </div>

                            <div
                                v-show="isDropdownVisible"
                                class="absolute left-30 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg"
                            >
                                <div class="py-1">
                                    <a
                                        @click="goToProfile"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer"
                                    >
                                        Profile
                                    </a>

                                    <a
                                        href="#"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >Settings</a
                                    >
                                    <a
                                        href="#"
                                        @click="logout"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >Logout</a
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Back Button -->
            <button
                @click="backToHome"
                class="flex items-center gap-2 text-zinc-400 hover:text-white mb-6 transition-colors"
            >
                <i class="fas fa-arrow-left"></i>
                <span>Back to Feed</span>
            </button>

            <!-- Post Content -->
            <div
                v-if="post"
                class="bg-gray-900 border border-gray-800 rounded-lg p-6 mb-6"
            >
                <div class="flex items-center gap-3 mb-4">
                    <!-- User Avatar with Profile Picture or Initials -->
                    <div
                        class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-semibold overflow-hidden"
                        :class="{
                            'bg-blue-500': !post.user?.profile?.photo_profile,
                        }"
                    >
                        <!-- Show profile picture if exists -->
                        <img
                            v-if="post.user?.profile?.photo_profile"
                            :src="'/storage/' + post.user.profile.photo_profile"
                            :alt="post.user?.name || post.user?.username"
                            class="w-full h-full object-cover"
                        />
                        <!-- Show initials if no profile picture -->
                        <span v-else>{{ getUserInitials(post.user) }}</span>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold">
                            {{ post.user?.name || post.user?.username }}
                        </h3>
                        <p class="text-zinc-400 text-sm">
                            {{ formatTimeAgo(post.created_at) }}
                        </p>
                    </div>
                </div>

                <!-- Post Title -->
                <h1 class="text-2xl font-bold text-white mb-3">
                    {{ post.title_post }}
                </h1>

                <!-- Post Content -->
                <p class="text-gray-300 mb-4 leading-relaxed">
                    {{ post.text_content }}
                </p>

                <!-- Post Image -->
                <div v-if="post.image" class="mb-4">
                    <img
                        :src="'/storage/' + post.image"
                        :alt="post.title_post"
                        class="w-full max-h-96 object-cover rounded-lg"
                    />
                </div>

                <!-- Post Stats -->
                <div class="flex items-center gap-4 text-zinc-400 text-sm">
                    <div class="flex items-center gap-1">
                        <i class="fas fa-comment"></i>
                        <span
                            >{{
                                post.comments_count || comments.length
                            }}
                            comments</span
                        >
                    </div>
                </div>
            </div>

            <!-- Comment Form -->

            <!-- Comments List -->
            <div class="space-y-4 mb-6">
                <h3 class="text-white font-semibold mb-4">
                    Comments ({{ comments.length }})
                </h3>

                <!-- Comments -->
                <div
                    v-for="comment in comments"
                    :key="comment.id"
                    class="bg-gray-900 border border-gray-800 rounded-lg p-4"
                >
                    <div class="flex items-start gap-3">
                        <!-- User Avatar -->
                        <div
                            class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-semibold"
                        >
                            <span>{{ getUserInitials(comment.user) }}</span>
                        </div>

                        <!-- Comment Content -->
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-white font-medium">
                                    {{
                                        comment.user?.name ||
                                        comment.user?.username
                                    }}
                                </span>
                                <span class="text-zinc-400 text-sm">
                                    {{ formatTimeAgo(comment.created_at) }}
                                </span>
                            </div>
                            <p class="text-gray-300">{{ comment.content }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 mb-6">
                <h3 class="text-white font-semibold mb-4">Add a comment</h3>
                <textarea
                    v-model="newComment"
                    placeholder="Write your comment..."
                    class="w-full h-24 bg-gray-800 border border-gray-700 rounded-lg p-4 text-white placeholder-gray-400 resize-none focus:outline-none focus:border-blue-500"
                ></textarea>
                <button
                    @click="submitComment"
                    :disabled="!newComment.trim() || isLoading"
                    class="mt-3 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors"
                >
                    <span v-if="isLoading">Posting...</span>
                    <span v-else>Post Comment</span>
                </button>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-black border-t border-zinc-800 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="text-center text-zinc-400 text-sm">
                    <p>&copy; 2025 DevFeed. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
</template>
