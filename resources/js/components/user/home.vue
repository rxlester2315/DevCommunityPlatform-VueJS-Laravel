<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import axios from "axios";
import { useRoute, useRouter } from "vue-router";
import Swal from "sweetalert2";
import { auth } from "../../utils/auth";
import "remixicon/fonts/remixicon.css";

const router = useRouter();

const isDropdownVisible = ref(false);
const isCreatePostModalVisible = ref(false);
const postContent = ref("");
const categoryPost = ref("");
const postTitle = ref("");
const selectedImage = ref(null);
const imagePreview = ref(null);
const isLoading = ref(false);
const user = ref(null);

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

function openCreatePostModal() {
    isCreatePostModalVisible.value = true;
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

async function submitPost() {
    if (!postTitle.value.trim()) {
        Swal.fire({
            icon: "error",
            title: "Empty Title",
            text: "Please add some content or an Title to your post",
            timer: 2000,
            showConfirmButton: true,
        });
    }

    if (!categoryPost.value) {
        Swal.fire({
            icon: "error",
            title: "Empty Category",
            text: "Please add some content or an Category to your post",
            timer: 2000,
            showConfirmButton: true,
        });
    }

    if (!postContent.value.trim() && !selectedImage.value) {
        Swal.fire({
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

        closeCreatePostModal();
    } catch (error) {
        console.error("Error Creating Post", error);

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
        }
    } catch (error) {
        console.error("Error loading user data:", error);
        user.value = null;
    }
}

const logout = async () => {
    try {
        await axios.post("/api/logout");

        await Swal.fire({
            icon: "success",
            title: "Logout Success",
            text: "Logout Successfully",
            timer: 2000,
            showConfirmButton: true,
        });

        auth.clearAuth();
        window.location.href = "/login";
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
        window.location.href = "/login";
    }
};

function toggleDropdown() {
    isDropdownVisible.value = !isDropdownVisible.value;
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
    loadUserData();
});

onBeforeUnmount(() => {
    document.removeEventListener("keydown", handleEscape);
});
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
                            >DevBlog</a
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
                                {{ userInitials }}
                            </div>

                            <div
                                v-show="isDropdownVisible"
                                class="absolute left-30 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg"
                            >
                                <div class="py-1">
                                    <a
                                        href="#"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >Profile</a
                                    >
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
                            <div
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

                    <!-- Post 1 -->
                    <article
                        class="bg-gray-900 border border-gray-800 rounded-lg mb-4 hover:border-gray-700 transition-colors"
                    >
                        <div class="flex gap-4 p-4">
                            <!-- Vote Section -->
                            <div class="flex flex-col items-center gap-1">
                                <button
                                    class="text-gray-400 hover:text-orange-500 transition-colors"
                                >
                                    <i class="fas fa-arrow-up text-xl"></i>
                                </button>
                                <span class="text-sm font-medium text-white"
                                    >342</span
                                >
                                <button
                                    class="text-gray-400 hover:text-blue-500 transition-colors"
                                >
                                    <i class="fas fa-arrow-down text-xl"></i>
                                </button>
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <div
                                    class="flex items-center gap-2 text-sm text-gray-400 mb-2"
                                >
                                    <span
                                        class="px-2 py-0.5 bg-orange-500/10 text-orange-500 rounded text-xs font-medium"
                                        >JavaScript</span
                                    >
                                    <span
                                        >Posted by
                                        <span
                                            class="text-white hover:underline cursor-pointer"
                                            >@sarah_dev</span
                                        ></span
                                    >
                                    <span>•</span>
                                    <span>4 hours ago</span>
                                </div>

                                <h2
                                    class="text-xl font-bold text-white mb-2 hover:text-orange-500 cursor-pointer"
                                >
                                    Understanding React Server Components: A
                                    Deep Dive
                                </h2>

                                <p class="text-gray-300 mb-4 leading-relaxed">
                                    After spending weeks working with React
                                    Server Components in production, I wanted to
                                    share my insights and lessons learned. The
                                    mental model shift is significant, but the
                                    benefits are worth it...
                                </p>

                                <div
                                    class="flex items-center gap-4 text-sm text-gray-400"
                                >
                                    <button
                                        class="flex items-center gap-2 hover:bg-gray-800 px-3 py-1.5 rounded-lg transition-colors"
                                    >
                                        <i class="fas fa-comment"></i>
                                        <span>128 comments</span>
                                    </button>
                                    <button
                                        class="flex items-center gap-2 hover:bg-gray-800 px-3 py-1.5 rounded-lg transition-colors"
                                    >
                                        <i class="fas fa-share"></i>
                                        <span>Share</span>
                                    </button>
                                    <button
                                        class="flex items-center gap-2 hover:bg-gray-800 px-3 py-1.5 rounded-lg transition-colors"
                                    >
                                        <i class="fas fa-bookmark"></i>
                                        <span>Save</span>
                                    </button>
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
                        <h3 class="text-lg font-bold text-white mb-4">
                            Popular Communities
                        </h3>
                        <div class="space-y-3">
                            <a
                                href="#"
                                class="flex items-center gap-3 p-2 hover:bg-gray-800 rounded-lg transition-colors group"
                            >
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center text-white font-bold"
                                >
                                    JS
                                </div>
                                <div class="flex-1">
                                    <div
                                        class="text-white font-medium group-hover:text-orange-500 transition-colors"
                                    >
                                        JavaScript
                                    </div>
                                    <div class="text-sm text-gray-400">
                                        245k members
                                    </div>
                                </div>
                            </a>
                            <a
                                href="#"
                                class="flex items-center gap-3 p-2 hover:bg-gray-800 rounded-lg transition-colors group"
                            >
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center text-white font-bold"
                                >
                                    PY
                                </div>
                                <div class="flex-1">
                                    <div
                                        class="text-white font-medium group-hover:text-orange-500 transition-colors"
                                    >
                                        Python
                                    </div>
                                    <div class="text-sm text-gray-400">
                                        198k members
                                    </div>
                                </div>
                            </a>
                            <a
                                href="#"
                                class="flex items-center gap-3 p-2 hover:bg-gray-800 rounded-lg transition-colors group"
                            >
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center text-white font-bold"
                                >
                                    WD
                                </div>
                                <div class="flex-1">
                                    <div
                                        class="text-white font-medium group-hover:text-orange-500 transition-colors"
                                    >
                                        Web Design
                                    </div>
                                    <div class="text-sm text-gray-400">
                                        156k members
                                    </div>
                                </div>
                            </a>
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
