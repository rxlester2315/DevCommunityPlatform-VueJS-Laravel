<template>
    <RouterView />
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { RouterView, useRouter } from "vue-router";
import { usePusher } from "@/components/composables/usePusher";
import { auth } from "../utils/auth";
import axios from "axios";

const { initPusher, disconnect, notifications, isConnected } = usePusher();
const user = ref(null);
const router = useRouter();

// Last Edited - 10/30/25 : Improved logging and error handling during Pusher initialization
// Error Encountered:  Unauthorized error when user is not logged in
// Errors : Problem: Previously, the code tried to fetch /api/user without checking if the user is actually authenticated
// Errors : Problem: Pusher was initializing even on login page where no user exists

// Solutions :
// 1. Added a check to see if the user is authenticated before making the API call
// 2. Added Detect if we're on auth pages and skip Pusher initialization if we're on login page
const initializeApp = async () => {
    try {
        console.log("🚀 Starting Pusher initialization...");

        const userData = auth.getUser();
        console.log("📋 User data from auth:", userData);

        // Solution 1 .
        // Check if user has a valid token before making API call
        const hasValidToken = auth.isAuthenticated(); // this is from  auth utils where in it check whether the user is authenticated or not

        // Check if current page is login page - Solution 2
        const isLoginPage =
            window.location.pathname === "/login" ||
            router.currentRoute.value.path === "/login";

        // only fetch user from API if no user in auth and has valid token and not on login page
        if (!userData && hasValidToken && !isLoginPage) {
            console.log(
                "🔍 No user in auth but has token, fetching from API..."
            );
            const response = await axios.get("/api/user");
            user.value = response.data;
            console.log("✅ User from API:", response.data);
        } else if (userData) {
            user.value = userData;
            console.log("✅ Using user from auth");
        } else {
            console.log("👤 No user data available, skipping Pusher");
            return; // Stop execution here
        }

        if (user.value) {
            console.log("🔌 Initializing Pusher for user ID:", user.value.id);
            initPusher(user.value.id);
            console.log("✅ Pusher initialization completed");
        }
    } catch (error) {
        if (error.response?.status === 401) {
            console.log("🔐 Unauthorized - user not logged in");
            // Clear any invalid tokens
            auth.logout();
        } else {
            console.error("❌ Error initializing app:", error);
        }
    }
};

onMounted(() => {
    console.log("🏗️ App mounted, initializing...");
    initializeApp();
});

onUnmounted(() => {
    console.log("🧹 Cleaning up Pusher...");
    disconnect();
});
</script>
