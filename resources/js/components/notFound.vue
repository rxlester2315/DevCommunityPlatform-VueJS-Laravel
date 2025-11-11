<script setup>
import { useRoute, useRouter } from "vue-router";
import { ref, onMounted } from "vue";
const router = useRouter();

const goProfile = () => {
    router.push("/profiles");
};

const goHome = () => {
    router.push("/");
};

const messages = [
    "Did you try turning it off and on again?",
    "This page went to the shadow realm. 👻",
    "404: Brain.exe has stopped working",
    "Lost in the digital void... 🌌",
    "Looks like this URL took a wrong turn at Albuquerque",
    "ERROR: This page is playing hide and seek... and winning",
    "Houston, we have a problem... 🚀",
    "The page you seek has entered the void",
];

const randomMessage = ref("");
onMounted(() => {
    randomMessage.value = messages[Math.floor(Math.random() * messages.length)];
});
</script>

<template>
    <div>
        <body class="bg-[#0a0a0a] text-white font-sans">
            <!-- Navigation -->
            <nav
                class="fixed top-0 w-full bg-[#0a0a0a]/80 backdrop-blur-md border-b border-[#1a1a1a] z-50"
            >
                <div
                    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between"
                >
                    <button
                        @click="goHome"
                        class="text-2xl font-bold text-white hover:text-[#ff6b35] transition-colors"
                    >
                        DevFeed
                    </button>
                    <div class="flex items-center gap-6">
                        <button
                            @click="goHome"
                            class="text-gray-400 hover:text-white transition-colors"
                        >
                            Home
                        </button>
                        <a
                            @click="goProfile"
                            class="text-gray-400 hover:text-white transition-colors"
                            >Profile</a
                        >
                    </div>
                </div>
            </nav>

            <!-- 404 Section -->
            <main
                class="min-h-screen flex items-center justify-center pt-20 px-4"
            >
                <div class="text-center max-w-2xl mx-auto">
                    <!-- Animated 404 -->
                    <div class="mb-8">
                        <div class="float-animation pulse-glow inline-block">
                            <div
                                class="text-9xl font-bold bg-gradient-to-r from-[#ff6b35] to-[#ff8c5a] bg-clip-text text-transparent"
                            >
                                404
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div class="mb-12">
                        <h1 class="text-4xl md:text-5xl font-bold mb-4">
                            Page Not Found
                        </h1>
                        <p class="text-xl text-gray-400 mb-6">
                            Oops! It looks like you've wandered into uncharted
                            territory. The page you're looking for doesn't exist
                            or has been moved.
                        </p>

                        <!-- Code Block Style Message -->
                        <div
                            class="bg-[#1a1a1a] border border-[#2a2a2a] rounded-lg p-6 text-left mb-8 font-mono text-sm"
                        >
                            <div class="text-gray-500">
                                &gt; error: page_not_found
                            </div>
                            <div class="text-gray-500">
                                &gt; status:
                                <span class="text-[#ff6b35]">404</span>
                            </div>
                            <div class="text-gray-500">
                                &gt; path:
                                <span class="text-blue-400"
                                    >"{window.location.pathname}"</span
                                >
                            </div>
                            <div class="text-gray-500">
                                &gt; suggestion:
                                <span class="text-green-400"
                                    >return home or navigate back</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button
                            @click="goHome"
                            class="px-8 py-3 bg-[#ff6b35] text-white rounded-lg font-semibold hover:bg-[#ff5520] transition-all duration-300 transform hover:scale-105"
                        >
                            Back to Home
                        </button>
                        <button
                            @click="goHome"
                            class="px-8 py-3 bg-[#1a1a1a] text-white border border-[#ff6b35] rounded-lg font-semibold hover:bg-[#ff6b35]/10 transition-all duration-300"
                        >
                            Explore Feed
                        </button>
                    </div>

                    <!-- Helpful Links -->
                    <div class="mt-16 pt-8 border-t border-[#2a2a2a]">
                        <p class="text-gray-400 mb-6">
                            Or check out these popular sections:
                        </p>
                        <div class="flex flex-wrap justify-center gap-4">
                            <a
                                href="profile.html"
                                class="px-4 py-2 text-gray-400 hover:text-[#ff6b35] transition-colors flex items-center gap-2"
                            >
                                <span>👤</span> Profile
                            </a>
                            <a
                                href="followers.html"
                                class="px-4 py-2 text-gray-400 hover:text-[#ff6b35] transition-colors flex items-center gap-2"
                            >
                                <span>👥</span> Community
                            </a>
                            <a
                                href="index.html#about"
                                class="px-4 py-2 text-gray-400 hover:text-[#ff6b35] transition-colors flex items-center gap-2"
                            >
                                <span>ℹ️</span> About
                            </a>
                        </div>
                    </div>

                    <!-- Fun Messages -->
                    <div class="mt-12 text-gray-500 text-sm">
                        <p id="random-message"></p>
                    </div>
                </div>
            </main>
        </body>
    </div>
</template>
