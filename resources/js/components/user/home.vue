<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import axios from "axios";
import { useRoute, useRouter } from "vue-router";
import Swal from "sweetalert2";
import { auth } from "../../utils/auth";
import "remixicon/fonts/remixicon.css";
import KarmaVoter from "../user/KarmaVoter.vue";

// Reactive Variables / Reactive State
const router = useRouter();
const isLoadingPosts = ref(false);
const isSettingVisibile = ref(false);
const profilePic = ref(null);
const isEditModalVisible = ref(false);
const editingPost = ref(null);
const editPostContent = ref("");
const editPostTitle = ref("");
const editCategPost = ref("");
const editSelectImage = ref(null);
const editImagePreview = ref(null);
const isDropdownVisible = ref(false);
const isCreatePostModalVisible = ref(false);
const postContent = ref("");

const categoryPost = ref("");
const posts = ref([]);
const postTitle = ref("");
const selectedImage = ref(null);
const imagePreview = ref(null);
const isLoading = ref(false);
const user = ref(null);
const total_comments = ref(0);
const currentUserId = ref(null);
const loadingFriends = ref(false);
const friends = ref([]);
const totalFriends = ref(0);
const onlineFriendsCount = ref(0);

const currentUser = computed(() => auth.getUser()); // ✅ Correct - calls the function

console.log("HERE THE DATA", currentUser.value); // ✅ Use .value to get the actual data

function onPostVoted(voteData) {
    const postIndex = posts.value.findIndex(
        (post) => post.id === voteData.postId
    );
    if (postIndex !== -1) {
        posts.value[postIndex].karma_score = voteData.score;
        posts.value[postIndex].user_vote = voteData.userVote;
    }
}

const fetchFriends = async () => {
    loadingFriends.value = true;
    try {
        const currentUser = auth.getUser();
        const userId = currentUser?.id;

        if (!userId) {
            console.error("❌ No user ID found - user not authenticated");
            friends.value = [];
            return;
        }

        console.log("👤 Current user ID:", userId);

        // Try different endpoints - use the one that works for you
        const response = await axios.get(`/api/friends/${userId}/list`);
        // OR: const response = await axios.get('/api/friends/my-list');
        // OR: const response = await axios.get(`/api/friends/${route.params.id}/list`);

        console.log("📦 Friends API Response:", response.data);

        if (response.data.success) {
            friends.value =
                response.data.data.friends.data ||
                response.data.data.friends ||
                [];
            totalFriends.value = response.data.data.total_friends || 0;
            onlineFriendsCount.value =
                response.data.data.online_friends_count || 0;

            console.log("👥 Friends data:", friends.value);
            console.log("📊 Total friends:", totalFriends.value);
        } else {
            console.log("❌ API returned success: false");
        }
    } catch (error) {
        console.error("❌ Error fetching friends:", error);
        console.log("🔍 Error response:", error.response?.data);
        friends.value = [];
    } finally {
        loadingFriends.value = false;
    }
};

const getFriendInitials = (name) => {
    if (!name) return "F";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .slice(0, 2);
};

const formatLastSeen = (lastSeen) => {
    if (!lastSeen) return "Offline";

    const lastSeenDate = new Date(lastSeen);
    const now = new Date();
    const diffInMinutes = Math.floor((now - lastSeenDate) / (1000 * 60));

    if (diffInMinutes < 1) return "Just now";
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
    return `${Math.floor(diffInMinutes / 1440)}d ago`;
};

const formatFriendsSince = (friendsSince) => {
    if (!friendsSince) return "";

    const date = new Date(friendsSince);
    const now = new Date();
    const diffInDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));

    if (diffInDays < 1) return "Today";
    if (diffInDays < 30) return `${diffInDays}d`;
    if (diffInDays < 365) return `${Math.floor(diffInDays / 30)}mo`;
    return `${Math.floor(diffInDays / 365)}y`;
};

async function fetchPosts() {
    isLoadingPosts.value = true;

    try {
        const response = await axios.get("/api/posts");

        posts.value = response.data.posts || response.data;
    } catch (error) {
        console.error("Error Getting posts:", error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error to load the data",
            timer: 3000,
            showConfirmButton: true,
        });
    } finally {
        isLoadingPosts.value = false;
    }
}

async function fetchProfilePic() {
    try {
        const response = await axios.get("/api/profile/photo");
        profilePic.value = response.data.photo_profile;
    } catch (error) {
        console.error("Error fetching profile picture:", error);
    }
}

async function totalComments(postId) {
    try {
        const response = await axios.get(`/api/post/comments`, {
            params: { post_id: postId },
        });
        return response.data.total_comments || 0;
    } catch (error) {
        console.error("Error fetching total comments:", error);
        return 0;
    }
}

async function deletePost($postId) {
    const result = await Swal.fire({
        icon: "warning",
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonText: "Yes, Delete it",
        cancelButtonText: "Cancel",
    });

    if (!result.isConfirmed) return;

    try {
        const response = await axios.delete(`/api/posts/${$postId}`);

        await Swal.fire({
            icon: "success",
            title: "Deleted!!",
            text: "Your post has been deleted",
            timer: 2000,
            showConfirmButton: true,
        });

        await fetchPosts();
    } catch (error) {
        console.error("Error Delete Post", error);
        let errorMessage = "Failed to delete post , Please Try again.";

        if (error.reponse?.data?.message) {
            errorMessage = error.response.data.message;
        }
        await Swal.fire({
            icon: "error",
            title: "Error",
            text: errorMessage,
        });
    }
}

async function submitPost() {
    if (!postTitle.value.trim()) {
        await Swal.fire({
            icon: "error",
            title: "Empty Title",
            text: "Please add some content or an Title to your post",
            timer: 2000,
            showConfirmButton: true,
        });
        return;
    }

    if (!categoryPost.value) {
        await Swal.fire({
            icon: "error",
            title: "Empty Category",
            text: "Please add some content or an Category to your post",
            timer: 2000,
            showConfirmButton: true,
        });
        return;
    }

    if (!postContent.value.trim() && !selectedImage.value) {
        await Swal.fire({
            icon: "error",
            title: "Empty Post",
            text: "Please add some content or an image to your post",
            timer: 2000,
            showConfirmButton: true,
        });
        return;
    }

    isLoading.value = true;

    try {
        const formData = new FormData();
        formData.append("content", postContent.value.trim());
        formData.append("title_post", postTitle.value.trim());
        formData.append("category_post", categoryPost.value);

        if (selectedImage.value) {
            formData.append("image", selectedImage.value);
        }

        console.log("Submitting post with data:", {
            title: postTitle.value.trim(),
            category: categoryPost.value,
            content: postContent.value.trim(),
            hasImage: !!selectedImage.value,
        });

        const response = await axios.post("/api/posts", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        await Swal.fire({
            icon: "success",
            title: "Post Created",
            text: "Your post has been published successfully",
            timer: 2000,
            showConfirmButton: false,
        });

        await fetchPosts();
        closeCreatePostModal();
    } catch (error) {
        console.error("Error Creating Post", error);

        localStorage.setItem(
            "last_post_error",
            JSON.stringify({
                message: error.message,
                response: error.response?.data,
                status: error.response?.status,
                timestamp: new Date().toISOString(),
            })
        );

        let errorMessage = "Failed To create post. Please try again";

        if (error.response?.data?.errors) {
            const errors = error.response.data.errors;
            errorMessage = Object.values(errors).flat().join(" ");
        } else if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        }

        await Swal.fire({
            icon: "error",
            title: "Failed to post",
            text: errorMessage,
        });

        if (error.response?.status === 401 || error.response?.status === 419) {
            console.log("Authentication error detected, redirecting to login");
            router.push("/login");
        }
    } finally {
        isLoading.value = false;
    }
}
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

async function UpdatePost() {
    if (!editPostTitle.value || !editPostTitle.value.trim()) {
        await Swal.fire({
            icon: "error",
            title: "Empty Title",
            text: "Please add a title to your post",
            timer: 3000,
            showConfirmButton: true,
        });
        return;
    }

    if (!editCategPost.value) {
        await Swal.fire({
            icon: "error",
            title: "Empty Category",
            text: "Please select a category for your post",
            timer: 3000,
            showConfirmButton: true,
        });
        return;
    }

    const hasContent = editPostContent.value && editPostContent.value.trim();
    const hasImage = editSelectImage.value || editingPost.value.image;

    if (!hasContent && !hasImage) {
        await Swal.fire({
            icon: "error",
            title: "Empty Post",
            text: "Please add some content or an image to your post",
            timer: 3000,
            showConfirmButton: true,
        });
        return;
    }

    isLoading.value = true;

    try {
        const formData = new FormData();

        formData.append("editPostContent", editPostContent.value.trim());
        formData.append("title_post", editPostTitle.value.trim());
        formData.append("editCategPost", editCategPost.value.trim());
        formData.append("_method", "PUT");

        if (editSelectImage.value) {
            formData.append("image", editSelectImage.value);
        }

        const response = await axios.post(
            `/api/posts/${editingPost.value.id}`,
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            }
        );

        await Swal.fire({
            icon: "success",
            title: "Update Success",
            text: "Post Updated Successfully",
            timer: 2000,
            showConfirmButton: true,
        });

        await fetchPosts();
        closeEditModal();
    } catch (error) {
        console.error("There something wrong please check", error);

        let errorMessage = "Error Please try again";

        if (error.response?.data?.errors) {
            const errors = error.response.data.errors;
            errorMessage = Object.values(errors).flat().join(", ");
        } else if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        }

        await Swal.fire({
            icon: "error",
            title: "Update Failed",
            text: errorMessage,
            timer: 3000,
            showConfirmButton: true,
        });
    } finally {
        isLoading.value = false;
    }
}

function formatTimeAgo(dateString) {
    // the date/time in
    const date = new Date(dateString);
    // current time in the user device auto detect
    const now = new Date();

    // so for this  converting a UTC date
    // (e.g. from your database or API) into your local timezone .
    const localDate = new Date(
        date.getTime() - date.getTimezoneOffset() * 60000
    );
    // For TimezoneOffset since nasa manila tayo UTC+8 A head tayo ng 8 hours sa utc
    // yung sa 60000 naman since converts minutes → milliseconds because JavaScript Date
    // timestamps work in milliseconds.
    // 1 minute = 60 seconds
    // 1 second = 1000 milliseconds
    // So, 1 minute = 60 × 1000 = 60000 milliseconds
    const diffInSeconds = Math.floor((now - localDate) / 1000);

    if (diffInSeconds < 60) return "Just Now";
    if (diffInSeconds < 3600)
        return `${Math.floor(diffInSeconds / 60)} minutes ago`;
    if (diffInSeconds < 86400)
        return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    if (diffInSeconds < 2592000)
        return `${Math.floor(diffInSeconds / 86400)} days ago`;
    return localDate.toLocaleDateString();
}

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

const getPostUserInitials = (postUser) => {
    if (!postUser) return "US";

    if (postUser.name) {
        return postUser.name
            .split(" ")
            .map((n) => n[0])
            .join("")
            .toUpperCase()
            .substring(0, 2);
    }

    // Use username if available
    if (postUser.username) {
        return postUser.username.substring(0, 2).toUpperCase();
    }

    // Use email if available
    if (postUser.email) {
        return postUser.email.substring(0, 2).toUpperCase();
    }

    // Default
    return "US";
};

function openCreatePostModal() {
    isCreatePostModalVisible.value = true;
}

// edit post post function modal

function openEditModal(post) {
    editingPost.value = post;
    editPostContent.value = post.text_content;
    editPostTitle.value = post.title_post;
    editCategPost.value = post.category_post;
    editImagePreview.value = post.image ? "/storage/" + post.image : null;
    isEditModalVisible.value = true;
}

function closeEditModal() {
    isEditModalVisible.value = false;
    editingPost.value = null;
    editPostContent.value = "";
    editPostTitle.value = "";
    editCategPost.value = "";
    editImagePreview.value = null;
    editSelectImage.value = null;
}

function handleEditImageSelect(event) {
    const file = event.target.files[0];

    if (file) {
        if (!file.type.startsWith("image/")) {
            Swal.fire({
                icon: "error",
                title: "Invalid File",
                text: "Please Select an image file only",
            });
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: "error",
                title: "File Too Large",
                text: "Please Select an image smaller than 5MB",
            });
            return;
        }

        editSelectImage.value = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            editImagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function removeEditImage() {
    editSelectImage.value = null;
    editImagePreview.value = editingPost.value.image
        ? "/storage/" + editingPost.value.image
        : null;
}

function closeCreatePostModal() {
    isCreatePostModalVisible.value = false;
    resetForm();
}

function resetForm() {
    postContent.value = "";
    postTitle.value = "";
    categoryPost.value = "";
    selectedImage.value = null;
    imagePreview.value = null;
}

function handleImageSelect(event) {
    const file = event.target.files[0];

    if (file) {
        if (!file.type.startsWith("image/")) {
            Swal.fire({
                icon: "error",
                title: "Invalid File",
                text: "Please Select Image file Only",
            });
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: "error",
                title: "File to Large",
                text: "Please Select an image Smaller than 5MB",
            });
            return;
        }

        selectedImage.value = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    selectedImage.value = null;
    imagePreview.value = null;
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

function toggleDropdown() {
    isDropdownVisible.value = !isDropdownVisible.value;
}

function toogleDropDownSetting() {
    isSettingVisibile.value = !isSettingVisibile.value;
}

function handleBackdropClick(event) {
    if (event.target.classList.contains("modal-backdrop")) {
        closeCreatePostModal();
    }
}

function handleEscape(e) {
    if (e.key === "Escape") {
        isDropdownVisible.value = false;
        if (isCreatePostModalVisible.value) {
            closeCreatePostModal();
        }
    }
}

onMounted(() => {
    document.addEventListener("keydown", handleEscape);
    fetchPosts();
    fetchProfilePic();
    loadUserData();
});

onBeforeUnmount(() => {
    document.removeEventListener("keydown", handleEscape);
});

const goToProfile = () => {
    router.push("/profiles");
};
const goToComments = (postId) => {
    router.push({
        name: "user.comment",
        params: { id: postId },
    });
};

onMounted(() => {
    const user = auth.getUser();
    currentUserId.value = user?.id;
    fetchFriends();
});

const VisitProfile = (userId) => {
    router.push({
        name: "user.visitProfile",
        params: { id: userId },
    });
};
</script>

<template>
    <body class="min-h-screen bg-gray-900">
        <!-- Header -->
        <header
            class="sticky top-0 z-50 bg-black/95 backdrop-blur-sm border-b border-gray-800"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-8">
                        <a
                            href="index.html"
                            class="text-xl font-bold text-white"
                            >DevFeed</a
                        >
                        <nav class="hidden md:flex items-center gap-6">
                            <a
                                href="feed.html"
                                class="text-orange-500 font-medium"
                                >Feed</a
                            >
                            <a
                                href="#"
                                class="text-gray-400 hover:text-white transition-colors"
                                >Popular</a
                            >
                            <a
                                href="#"
                                class="text-gray-400 hover:text-white transition-colors"
                                >Topics</a
                            >
                        </nav>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="relative hidden sm:block">
                            <input
                                type="search"
                                placeholder="Search posts..."
                                class="w-64 px-4 py-2 pl-10 bg-gray-900 border border-gray-800 rounded-lg text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                            />
                            <i
                                class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm"
                            ></i>
                        </div>
                        <button
                            class="relative text-gray-400 hover:text-white transition-colors"
                        >
                            <i class="fas fa-bell text-xl"></i>
                            <span
                                class="absolute -top-1 -right-1 w-2 h-2 bg-orange-500 rounded-full"
                            ></span>
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

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Main Feed -->
                <main class="lg:col-span-8">
                    <!-- Create Post Card -->
                    <div
                        class="bg-gray-900 border border-gray-800 rounded-lg p-4 mb-6"
                    >
                        <div class="flex items-center gap-3">
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

                            <button
                                @click="openCreatePostModal"
                                class="flex-1 text-left px-4 py-2.5 bg-gray-800 hover:bg-gray-750 border border-gray-700 rounded-lg text-gray-400 transition-colors"
                            >
                                Share your thoughts...
                            </button>

                            <div
                                v-if="isCreatePostModalVisible"
                                class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop"
                                @click="handleBackdropClick"
                            >
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-50"
                                ></div>

                                <div
                                    class="relative bg-gray-800 rounded-lg w-full max-w-2xl mx-4 shadow-xl"
                                >
                                    <!-- Modal Header -->
                                    <div
                                        class="flex items-center justify-between p-4 border-b border-gray-700"
                                    >
                                        <h3
                                            class="text-lg font-semibold text-white"
                                        >
                                            Create Post
                                        </h3>
                                        <button
                                            @click="closeCreatePostModal"
                                            class="text-gray-400 hover:text-white transition-colors"
                                        >
                                            <svg
                                                class="w-6 h-6"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"
                                                ></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Modal Body -->
                                    <form
                                        @submit.prevent="submitPost"
                                        class="p-4"
                                    >
                                        <!-- User Info -->
                                        <div class="flex items-center mb-4">
                                            <div
                                                class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold mr-3"
                                            >
                                                {{ userInitials }}
                                            </div>

                                            <div>
                                                <p
                                                    class="text-white font-semibold"
                                                >
                                                    {{
                                                        user?.name ||
                                                        user?.email
                                                    }}
                                                </p>

                                                <p
                                                    class="text-white text-[12px] border border-gray-600 bg-gray-600 text-center rounded-[5px]"
                                                >
                                                    <i
                                                        class="ri-earth-line mr-1"
                                                    ></i
                                                    >Public
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Title Post -->
                                        <input
                                            type="text"
                                            placeholder="Title Post"
                                            v-model="postTitle"
                                            class="w-full h-10 mb-5 bg-gray-900 border border-gray-700 rounded-lg p-4 text-white placeholder-gray-400 resize-none focus:outline-none focus:border-blue-500"
                                        />
                                        <!-- Content Textarea -->

                                        <textarea
                                            v-model="postContent"
                                            placeholder="What's on your mind?"
                                            class="w-full h-32 bg-gray-900 border border-gray-700 rounded-lg p-4 text-white placeholder-gray-400 resize-none focus:outline-none focus:border-blue-500"
                                        ></textarea>

                                        <!-- Dropdown  Area to -->
                                        <div class="mb-4">
                                            <select
                                                v-model="categoryPost"
                                                class="w-full bg-gray-900 border border-gray-700 rounded-lg p-3 text-white focus:outline-none focus:border-blue-500"
                                            >
                                                <option
                                                    value=""
                                                    disabled
                                                    selected
                                                >
                                                    Select Category
                                                </option>
                                                <option value="Technology">
                                                    Technology
                                                </option>
                                                <option value="Programming">
                                                    Programming
                                                </option>
                                                <option value="Web Development">
                                                    Web Development
                                                </option>
                                                <option
                                                    value="Mobile Development"
                                                >
                                                    Mobile Development
                                                </option>
                                                <option value="Design">
                                                    Design
                                                </option>
                                                <option value="Other">
                                                    Other
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Image Preview -->
                                        <div
                                            v-if="imagePreview"
                                            class="mt-4 relative"
                                        >
                                            <img
                                                :src="imagePreview"
                                                alt="Preview"
                                                class="w-full h-64 object-cover rounded-lg"
                                            />
                                            <button
                                                @click="removeImage"
                                                class="absolute top-2 right-2 bg-black bg-opacity-50 rounded-full p-1 text-white hover:bg-opacity-70 transition-colors"
                                            >
                                                <svg
                                                    class="w-5 h-5"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"
                                                    ></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Add to your post -->
                                        <div
                                            class="mt-4 p-3 border border-gray-700 rounded-lg"
                                        >
                                            <p
                                                class="text-gray-400 text-sm mb-2"
                                            >
                                                Add to your post
                                            </p>
                                            <div
                                                class="flex items-center space-x-4"
                                            >
                                                <label
                                                    class="flex items-center text-gray-400 hover:text-white cursor-pointer transition-colors"
                                                >
                                                    <input
                                                        type="file"
                                                        accept="image/*"
                                                        name="image"
                                                        @change="
                                                            handleImageSelect
                                                        "
                                                        class="hidden"
                                                    />
                                                    <svg
                                                        class="w-6 h-6 mr-2"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                        ></path>
                                                    </svg>
                                                    <span>Photo</span>
                                                </label>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Modal Footer -->
                                    <div class="p-4 border-t border-gray-700">
                                        <button
                                            @click="submitPost"
                                            :disabled="
                                                isLoading ||
                                                (!postContent.trim() &&
                                                    !selectedImage)
                                            "
                                            class="w-full bg-blue-500 hover:bg-blue-600 disabled:bg-blue-300 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-colors"
                                        >
                                            <span v-if="isLoading"
                                                >Posting...</span
                                            >
                                            <span v-else>Post</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button
                                @click="openCreatePostModal"
                                class="px-4 py-2.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors"
                            >
                                <i class="fas fa-plus mr-2"></i>Post
                            </button>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <div
                        class="flex items-center gap-2 mb-6 border-b border-gray-800"
                    >
                        <button
                            class="px-4 py-2 text-orange-500 border-b-2 border-orange-500 font-medium"
                        >
                            <i class="fas fa-fire mr-2"></i>Hot
                        </button>
                        <button
                            class="px-4 py-2 text-gray-400 hover:text-white transition-colors"
                        >
                            <i class="fas fa-chart-line mr-2"></i>Trending
                        </button>
                        <button
                            class="px-4 py-2 text-gray-400 hover:text-white transition-colors"
                        >
                            <i class="fas fa-clock mr-2"></i>New
                        </button>
                        <button
                            class="px-4 py-2 text-gray-400 hover:text-white transition-colors"
                        >
                            <i class="fas fa-trophy mr-2"></i>Top
                        </button>
                    </div>

                    <!-- heading ng post -->
                    <div v-if="isLoadingPosts" class="text-center py-8">
                        <div
                            class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500 mx-auto"
                        ></div>
                        <p class="text-gray-400 mt-2">Loading posts...</p>
                    </div>

                    <!--if empty eto lalabas-->
                    <div
                        v-else-if="posts.length === 0"
                        class="text-center py-12"
                    >
                        <i
                            class="fas fa-newspaper text-4xl text-gray-600 mb-4"
                        ></i>
                        <h3 class="text-white text-lg font-semibold mb-2">
                            No posts yet
                        </h3>
                        <p class="text-gray-400">
                            Be the first to share your thoughts!
                        </p>
                    </div>
                    <article
                        v-for="post in posts"
                        :key="post.id"
                        class="bg-gray-900 border border-gray-800 rounded-lg mb-4 hover:border-gray-700 transition-colors"
                    >
                        <div class="flex gap-4 p-4">
                            <!-- User Avatar on Left -->
                            <div class="flex flex-col items-center">
                                <div
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-semibold bg-blue-500 overflow-hidden border-2 border-white"
                                >
                                    <img
                                        v-if="post.user?.profile?.photo_profile"
                                        :src="
                                            '/storage/' +
                                            post.user.profile.photo_profile
                                        "
                                        :alt="post.user?.name"
                                        class="w-full h-full object-cover"
                                    />
                                    <span v-else>{{
                                        getPostUserInitials(post.user)
                                    }}</span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <!-- Post header -->
                                <div
                                    class="flex items-center gap-2 text-sm text-gray-400 mb-2"
                                >
                                    <span
                                        v-if="post.category_post"
                                        class="px-2 py-0.5 bg-orange-500/10 text-orange-500 rounded text-xs font-medium"
                                    >
                                        {{ post.category_post }}
                                    </span>
                                    <span>
                                        Posted by

                                        <!-- For own profile -->
                                        <router-link
                                            v-if="
                                                post.user?.id === currentUserId
                                            "
                                            to="/profiles"
                                            class="text-white hover:underline"
                                        >
                                            {{ post.user?.name }}
                                        </router-link>

                                        <!-- For other profiles -->
                                        <span
                                            v-else
                                            class="text-white hover:underline cursor-pointer"
                                            @click="VisitProfile(post.user?.id)"
                                        >
                                            {{ post.user?.name }}
                                        </span>
                                    </span>
                                    <span>•</span>
                                    <span>{{
                                        formatTimeAgo(
                                            post.published_at || post.created_at
                                        )
                                    }}</span>

                                    <!-- Edit/Delete buttons -->
                                    <button
                                        v-if="user && post.user_id === user.id"
                                        @click="deletePost(post.id)"
                                        class="group p-2 rounded-full text-white hover: transition-all duration-200 ease-in-out transform hover:scale-110 focus:outline-none"
                                        title="Delete Post"
                                    >
                                        <i
                                            class="ri-delete-bin-line text-[18px] group-hover:scale-110"
                                        ></i>
                                    </button>
                                    <button
                                        v-if="user && post.user_id === user.id"
                                        @click="openEditModal(post)"
                                        class="group p-2 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-all duration-200 ease-in-out transform hover:scale-110 focus:outline-none"
                                        title="Edit Post"
                                    >
                                        <i
                                            class="ri-edit-line text-[18px] group-hover:scale-110"
                                        ></i>
                                    </button>
                                </div>

                                <!-- Post Title -->
                                <h2
                                    class="text-xl font-bold text-white mb-2 hover:text-orange-500 cursor-pointer"
                                >
                                    {{ post.title_post }}
                                </h2>

                                <!-- Post Content -->
                                <p class="text-gray-300 mb-4 leading-relaxed">
                                    {{ post.text_content }}
                                </p>

                                <!-- Post Image -->
                                <div v-if="post.image" class="mb-4">
                                    <img
                                        :src="'/storage/' + post.image"
                                        :alt="post.title_post"
                                        class="w-full h-64 object-cover rounded-lg"
                                    />
                                </div>

                                <div
                                    class="flex items-center gap-4 text-sm text-gray-400"
                                >
                                    <!-- Karma Voting -->
                                    <KarmaVoter
                                        :post-id="post.id"
                                        :initial-karma="post.karma_score || 0"
                                        :initial-user-vote="post.user_vote"
                                        @voted="onPostVoted"
                                    />

                                    <!-- Comments -->
                                    <button
                                        @click="goToComments(post.id)"
                                        class="flex items-center gap-2 hover:bg-gray-800 px-3 py-1.5 rounded-lg transition-colors"
                                    >
                                        <i class="fas fa-comment"></i>
                                        <span
                                            >{{
                                                post.comments_count
                                            }}
                                            comments</span
                                        >
                                    </button>

                                    <!-- Share -->
                                    <button
                                        class="flex items-center gap-2 hover:bg-gray-800 px-3 py-1.5 rounded-lg transition-colors"
                                    >
                                        <i class="fas fa-share"></i>
                                        <span>Share</span>
                                    </button>

                                    <!-- Save -->
                                    <button
                                        class="flex items-center gap-2 hover:bg-gray-800 px-3 py-1.5 rounded-lg transition-colors"
                                    >
                                        <i class="fas fa-bookmark"></i>
                                        <span>Save</span>
                                    </button>
                                </div>

                                <!-- Comments Section -->
                                <div
                                    v-if="expandedPostId === post.id"
                                    class="mt-4"
                                >
                                    <CommentComponent
                                        :postId="post.id"
                                        :initialComments="post.comments || []"
                                        @comment-added="
                                            fetchPostComments(post.id)
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Load More -->
                    <button
                        class="w-full py-3 bg-gray-900 hover:bg-gray-800 border border-gray-800 rounded-lg text-white font-medium transition-colors"
                    >
                        Load More Posts
                    </button>
                </main>

                <!-- Sidebar -->
                <aside class="lg:col-span-4 space-y-6">
                    <!-- Trending Topics -->
                    <div
                        class="bg-gray-900 border border-gray-800 rounded-lg p-6"
                    >
                        <h3 class="text-lg font-bold text-white mb-4">
                            Trending Topics
                        </h3>
                        <div class="space-y-3">
                            <a
                                href="#"
                                class="flex items-center justify-between p-3 bg-gray-800 hover:bg-gray-750 rounded-lg transition-colors group"
                            >
                                <div>
                                    <div
                                        class="text-white font-medium group-hover:text-orange-500 transition-colors"
                                    >
                                        #ReactJS
                                    </div>
                                    <div class="text-sm text-gray-400">
                                        2.4k posts today
                                    </div>
                                </div>
                                <i
                                    class="fas fa-arrow-trend-up text-orange-500"
                                ></i>
                            </a>
                            <a
                                href="#"
                                class="flex items-center justify-between p-3 bg-gray-800 hover:bg-gray-750 rounded-lg transition-colors group"
                            >
                                <div>
                                    <div
                                        class="text-white font-medium group-hover:text-orange-500 transition-colors"
                                    >
                                        #WebDev
                                    </div>
                                    <div class="text-sm text-gray-400">
                                        1.8k posts today
                                    </div>
                                </div>
                                <i
                                    class="fas fa-arrow-trend-up text-orange-500"
                                ></i>
                            </a>
                            <a
                                href="#"
                                class="flex items-center justify-between p-3 bg-gray-800 hover:bg-gray-750 rounded-lg transition-colors group"
                            >
                                <div>
                                    <div
                                        class="text-white font-medium group-hover:text-orange-500 transition-colors"
                                    >
                                        #AI
                                    </div>
                                    <div class="text-sm text-gray-400">
                                        1.5k posts today
                                    </div>
                                </div>
                                <i
                                    class="fas fa-arrow-trend-up text-orange-500"
                                ></i>
                            </a>
                            <a
                                href="#"
                                class="flex items-center justify-between p-3 bg-gray-800 hover:bg-gray-750 rounded-lg transition-colors group"
                            >
                                <div>
                                    <div
                                        class="text-white font-medium group-hover:text-orange-500 transition-colors"
                                    >
                                        #DevOps
                                    </div>
                                    <div class="text-sm text-gray-400">
                                        892 posts today
                                    </div>
                                </div>
                                <i
                                    class="fas fa-arrow-trend-up text-orange-500"
                                ></i>
                            </a>
                        </div>
                    </div>

                    <!-- Popular Communities -->
                    <div
                        class="bg-gray-900 border border-gray-800 rounded-lg p-6"
                    >
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-white">
                                Friends
                            </h3>
                            <div class="flex items-center gap-2">
                                <span
                                    v-if="onlineFriendsCount > 0"
                                    class="inline-flex items-center gap-1 text-xs text-green-400 bg-green-900/20 px-2 py-1 rounded-full"
                                >
                                    <span
                                        class="w-2 h-2 bg-green-400 rounded-full"
                                    ></span>
                                    {{ onlineFriendsCount }} online
                                </span>
                                <span class="text-sm text-gray-400">
                                    {{ totalFriends }} total
                                </span>
                            </div>
                        </div>

                        <div v-if="loadingFriends" class="space-y-3">
                            <!-- Loading Skeleton -->
                            <div
                                v-for="n in 3"
                                :key="n"
                                class="flex items-center gap-3 p-2"
                            >
                                <div
                                    class="w-10 h-10 bg-gray-800 rounded-lg animate-pulse"
                                ></div>
                                <div class="flex-1 space-y-2">
                                    <div
                                        class="h-4 bg-gray-800 rounded animate-pulse"
                                    ></div>
                                    <div
                                        class="h-3 bg-gray-800 rounded animate-pulse w-3/4"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <div v-else-if="friends.length > 0" class="space-y-3">
                            <div
                                v-for="friend in friends"
                                :key="friend.id"
                                class="flex items-center gap-3 p-2 hover:bg-gray-800 rounded-lg transition-colors group cursor-pointer"
                                @click="VisitProfile(friend.id)"
                            >
                                <div class="relative">
                                    <div
                                        class="w-10 h-10 rounded-lg flex items-center justify-center text-white font-bold overflow-hidden"
                                        :class="
                                            friend.profile?.photo_profile
                                                ? 'bg-gray-700'
                                                : 'bg-gradient-to-br from-blue-500 to-cyan-500'
                                        "
                                    >
                                        <img
                                            v-if="friend.profile?.photo_profile"
                                            :src="
                                                '/storage/' +
                                                friend.profile.photo_profile
                                            "
                                            :alt="friend.name"
                                            class="w-full h-full object-cover"
                                        />
                                        <span v-else>
                                            {{ getFriendInitials(friend.name) }}
                                        </span>
                                    </div>
                                    <!-- Online Status Indicator -->
                                    <div
                                        class="absolute -bottom-1 -right-1 w-3 h-3 rounded-full border-2 border-gray-900"
                                        :class="
                                            friend.is_online
                                                ? 'bg-green-400'
                                                : 'bg-gray-500'
                                        "
                                        :title="
                                            friend.is_online
                                                ? 'Online'
                                                : `Last seen ${formatLastSeen(
                                                      friend.last_seen
                                                  )}`
                                        "
                                    ></div>
                                </div>

                                <!-- Friend Info -->
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="text-white font-medium group-hover:text-orange-500 transition-colors truncate"
                                    >
                                        {{ friend.name }}
                                    </div>
                                    <div
                                        class="text-sm text-gray-400 flex items-center gap-1"
                                    >
                                        <span
                                            v-if="friend.is_online"
                                            class="text-green-400 flex items-center gap-1"
                                        >
                                            <span
                                                class="w-1.5 h-1.5 bg-green-400 rounded-full"
                                            ></span>
                                            Online
                                        </span>
                                        <span
                                            v-else
                                            class="flex items-center gap-1"
                                        >
                                            <span
                                                class="w-1.5 h-1.5 bg-gray-500 rounded-full"
                                            ></span>
                                            {{
                                                formatLastSeen(friend.last_seen)
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Friends Since Badge -->
                                <div
                                    v-if="friend.friends_since"
                                    class="text-xs text-gray-400 bg-gray-800 px-2 py-1 rounded"
                                    title="Friends since"
                                >
                                    {{
                                        formatFriendsSince(friend.friends_since)
                                    }}
                                </div>
                            </div>

                            <!-- View All Friends Link -->
                            <div class="pt-2 border-t border-gray-800">
                                <button
                                    @click="viewAllFriends"
                                    class="w-full text-center text-orange-500 hover:text-orange-400 text-sm font-medium py-2 transition-colors"
                                >
                                    View All Friends
                                </button>
                            </div>
                        </div>

                        <div v-else class="text-center py-8">
                            <div class="text-gray-400 mb-2">
                                <i class="fas fa-users text-2xl mb-3"></i>
                            </div>
                            <p class="text-gray-400 text-sm">No friends yet</p>
                            <p class="text-gray-500 text-xs mt-1">
                                Friends will appear here when you follow each
                                other
                            </p>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div
                        class="bg-gray-900 border border-gray-800 rounded-lg p-6"
                    >
                        <h3 class="text-lg font-bold text-white mb-4">
                            Your Stats
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Posts</span>
                                <span class="text-white font-medium">24</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Comments</span>
                                <span class="text-white font-medium">156</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Karma</span>
                                <span class="text-orange-500 font-medium"
                                    >2,847</span
                                >
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Create Post Modal -->
        <div
            id="createPostModal"
            class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        >
            <div
                class="bg-gray-900 border border-gray-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto"
            >
                <div
                    class="flex items-center justify-between p-6 border-b border-gray-800"
                >
                    <h2 class="text-xl font-bold text-white">Create a Post</h2>
                    <button
                        onclick="closeCreatePostModal()"
                        class="text-gray-400 hover:text-white transition-colors"
                    >
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form class="p-6 space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-300 mb-2"
                            >Topic</label
                        >
                        <select
                            class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        >
                            <option>JavaScript</option>
                            <option>TypeScript</option>
                            <option>CSS</option>
                            <option>React</option>
                            <option>Career</option>
                            <option>DevOps</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-300 mb-2"
                            >Title</label
                        >
                        <input
                            type="text"
                            placeholder="Enter an engaging title..."
                            class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-300 mb-2"
                            >Content</label
                        >
                        <textarea
                            rows="8"
                            placeholder="Share your thoughts, code, or questions..."
                            class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                        ></textarea>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-300 mb-2"
                            >Add Image (Optional)</label
                        >
                        <div
                            class="border-2 border-dashed border-gray-700 rounded-lg p-8 text-center hover:border-gray-600 transition-colors cursor-pointer"
                        >
                            <i
                                class="fas fa-cloud-upload-alt text-4xl text-gray-500 mb-2"
                            ></i>
                            <p class="text-gray-400">
                                Click to upload or drag and drop
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                PNG, JPG, GIF up to 10MB
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-4">
                        <button
                            type="submit"
                            class="flex-1 px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors"
                        >
                            <i class="fas fa-paper-plane mr-2"></i>Publish Post
                        </button>
                        <button
                            type="button"
                            onclick="closeCreatePostModal()"
                            class="px-6 py-3 bg-gray-800 hover:bg-gray-750 text-white rounded-lg font-medium transition-colors"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</template>
