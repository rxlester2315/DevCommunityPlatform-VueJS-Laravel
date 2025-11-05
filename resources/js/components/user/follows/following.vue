<script setup>
import { useRoute, useRouter } from "vue-router";

const router = useRouter();

const followers = () => {
    router.push("/profile/followers");
};
</script>

<template>
    <div>
        <body>
            <!-- Navigation Header -->
            <header
                class="border-b border-[#27272a] bg-[#0a0a0a] sticky top-0 z-50"
            >
                <div
                    class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between"
                >
                    <div class="flex items-center gap-8">
                        <a
                            href="profile.html"
                            class="text-white text-xl font-bold hover:text-[#ff6b35] transition"
                            >DevBlog</a
                        >
                        <nav class="hidden md:flex gap-6">
                            <a
                                href="feed.html"
                                class="text-[#71717a] hover:text-white transition"
                                >Feed</a
                            >
                            <a
                                href="profile.html"
                                class="text-[#71717a] hover:text-white transition"
                                >Profile</a
                            >
                            <a href="following.html" class="text-white"
                                >Connections</a
                            >
                        </nav>
                    </div>
                    <div class="flex items-center gap-4">
                        <input
                            type="text"
                            placeholder="Search users..."
                            class="hidden sm:block w-48"
                        />
                        <button
                            class="w-10 h-10 rounded-full bg-[#27272a] hover:bg-[#3a3a3e] transition flex items-center justify-center text-white"
                        >
                            ⚙️
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="max-w-4xl mx-auto px-6 py-8">
                <!-- Header Section -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-white mb-2">
                        Connections
                    </h1>
                    <p class="text-[#71717a]">
                        Manage your followers and following
                    </p>
                </div>

                <!-- Tab Navigation -->
                <div class="flex gap-8 border-b border-[#27272a] mb-8">
                    <button
                        class="tab-active pb-4 font-semibold transition"
                        onclick="switchTab('followers')"
                    >
                        Following <span class="text-[#71717a] ml-2">1,234</span>
                    </button>
                    <button
                        class="tab-inactive pb-4 font-semibold transition"
                        @click="followers"
                    >
                        Follower <span class="text-[#71717a] ml-2">567</span>
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <input
                        type="text"
                        placeholder="Search followers..."
                        class="w-full"
                    />
                </div>

                <!-- Followers Tab Content -->
                <div id="followers" class="space-y-4">
                    <!-- Follower Card 1 -->
                    <div class="user-card p-6 flex items-start justify-between">
                        <div class="flex gap-4 flex-1">
                            <div class="avatar">JD</div>
                            <div class="flex-1">
                                <h3 class="text-white font-semibold text-lg">
                                    John Developer
                                </h3>
                                <p class="text-[#71717a] text-sm">@johndev</p>
                                <p class="text-[#a1a1a6] text-sm mt-2">
                                    Senior Full-stack Engineer passionate about
                                    React and Node.js
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="tag">React</span>
                                    <span class="tag">Node.js</span>
                                    <span class="tag">TypeScript</span>
                                </div>
                                <p class="text-[#71717a] text-xs mt-3">
                                    Followed you 2 weeks ago
                                </p>
                            </div>
                        </div>
                        <button class="btn-follow">Follow Back</button>
                    </div>

                    <!-- Follower Card 2 -->
                    <div class="user-card p-6 flex items-start justify-between">
                        <div class="flex gap-4 flex-1">
                            <div
                                class="avatar"
                                style="
                                    background: linear-gradient(
                                        135deg,
                                        #667eea 0%,
                                        #764ba2 100%
                                    );
                                "
                            >
                                SD
                            </div>
                            <div class="flex-1">
                                <h3 class="text-white font-semibold text-lg">
                                    Sarah Designer
                                </h3>
                                <p class="text-[#71717a] text-sm">
                                    @sarahdesign
                                </p>
                                <p class="text-[#a1a1a6] text-sm mt-2">
                                    UI/UX Designer exploring the intersection of
                                    design and development
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="tag">UI/UX</span>
                                    <span class="tag">Figma</span>
                                    <span class="tag">Design System</span>
                                </div>
                                <p class="text-[#71717a] text-xs mt-3">
                                    Following you
                                </p>
                            </div>
                        </div>
                        <button class="btn-follow">Follow Back</button>
                    </div>

                    <!-- Follower Card 3 -->
                    <div class="user-card p-6 flex items-start justify-between">
                        <div class="flex gap-4 flex-1">
                            <div
                                class="avatar"
                                style="
                                    background: linear-gradient(
                                        135deg,
                                        #f093fb 0%,
                                        #f5576c 100%
                                    );
                                "
                            >
                                MC
                            </div>
                            <div class="flex-1">
                                <h3 class="text-white font-semibold text-lg">
                                    Mike Chen
                                </h3>
                                <p class="text-[#71717a] text-sm">@mikechen</p>
                                <p class="text-[#a1a1a6] text-sm mt-2">
                                    DevOps Engineer | Cloud Infrastructure |
                                    Kubernetes enthusiast
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="tag">DevOps</span>
                                    <span class="tag">Kubernetes</span>
                                    <span class="tag">AWS</span>
                                </div>
                                <p class="text-[#71717a] text-xs mt-3">
                                    Followed you 1 month ago
                                </p>
                            </div>
                        </div>
                        <button class="btn-follow">Follow Back</button>
                    </div>

                    <!-- Follower Card 4 -->
                    <div class="user-card p-6 flex items-start justify-between">
                        <div class="flex gap-4 flex-1">
                            <div
                                class="avatar"
                                style="
                                    background: linear-gradient(
                                        135deg,
                                        #4facfe 0%,
                                        #00f2fe 100%
                                    );
                                "
                            >
                                LP
                            </div>
                            <div class="flex-1">
                                <h3 class="text-white font-semibold text-lg">
                                    Lisa Park
                                </h3>
                                <p class="text-[#71717a] text-sm">@lisapark</p>
                                <p class="text-[#a1a1a6] text-sm mt-2">
                                    Backend specialist | Python | Database
                                    optimization expert
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="tag">Python</span>
                                    <span class="tag">Backend</span>
                                    <span class="tag">PostgreSQL</span>
                                </div>
                                <p class="text-[#71717a] text-xs mt-3">
                                    Following you
                                </p>
                            </div>
                        </div>
                        <button class="btn-following">Following</button>
                    </div>
                </div>

                <!-- Following Tab Content (Hidden by default) -->
                <div id="following" class="space-y-4 hidden">
                    <!-- Following Card 1 -->
                    <div class="user-card p-6 flex items-start justify-between">
                        <div class="flex gap-4 flex-1">
                            <div
                                class="avatar"
                                style="
                                    background: linear-gradient(
                                        135deg,
                                        #fa709a 0%,
                                        #fee140 100%
                                    );
                                "
                            >
                                AB
                            </div>
                            <div class="flex-1">
                                <h3 class="text-white font-semibold text-lg">
                                    Alex Bell
                                </h3>
                                <p class="text-[#71717a] text-sm">@alexbell</p>
                                <p class="text-[#a1a1a6] text-sm mt-2">
                                    Open source contributor | JavaScript wizard
                                    | Teaching web dev
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="tag">JavaScript</span>
                                    <span class="tag">Open Source</span>
                                    <span class="tag">Web Dev</span>
                                </div>
                                <p class="text-[#71717a] text-xs mt-3">
                                    23.5K followers
                                </p>
                            </div>
                        </div>
                        <button class="btn-following">Following</button>
                    </div>

                    <!-- Following Card 2 -->
                    <div class="user-card p-6 flex items-start justify-between">
                        <div class="flex gap-4 flex-1">
                            <div
                                class="avatar"
                                style="
                                    background: linear-gradient(
                                        135deg,
                                        #a8edea 0%,
                                        #fed6e3 100%
                                    );
                                "
                            >
                                RC
                            </div>
                            <div class="flex-1">
                                <h3 class="text-white font-semibold text-lg">
                                    Rachel Chen
                                </h3>
                                <p class="text-[#71717a] text-sm">
                                    @rachelchen
                                </p>
                                <p class="text-[#a1a1a6] text-sm mt-2">
                                    Tech writer | Developer advocacy | Speaking
                                    at conferences
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="tag">Tech Writing</span>
                                    <span class="tag">Advocacy</span>
                                    <span class="tag">Speaking</span>
                                </div>
                                <p class="text-[#71717a] text-xs mt-3">
                                    45.2K followers
                                </p>
                            </div>
                        </div>
                        <button class="btn-following">Following</button>
                    </div>

                    <!-- Following Card 3 -->
                    <div class="user-card p-6 flex items-start justify-between">
                        <div class="flex gap-4 flex-1">
                            <div
                                class="avatar"
                                style="
                                    background: linear-gradient(
                                        135deg,
                                        #ff9a56 0%,
                                        #ff6a88 100%
                                    );
                                "
                            >
                                TW
                            </div>
                            <div class="flex-1">
                                <h3 class="text-white font-semibold text-lg">
                                    Thomas Wilson
                                </h3>
                                <p class="text-[#71717a] text-sm">
                                    @thomaswilson
                                </p>
                                <p class="text-[#a1a1a6] text-sm mt-2">
                                    Security researcher | Bug bounty hunter |
                                    AppSec specialist
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="tag">Security</span>
                                    <span class="tag">AppSec</span>
                                    <span class="tag">Bug Bounty</span>
                                </div>
                                <p class="text-[#71717a] text-xs mt-3">
                                    12.8K followers
                                </p>
                            </div>
                        </div>
                        <button class="btn-following">Following</button>
                    </div>

                    <!-- Following Card 4 -->
                    <div class="user-card p-6 flex items-start justify-between">
                        <div class="flex gap-4 flex-1">
                            <div
                                class="avatar"
                                style="
                                    background: linear-gradient(
                                        135deg,
                                        #11998e 0%,
                                        #38ef7d 100%
                                    );
                                "
                            >
                                NK
                            </div>
                            <div class="flex-1">
                                <h3 class="text-white font-semibold text-lg">
                                    Nina Kumar
                                </h3>
                                <p class="text-[#71717a] text-sm">@ninakumar</p>
                                <p class="text-[#a1a1a6] text-sm mt-2">
                                    AI/ML engineer | Data science | Building
                                    intelligent systems
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="tag">AI/ML</span>
                                    <span class="tag">Python</span>
                                    <span class="tag">Data Science</span>
                                </div>
                                <p class="text-[#71717a] text-xs mt-3">
                                    34.1K followers
                                </p>
                            </div>
                        </div>
                        <button class="btn-following">Following</button>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="border-t border-[#27272a] bg-[#0a0a0a] mt-16">
                <div class="max-w-6xl mx-auto px-6 py-8">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8">
                        <div>
                            <h4 class="text-white font-semibold mb-4">
                                Product
                            </h4>
                            <ul class="space-y-2 text-[#71717a] text-sm">
                                <li>
                                    <a
                                        href="#"
                                        class="hover:text-white transition"
                                        >Features</a
                                    >
                                </li>
                                <li>
                                    <a
                                        href="#"
                                        class="hover:text-white transition"
                                        >Pricing</a
                                    >
                                </li>
                                <li>
                                    <a
                                        href="#"
                                        class="hover:text-white transition"
                                        >Security</a
                                    >
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold mb-4">
                                Company
                            </h4>
                            <ul class="space-y-2 text-[#71717a] text-sm">
                                <li>
                                    <a
                                        href="#"
                                        class="hover:text-white transition"
                                        >About</a
                                    >
                                </li>
                                <li>
                                    <a
                                        href="#"
                                        class="hover:text-white transition"
                                        >Blog</a
                                    >
                                </li>
                                <li>
                                    <a
                                        href="#"
                                        class="hover:text-white transition"
                                        >Careers</a
                                    >
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold mb-4">
                                Resources
                            </h4>
                            <ul class="space-y-2 text-[#71717a] text-sm">
                                <li>
                                    <a
                                        href="#"
                                        class="hover:text-white transition"
                                        >Docs</a
                                    >
                                </li>
                                <li>
                                    <a
                                        href="#"
                                        class="hover:text-white transition"
                                        >API</a
                                    >
                                </li>
                                <li>
                                    <a
                                        href="#"
                                        class="hover:text-white transition"
                                        >Community</a
                                    >
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold mb-4">Legal</h4>
                            <ul class="space-y-2 text-[#71717a] text-sm">
                                <li>
                                    <a
                                        href="#"
                                        class="hover:text-white transition"
                                        >Privacy</a
                                    >
                                </li>
                                <li>
                                    <a
                                        href="#"
                                        class="hover:text-white transition"
                                        >Terms</a
                                    >
                                </li>
                                <li>
                                    <a
                                        href="#"
                                        class="hover:text-white transition"
                                        >Contact</a
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div
                        class="border-t border-[#27272a] pt-8 flex flex-col md:flex-row justify-between items-center text-[#71717a] text-sm"
                    >
                        <p>&copy; 2025 DevBlog. All rights reserved.</p>
                        <div class="flex gap-6 mt-4 md:mt-0">
                            <a href="#" class="hover:text-white transition"
                                >GitHub</a
                            >
                            <a href="#" class="hover:text-white transition"
                                >Twitter</a
                            >
                            <a href="#" class="hover:text-white transition"
                                >LinkedIn</a
                            >
                        </div>
                    </div>
                </div>
            </footer>
        </body>
    </div>
</template>
