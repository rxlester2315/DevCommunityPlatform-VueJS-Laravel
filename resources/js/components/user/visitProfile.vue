<script setup>
import { ref, onMounted, watch, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const route = useRoute();
const router = useRouter();

const profile = ref({
    user: {
        name: "",
        email: "",
        username: "",
    },
    bio: "",
    location: "",
    website: "",
    github_url: "",
    photo_profile: "",
    created_at: "",
});

const posts = ref([]);
const loading = ref(true);
const isLoadingPosts = ref(true);
const isDropdownVisible = ref(false);
const followLoading = ref(false);
const isFollowing = ref(false);
const followerCount = ref(0);
const isFriends = ref(false);
const isMutualFollow = ref(false);

// Computed property for user initials
const userInitials = computed(() => {
    return getInitials(profile.value.user.name);
});

// Fetch profile data for the visited user
const fetchProfile = async () => {
    try {
        const response = await axios.get(
            `/api/profile/visit/${route.params.id}`
        );
        profile.value = response.data.profile;
        isFollowing.value = response.data.is_following;
        isFriends.value = response.data.is_friends;
        isMutualFollow.value = response.data.is_mutual_follow;
    } catch (error) {
        console.error("Failed to fetch Profile", error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Failed to load profile",
        });
    } finally {
        loading.value = false;
    }
};

const followUser = async () => {
    followLoading.value = true;

    try {
        const response = await axios.post(
            `/api/users/${route.params.id}/toggle`
        );

        isFollowing.value = response.data.data.is_following;
        isFriends.value = response.data.data.is_friends;

        const action = isFollowing.value ? "followed" : "unfollowed";
        let message = `You have ${action} ${profile.value.user.name}.`;

        if (response.data.data.became_friends) {
            message += " - You are now friends! 🎉";
        }
        await Swal.fire({
            toast: true,
            icon: "success",
            title: message,
            position: "top-end",
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            background: "#1e1e2f", // subtle dark background
            color: "#fff",
            iconColor: "#4ade80", // modern green accent
            customClass: {
                popup: "swal2-modern-toast",
            },
            didOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
            },
        });
    } catch (error) {
        console.error("Error Follow", error);

        let errorMessage = "Failed to follow. Please try again.";

        if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        }

        await Swal.fire({
            icon: "error",
            title: "Oops!",
            text: errorMessage,
            background: "#1e1e2f",
            color: "#fff",
            iconColor: "#f87171", // modern red
            confirmButtonColor: "#6366f1", // indigo accent
            customClass: {
                popup: "swal2-modern-popup",
            },
        });
    } finally {
        followLoading.value = false;
    }
};

// Fetch posts for the visited user
const fetchPosts = async () => {
    try {
        const response = await axios.get("/api/profile/post");

        posts.value = response.data.posts;
    } catch (error) {
        console.error("Failed to fetch post", error);
    } finally {
        isLoadingPosts.value = false;
    }
};

// Use the same helper functions from your main profile
const formatDate = (datestring) => {
    if (!datestring) return "";
    const date = new Date(datestring);
    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
    });
};

const getInitials = (name) => {
    if (!name) return "UD";
    return name
        .split(" ")
        .map((word) => word[0])
        .join("")
        .toUpperCase()
        .slice(0, 2);
};

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return "Just Now";
    if (diffInSeconds < 3600)
        return `${Math.floor(diffInSeconds / 60)} minutes ago`;
    if (diffInSeconds < 86400)
        return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    if (diffInSeconds < 2592000)
        return `${Math.floor(diffInSeconds / 86400)} days ago`;
    return date.toLocaleDateString();
}

const getUsername = (email) => {
    if (!email) return "";
    return email.split("@")[0];
};

const getWebsite = (website) => {
    if (!website) return "";
    try {
        const url = new URL(website);
        return url.hostname.replace("www.", "");
    } catch {
        return website;
    }
};

const getGithub = (githublink) => {
    if (!githublink) return "";
    try {
        const url = new URL(githublink);
        return url.pathname.replace("/", "");
    } catch {
        return githublink;
    }
};

function toggleDropdown() {
    isDropdownVisible.value = !isDropdownVisible.value;
}

const backToHome = () => {
    router.push("/home");
};

// Lifecycle
onMounted(() => {
    fetchProfile();
    fetchPosts();
});

// Watch for route changes
watch(
    () => route.params.id,
    () => {
        fetchProfile();
        fetchPosts();
    }
);
</script>

<template>
    <body class="min-h-screen">
        <!-- Header - Same as your main profile -->
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
                            class="relative text-gray-400 hover:text-white transition-colors"
                        >
                            <i class="fas fa-bell text-xl"></i>
                            <span
                                class="absolute -top-1 -right-1 w-2 h-2 bg-orange-500 rounded-full"
                            ></span>
                        </button>
                        <!-- Your user dropdown remains the same -->
                    </div>
                </div>
            </div>
        </header>

        <!-- Profile Header -->
        <div v-if="loading" class="flex justify-center items-center py-20">
            <div class="text-white">Loading profile...</div>
        </div>
        <div
            v-else
            class="bg-gradient-to-r from-zinc-900 via-zinc-800 to-zinc-900 border-b border-zinc-800"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div
                    class="flex flex-col md:flex-row items-start md:items-end gap-6"
                >
                    <!-- Avatar -->
                    <div class="relative">
                        <div
                            class="w-32 h-32 rounded-full bg-gradient-to-br from-orange-500 to-pink-500 p-1"
                        >
                            <div
                                v-if="profile.photo_profile"
                                class="w-full h-full rounded-full bg-zinc-900 overflow-hidden"
                            >
                                <img
                                    :src="'/storage/' + profile.photo_profile"
                                    :alt="profile.user.name"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            <div
                                v-else
                                class="w-full h-full rounded-full bg-zinc-900 flex items-center justify-center text-4xl font-bold text-white"
                            >
                                {{ getInitials(profile.user.name) }}
                            </div>
                        </div>
                        <!-- Remove edit button for visited profiles -->
                    </div>

                    <!-- User Info -->
                    <div class="flex-1">
                        <div
                            class="flex flex-col md:flex-row md:items-center justify-between gap-4"
                        >
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-1">
                                    {{ profile.user.name || "No Name Set" }}
                                </h1>
                                <p class="text-zinc-400">
                                    @{{ getUsername(profile.user.email) }} •
                                    Joined {{ formatDate(profile.created_at) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <button
                                    @click="followUser"
                                    :disabled="followLoading"
                                    :class="[
                                        'px-6 py-2 rounded-lg transition-colors font-medium flex items-center gap-2',
                                        isFriends
                                            ? 'bg-green-600 text-white hover:bg-green-700 border border-green-500'
                                            : isFollowing
                                            ? 'bg-gray-700 text-white hover:bg-gray-600 border border-gray-600'
                                            : 'bg-orange-500 text-white hover:bg-orange-600',
                                        followLoading
                                            ? 'opacity-50 cursor-not-allowed'
                                            : '',
                                    ]"
                                >
                                    <!-- Loading Spinner -->
                                    <i
                                        v-if="followLoading"
                                        class="fas fa-spinner fa-spin"
                                    ></i>

                                    <!-- Friends Icon (when friends) -->
                                    <i
                                        v-else-if="isFriends"
                                        class="fas fa-user-friends"
                                    ></i>

                                    <!-- Following Icon (when following but not friends) -->
                                    <i
                                        v-else-if="isFollowing"
                                        class="fas fa-user-check"
                                    ></i>

                                    <!-- Follow Icon (when not following) -->
                                    <i v-else class="fas fa-user-plus"></i>

                                    <!-- Dynamic Text -->
                                    <span>
                                        {{
                                            followLoading
                                                ? "Loading..."
                                                : isFriends
                                                ? "Friends"
                                                : isFollowing
                                                ? "Following"
                                                : "Follow"
                                        }}
                                    </span>
                                </button>

                                <button
                                    class="px-6 py-2 bg-zinc-800 text-white rounded-lg hover:bg-zinc-700 transition-colors font-medium"
                                >
                                    <i class="fas fa-envelope mr-2"></i>Message
                                </button>
                                <button
                                    class="p-2 bg-zinc-800 text-white rounded-lg hover:bg-zinc-700 transition-colors"
                                >
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-zinc-300 mt-4 max-w-2xl">
                            {{ profile.bio || "No bio Available" }}
                        </p>
                        <div class="flex items-center gap-6 mt-4 text-sm">
                            <a
                                href="#"
                                class="text-zinc-400 hover:text-orange-500 transition-colors"
                            >
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                {{
                                    profile.location || "No location Available"
                                }}
                            </a>
                            <a
                                v-if="profile.website"
                                :href="profile.website"
                                target="_blank"
                                class="text-zinc-400 hover:text-orange-500 transition-colors"
                            >
                                <i class="fas fa-link mr-2"></i>
                                {{ getWebsite(profile.website) }}
                            </a>
                            <a
                                v-if="profile.github_url"
                                :href="profile.github_url"
                                target="_blank"
                                class="text-zinc-400 hover:text-orange-500 transition-colors"
                            >
                                <i class="fab fa-github mr-2"></i>
                                {{ getGithub(profile.github_url) }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                    <div
                        class="stat-card p-4 rounded-lg border border-zinc-800"
                    >
                        <div class="text-2xl font-bold text-white">
                            {{ posts.length }}
                        </div>
                        <div class="text-sm text-zinc-400">Posts</div>
                    </div>
                    <div
                        class="stat-card p-4 rounded-lg border border-zinc-800"
                    >
                        <div class="text-2xl font-bold text-white">5,678</div>
                        <div class="text-sm text-zinc-400">Followers</div>
                    </div>
                    <div
                        class="stat-card p-4 rounded-lg border border-zinc-800"
                    >
                        <div class="text-2xl font-bold text-white">890</div>
                        <div class="text-sm text-zinc-400">Following</div>
                    </div>
                    <div
                        class="stat-card p-4 rounded-lg border border-zinc-800"
                    >
                        <div class="text-2xl font-bold text-orange-500">
                            12.5K
                        </div>
                        <div class="text-sm text-zinc-400">Total Karma</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content - Same structure as your profile -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Main Content -->
                <div class="lg:col-span-2">
                    <!-- Tabs -->
                    <div
                        class="flex items-center gap-8 border-b border-zinc-800 mb-6"
                    >
                        <button
                            class="tab-active py-3 text-sm font-medium transition-colors"
                        >
                            <i class="fas fa-newspaper mr-2"></i>Posts
                        </button>
                        <button
                            class="py-3 text-sm font-medium text-zinc-400 hover:text-white transition-colors"
                        >
                            <i class="fas fa-comment mr-2"></i>Comments
                        </button>
                        <button
                            class="py-3 text-sm font-medium text-zinc-400 hover:text-white transition-colors"
                        >
                            <i class="fas fa-bookmark mr-2"></i>Saved
                        </button>
                        <button
                            class="py-3 text-sm font-medium text-zinc-400 hover:text-white transition-colors"
                        >
                            <i class="fas fa-heart mr-2"></i>Liked
                        </button>
                    </div>

                    <div v-if="isLoadingPosts" class="text-center py-8">
                        <div
                            class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500 mx-auto"
                        ></div>
                        <p class="text-gray-400 mt-2">Loading posts...</p>
                    </div>

                    <!-- Posts -->
                    <div class="space-y-4">
                        <div
                            v-for="post in posts"
                            :key="post.id"
                            class="post-card bg-zinc-900 rounded-lg border border-zinc-800 p-6 transition-colors"
                        >
                            <div class="flex items-start gap-4">
                                <div class="flex flex-col items-center gap-2">
                                    <button
                                        class="text-zinc-400 hover:text-orange-500 transition-colors"
                                    >
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                    <span
                                        class="text-sm font-medium text-orange-500"
                                        >{{ post.karma_score || 0 }}</span
                                    >
                                    <button
                                        class="text-zinc-400 hover:text-blue-500 transition-colors"
                                    >
                                        <i class="fas fa-arrow-down"></i>
                                    </button>
                                </div>
                                <div class="flex-1">
                                    <div
                                        class="flex items-center gap-2 text-xs text-zinc-400 mb-2"
                                    >
                                        <span
                                            v-if="post.category_post"
                                            class="px-2 py-0.5 bg-orange-500/10 text-orange-500 rounded text-xs font-medium"
                                        >
                                            {{ post.category_post }}
                                        </span>
                                        <span>•</span>
                                        <span>{{
                                            formatTimeAgo(
                                                post.published_at ||
                                                    post.created_at
                                            )
                                        }}</span>
                                    </div>
                                    <h3
                                        class="text-lg font-semibold text-white mb-2 hover:text-orange-500 cursor-pointer"
                                    >
                                        {{ post.title_post }}
                                    </h3>
                                    <p class="text-zinc-400 text-sm mb-4">
                                        {{ post.text_content }}
                                    </p>
                                    <div
                                        class="flex items-center gap-6 text-sm text-zinc-400"
                                    >
                                        <button
                                            class="hover:text-white transition-colors"
                                        >
                                            <i class="fas fa-comment mr-2"></i>
                                            {{ post.comments_count || 0 }}
                                            comments
                                        </button>
                                        <button
                                            class="hover:text-white transition-colors"
                                        >
                                            <i class="fas fa-share mr-2"></i
                                            >Share
                                        </button>
                                        <button
                                            class="hover:text-white transition-colors"
                                        >
                                            <i class="fas fa-bookmark mr-2"></i
                                            >Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="!isLoadingPosts && posts.length === 0"
                            class="text-center py-8"
                        >
                            <p class="text-gray-400">No posts yet</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <!-- Contribution Graph -->
                    <div
                        class="bg-zinc-900 rounded-lg border border-zinc-800 p-6"
                    >
                        <h3 class="text-lg font-semibold text-white mb-4">
                            Contribution Activity
                        </h3>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-zinc-400 w-8"
                                    >Mon</span
                                >
                                <div class="flex gap-1">
                                    <div
                                        class="contribution-day bg-zinc-800"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/20"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/40"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/60"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/80"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/40"
                                    ></div>
                                    <div
                                        class="contribution-day bg-zinc-800"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/20"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/60"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500"
                                    ></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-zinc-400 w-8"
                                    >Wed</span
                                >
                                <div class="flex gap-1">
                                    <div
                                        class="contribution-day bg-orange-500/40"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/80"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/60"
                                    ></div>
                                    <div
                                        class="contribution-day bg-zinc-800"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/20"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/80"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/40"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/60"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/80"
                                    ></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-zinc-400 w-8"
                                    >Fri</span
                                >
                                <div class="flex gap-1">
                                    <div
                                        class="contribution-day bg-orange-500/60"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/80"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/40"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/20"
                                    ></div>
                                    <div
                                        class="contribution-day bg-zinc-800"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/60"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/80"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/40"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500"
                                    ></div>
                                    <div
                                        class="contribution-day bg-orange-500/60"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex items-center justify-between mt-4 text-xs text-zinc-400"
                        >
                            <span>Less</span>
                            <div class="flex items-center gap-1">
                                <div class="contribution-day bg-zinc-800"></div>
                                <div
                                    class="contribution-day bg-orange-500/20"
                                ></div>
                                <div
                                    class="contribution-day bg-orange-500/40"
                                ></div>
                                <div
                                    class="contribution-day bg-orange-500/60"
                                ></div>
                                <div
                                    class="contribution-day bg-orange-500/80"
                                ></div>
                                <div
                                    class="contribution-day bg-orange-500"
                                ></div>
                            </div>
                            <span>More</span>
                        </div>
                    </div>

                    <!-- Achievements -->
                    <div
                        class="bg-zinc-900 rounded-lg border border-zinc-800 p-6"
                    >
                        <h3 class="text-lg font-semibold text-white mb-4">
                            Achievements
                        </h3>
                        <div class="grid grid-cols-3 gap-3">
                            <div
                                class="flex flex-col items-center gap-2 p-3 bg-zinc-800 rounded-lg"
                            >
                                <div class="text-2xl">🏆</div>
                                <span class="text-xs text-zinc-400 text-center"
                                    >Top Contributor</span
                                >
                            </div>
                            <div
                                class="flex flex-col items-center gap-2 p-3 bg-zinc-800 rounded-lg"
                            >
                                <div class="text-2xl">⭐</div>
                                <span class="text-xs text-zinc-400 text-center"
                                    >100 Posts</span
                                >
                            </div>
                            <div
                                class="flex flex-col items-center gap-2 p-3 bg-zinc-800 rounded-lg"
                            >
                                <div class="text-2xl">🔥</div>
                                <span class="text-xs text-zinc-400 text-center"
                                    >30 Day Streak</span
                                >
                            </div>
                            <div
                                class="flex flex-col items-center gap-2 p-3 bg-zinc-800 rounded-lg"
                            >
                                <div class="text-2xl">💬</div>
                                <span class="text-xs text-zinc-400 text-center"
                                    >Helpful</span
                                >
                            </div>
                            <div
                                class="flex flex-col items-center gap-2 p-3 bg-zinc-800 rounded-lg"
                            >
                                <div class="text-2xl">👥</div>
                                <span class="text-xs text-zinc-400 text-center"
                                    >Community Leader</span
                                >
                            </div>
                            <div
                                class="flex flex-col items-center gap-2 p-3 bg-zinc-800 rounded-lg"
                            >
                                <div class="text-2xl">📚</div>
                                <span class="text-xs text-zinc-400 text-center"
                                    >Educator</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Top Communities -->
                    <div
                        class="bg-zinc-900 rounded-lg border border-zinc-800 p-6"
                    >
                        <h3 class="text-lg font-semibold text-white mb-4">
                            Top Communities
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-xs font-bold"
                                    >
                                        JS
                                    </div>
                                    <div>
                                        <div
                                            class="text-sm font-medium text-white"
                                        >
                                            r/javascript
                                        </div>
                                        <div class="text-xs text-zinc-400">
                                            234 posts
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-xs font-bold"
                                    ></div>
                                    <div>
                                        <div
                                            class="text-sm font-medium text-white"
                                        >
                                            r/reactjs
                                        </div>
                                        <div class="text-xs text-zinc-400">
                                            189 posts
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-xs font-bold"
                                    >
                                        N
                                    </div>
                                    <div>
                                        <div
                                            class="text-sm font-medium text-white"
                                        >
                                            r/node
                                        </div>
                                        <div class="text-xs text-zinc-400">
                                            156 posts
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Skills -->
                    <div
                        class="bg-zinc-900 rounded-lg border border-zinc-800 p-6"
                    >
                        <h3 class="text-lg font-semibold text-white mb-4">
                            Skills & Expertise
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <span
                                class="px-3 py-1 bg-orange-500/10 text-orange-500 rounded-full text-sm"
                                >JavaScript</span
                            >
                            <span
                                class="px-3 py-1 bg-blue-500/10 text-blue-500 rounded-full text-sm"
                                >React</span
                            >
                            <span
                                class="px-3 py-1 bg-green-500/10 text-green-500 rounded-full text-sm"
                                >Node.js</span
                            >
                            <span
                                class="px-3 py-1 bg-purple-500/10 text-purple-500 rounded-full text-sm"
                                >TypeScript</span
                            >
                            <span
                                class="px-3 py-1 bg-pink-500/10 text-pink-500 rounded-full text-sm"
                                >GraphQL</span
                            >
                            <span
                                class="px-3 py-1 bg-yellow-500/10 text-yellow-500 rounded-full text-sm"
                                >AWS</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
