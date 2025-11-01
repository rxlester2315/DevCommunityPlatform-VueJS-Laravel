<template>
    <div class="flex items-center gap-1">
        <!-- Upvote Button -->
        <button
            @click="upvote"
            :class="[
                'vote-btn upvote transition-colors duration-200 p-1 rounded',
                {
                    'text-orange-500 bg-orange-500/10': userVote === 'up',
                    'text-gray-400 hover:text-white hover:bg-orange-500':
                        userVote !== 'up',
                },
            ]"
            :disabled="loading"
            title="Upvote"
        >
            <i class="ri-arrow-up-line text-lg"></i>
        </button>

        <!-- Karma Score -->
        <span
            class="text-xs font-semibold px-2 min-w-[20px] text-center"
            :class="{
                'text-orange-500': karmaScore > 0,
                'text-blue-500': karmaScore < 0,
                'text-gray-400': karmaScore === 0,
            }"
        >
            {{ karmaScore }}
        </span>

        <!-- Downvote Button -->
        <button
            @click="downvote"
            :class="[
                'vote-btn downvote transition-colors duration-200 p-1 rounded',
                {
                    'text-blue-500 bg-blue-500/10': userVote === 'down',
                    'text-gray-400 hover:text-white hover:bg-blue-500':
                        userVote !== 'down',
                },
            ]"
            :disabled="loading"
            title="Downvote"
        >
            <i class="ri-arrow-down-line text-lg"></i>
        </button>
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import Swal from "sweetalert2";

const props = defineProps({
    postId: {
        type: Number,
        required: true,
    },
    initialKarma: {
        type: Number,
        default: 0,
    },
    initialUserVote: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["voted"]);

const userVote = ref(props.initialUserVote);
const karmaScore = ref(props.initialKarma);
const loading = ref(false);

const makeVote = async (type) => {
    if (loading.value) return;

    loading.value = true;

    try {
        const response = await axios.post(
            `/api/posts/${props.postId}/karma/${type}`
        );

        userVote.value = response.data.user_vote;
        karmaScore.value = response.data.karma_score;

        emit("voted", {
            postId: props.postId,
            score: karmaScore.value,
            userVote: userVote.value,
        });
    } catch (error) {
        console.error("Vote error:", error);
        if (error.response?.status === 401) {
            Swal.fire({
                icon: "error",
                title: "Login Required",
                text: "Please login to vote",
                timer: 3000,
            });
        }
    } finally {
        loading.value = false;
    }
};

const upvote = () => makeVote("upvote");
const downvote = () => makeVote("downvote");
</script>

<style scoped>
.vote-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.vote-btn:disabled:hover {
    background: transparent !important;
}
</style>
