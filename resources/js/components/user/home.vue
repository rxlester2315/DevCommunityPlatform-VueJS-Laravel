<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import axios from "axios";
import { useRoute, useRouter } from "vue-router";
import Swal from "sweetalert2";
import { auth } from "../../utils/auth";

const router = useRouter();

const isDropdownVisible = ref(false);
const isCreatePostModalVisible = ref(false);

const user = computed(() => auth.getUser());

const userInitials = computed(() => {
    if (!user.value) return "GU";
    return user.value.name
        ? user.value.name
              .split(" ")
              .map((n) => n[0])
              .join("")
              .toUpperCase()
        : user.value.email.substring(0, 2).toUpperCase();
});

const logout = async () => {
    try {
        await axios.post("/api/logout");

        await Swal.fire({
            icon: "success",
            title: "Logout Success",
            text: "Logout Successfully",
            timer: 2000,
            showConfirmButton: false,
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

function handleEscape(e) {
    if (e.key === "Escape") {
        isDropdownVisible.value = false;
    }
}

onMounted(() => {
    document.addEventListener("keydown", handleEscape);
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
                            <!-- User Icon -->
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-orange-500 to-red-500 rounded-full flex items-center justify-center text-white text-sm font-medium cursor-pointer"
                                @click="toggleDropdown"
                            >
                                JD
                            </div>

                            <!-- Dropdown Menu -->
                            <div
                                v-show="isDropdownVisible"
                                class="absolute left-30 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg"
                            >
                                <div class="py-1">
                                    <!-- Dropdown item: Profile -->
                                    <a
                                        href="#"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >Profile</a
                                    >
                                    <!-- Dropdown item: Settings -->
                                    <a
                                        href="#"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        >Settings</a
                                    >
                                    <!-- Dropdown item: Logout -->
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
                                JD
                            </div>
                            <button
                                onclick="openCreatePostModal()"
                                class="flex-1 text-left px-4 py-2.5 bg-gray-800 hover:bg-gray-750 border border-gray-700 rounded-lg text-gray-400 transition-colors"
                            >
                                Share your thoughts...
                            </button>
                            <button
                                onclick="openCreatePostModal()"
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

                    <!-- Post 2 -->
                    <article
                        class="bg-gray-900 border border-gray-800 rounded-lg mb-4 hover:border-gray-700 transition-colors"
                    >
                        <div class="flex gap-4 p-4">
                            <div class="flex flex-col items-center gap-1">
                                <button
                                    class="text-gray-400 hover:text-orange-500 transition-colors"
                                >
                                    <i class="fas fa-arrow-up text-xl"></i>
                                </button>
                                <span class="text-sm font-medium text-white"
                                    >1.2k</span
                                >
                                <button
                                    class="text-gray-400 hover:text-blue-500 transition-colors"
                                >
                                    <i class="fas fa-arrow-down text-xl"></i>
                                </button>
                            </div>

                            <div class="flex-1">
                                <div
                                    class="flex items-center gap-2 text-sm text-gray-400 mb-2"
                                >
                                    <span
                                        class="px-2 py-0.5 bg-blue-500/10 text-blue-500 rounded text-xs font-medium"
                                        >TypeScript</span
                                    >
                                    <span
                                        >Posted by
                                        <span
                                            class="text-white hover:underline cursor-pointer"
                                            >@code_master</span
                                        ></span
                                    >
                                    <span>•</span>
                                    <span>8 hours ago</span>
                                </div>

                                <h2
                                    class="text-xl font-bold text-white mb-2 hover:text-orange-500 cursor-pointer"
                                >
                                    TypeScript 5.4: What's New and Why It
                                    Matters
                                </h2>

                                <p class="text-gray-300 mb-4 leading-relaxed">
                                    The latest TypeScript release brings some
                                    exciting features that will change how we
                                    write type-safe code. Let's explore the most
                                    impactful changes...
                                </p>

                                <div
                                    class="flex items-center gap-4 text-sm text-gray-400"
                                >
                                    <button
                                        class="flex items-center gap-2 hover:bg-gray-800 px-3 py-1.5 rounded-lg transition-colors"
                                    >
                                        <i class="fas fa-comment"></i>
                                        <span>89 comments</span>
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

                    <!-- Post 3 with Image -->
                    <article
                        class="bg-gray-900 border border-gray-800 rounded-lg mb-4 hover:border-gray-700 transition-colors"
                    >
                        <div class="flex gap-4 p-4">
                            <div class="flex flex-col items-center gap-1">
                                <button
                                    class="text-gray-400 hover:text-orange-500 transition-colors"
                                >
                                    <i class="fas fa-arrow-up text-xl"></i>
                                </button>
                                <span class="text-sm font-medium text-white"
                                    >856</span
                                >
                                <button
                                    class="text-gray-400 hover:text-blue-500 transition-colors"
                                >
                                    <i class="fas fa-arrow-down text-xl"></i>
                                </button>
                            </div>

                            <div class="flex-1">
                                <div
                                    class="flex items-center gap-2 text-sm text-gray-400 mb-2"
                                >
                                    <span
                                        class="px-2 py-0.5 bg-green-500/10 text-green-500 rounded text-xs font-medium"
                                        >CSS</span
                                    >
                                    <span
                                        >Posted by
                                        <span
                                            class="text-white hover:underline cursor-pointer"
                                            >@design_wizard</span
                                        ></span
                                    >
                                    <span>•</span>
                                    <span>12 hours ago</span>
                                </div>

                                <h2
                                    class="text-xl font-bold text-white mb-2 hover:text-orange-500 cursor-pointer"
                                >
                                    Modern CSS Layouts: Grid vs Flexbox in 2025
                                </h2>

                                <p class="text-gray-300 mb-3 leading-relaxed">
                                    A comprehensive comparison of when to use
                                    CSS Grid versus Flexbox, with real-world
                                    examples and performance considerations.
                                </p>

                                <div
                                    class="flex items-center gap-4 text-sm text-gray-400"
                                >
                                    <button
                                        class="flex items-center gap-2 hover:bg-gray-800 px-3 py-1.5 rounded-lg transition-colors"
                                    >
                                        <i class="fas fa-comment"></i>
                                        <span>234 comments</span>
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

                    <!-- Post 4 -->
                    <article
                        class="bg-gray-900 border border-gray-800 rounded-lg mb-4 hover:border-gray-700 transition-colors"
                    >
                        <div class="flex gap-4 p-4">
                            <div class="flex flex-col items-center gap-1">
                                <button
                                    class="text-gray-400 hover:text-orange-500 transition-colors"
                                >
                                    <i class="fas fa-arrow-up text-xl"></i>
                                </button>
                                <span class="text-sm font-medium text-white"
                                    >523</span
                                >
                                <button
                                    class="text-gray-400 hover:text-blue-500 transition-colors"
                                >
                                    <i class="fas fa-arrow-down text-xl"></i>
                                </button>
                            </div>

                            <div class="flex-1">
                                <div
                                    class="flex items-center gap-2 text-sm text-gray-400 mb-2"
                                >
                                    <span
                                        class="px-2 py-0.5 bg-purple-500/10 text-purple-500 rounded text-xs font-medium"
                                        >Career</span
                                    >
                                    <span
                                        >Posted by
                                        <span
                                            class="text-white hover:underline cursor-pointer"
                                            >@tech_lead</span
                                        ></span
                                    >
                                    <span>•</span>
                                    <span>1 day ago</span>
                                </div>

                                <h2
                                    class="text-xl font-bold text-white mb-2 hover:text-orange-500 cursor-pointer"
                                >
                                    From Junior to Senior: My 5-Year Journey in
                                    Tech
                                </h2>

                                <p class="text-gray-300 mb-4 leading-relaxed">
                                    Sharing the lessons, mistakes, and
                                    breakthroughs that shaped my career
                                    progression. What I wish I knew when I
                                    started...
                                </p>

                                <div
                                    class="flex items-center gap-4 text-sm text-gray-400"
                                >
                                    <button
                                        class="flex items-center gap-2 hover:bg-gray-800 px-3 py-1.5 rounded-lg transition-colors"
                                    >
                                        <i class="fas fa-comment"></i>
                                        <span>167 comments</span>
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
