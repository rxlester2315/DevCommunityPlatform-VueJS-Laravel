<template>
    <RouterView />
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { RouterView } from "vue-router";
import { usePusher } from "@/components/composables/usePusher";
import { auth } from "../utils/auth";
import axios from "axios";

const { initPusher, disconnect, notifications, isConnected } = usePusher();
const user = ref(null);

const initializeApp = async () => {
    try {
        console.log("🚀 Starting Pusher initialization...");

        const userData = auth.getUser();
        console.log("📋 User data from auth:", userData);

        if (!userData) {
            console.log("🔍 No user in auth, fetching from API...");
            const response = await axios.get("/api/user");
            user.value = response.data;
            console.log("✅ User from API:", response.data);
        } else {
            user.value = userData;
            console.log("✅ Using user from auth");
        }

        if (user.value) {
            console.log("🔌 Initializing Pusher for user ID:", user.value.id);
            initPusher(user.value.id);
            console.log("✅ Pusher initialization completed");
        } else {
            console.log("❌ No user found, skipping Pusher");
        }
    } catch (error) {
        console.error("❌ Error initializing app:", error);
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
