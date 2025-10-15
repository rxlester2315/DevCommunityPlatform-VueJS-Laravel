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
                            <a
                                href="feed.html"
                                class="text-zinc-400 hover:text-white transition-colors"
                                >Feed</a
                            >
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
                        <div
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-pink-500"
                        ></div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Profile Header -->
        <div
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
                                class="w-full h-full rounded-full bg-zinc-900 flex items-center justify-center text-4xl font-bold"
                            >
                                JD
                            </div>
                        </div>
                        <button
                            class="absolute bottom-0 right-0 w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center hover:bg-orange-600 transition-colors"
                        >
                            <i class="fas fa-camera text-white text-sm"></i>
                        </button>
                    </div>

                    <!-- User Info -->
                    <div class="flex-1">
                        <div
                            class="flex flex-col md:flex-row md:items-center justify-between gap-4"
                        >
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-1">
                                    John Doe
                                </h1>
                                <p class="text-zinc-400">
                                    @johndoe • Joined January 2024
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <button
                                    class="px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors font-medium"
                                >
                                    <i class="fas fa-user-plus mr-2"></i>Follow
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
                            Full-stack developer passionate about React,
                            Node.js, and building scalable web applications.
                            Open source contributor and tech blogger.
                        </p>
                        <div class="flex items-center gap-6 mt-4 text-sm">
                            <a
                                href="#"
                                class="text-zinc-400 hover:text-orange-500 transition-colors"
                            >
                                <i class="fas fa-map-marker-alt mr-2"></i>San
                                Francisco, CA
                            </a>
                            <a
                                href="#"
                                class="text-zinc-400 hover:text-orange-500 transition-colors"
                            >
                                <i class="fas fa-link mr-2"></i>johndoe.dev
                            </a>
                            <a
                                href="#"
                                class="text-zinc-400 hover:text-orange-500 transition-colors"
                            >
                                <i class="fab fa-github mr-2"></i
                                >github.com/johndoe
                            </a>
                            <a
                                href="#"
                                class="text-zinc-400 hover:text-orange-500 transition-colors"
                            >
                                <i class="fab fa-twitter mr-2"></i>@johndoe
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                    <div
                        class="stat-card p-4 rounded-lg border border-zinc-800"
                    >
                        <div class="text-2xl font-bold text-white">1,234</div>
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

        <!-- Main Content -->
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

                    <!-- Posts -->
                    <div class="space-y-4">
                        <!-- Post Card 1 -->
                        <div
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
                                        >342</span
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
                                            class="px-2 py-1 bg-orange-500/10 text-orange-500 rounded"
                                            >JavaScript</span
                                        >
                                        <span>•</span>
                                        <span>Posted 2 hours ago</span>
                                    </div>
                                    <h3
                                        class="text-lg font-semibold text-white mb-2 hover:text-orange-500 cursor-pointer"
                                    >
                                        Building a Real-time Chat Application
                                        with WebSockets
                                    </h3>
                                    <p class="text-zinc-400 text-sm mb-4">
                                        Learn how to build a scalable real-time
                                        chat application using WebSockets,
                                        Node.js, and React. This comprehensive
                                        guide covers everything from setup to
                                        deployment...
                                    </p>
                                    <div
                                        class="flex items-center gap-6 text-sm text-zinc-400"
                                    >
                                        <button
                                            class="hover:text-white transition-colors"
                                        >
                                            <i class="fas fa-comment mr-2"></i
                                            >45 comments
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

                        <!-- Post Card 2 -->
                        <div
                            class="post-card bg-zinc-900 rounded-lg border border-zinc-800 p-6 transition-colors"
                        >
                            <div class="flex items-start gap-4">
                                <div class="flex flex-col items-center gap-2">
                                    <button
                                        class="text-orange-500 hover:text-orange-600 transition-colors"
                                    >
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                    <span
                                        class="text-sm font-medium text-orange-500"
                                        >1.2K</span
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
                                            class="px-2 py-1 bg-blue-500/10 text-blue-500 rounded"
                                            >React</span
                                        >
                                        <span>•</span>
                                        <span>Posted 1 day ago</span>
                                    </div>
                                    <h3
                                        class="text-lg font-semibold text-white mb-2 hover:text-orange-500 cursor-pointer"
                                    >
                                        React Server Components: A Deep Dive
                                    </h3>
                                    <p class="text-zinc-400 text-sm mb-4">
                                        Exploring the new React Server
                                        Components architecture and how it
                                        changes the way we build React
                                        applications...
                                    </p>
                                    <div
                                        class="flex items-center gap-6 text-sm text-zinc-400"
                                    >
                                        <button
                                            class="hover:text-white transition-colors"
                                        >
                                            <i class="fas fa-comment mr-2"></i
                                            >128 comments
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

                        <!-- Post Card 3 -->
                        <div
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
                                        >856</span
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
                                            class="px-2 py-1 bg-green-500/10 text-green-500 rounded"
                                            >Node.js</span
                                        >
                                        <span>•</span>
                                        <span>Posted 3 days ago</span>
                                    </div>
                                    <h3
                                        class="text-lg font-semibold text-white mb-2 hover:text-orange-500 cursor-pointer"
                                    >
                                        Optimizing Node.js Performance: Best
                                        Practices
                                    </h3>
                                    <p class="text-zinc-400 text-sm mb-4">
                                        A collection of proven techniques to
                                        improve your Node.js application
                                        performance, from caching strategies to
                                        database optimization...
                                    </p>
                                    <div
                                        class="flex items-center gap-6 text-sm text-zinc-400"
                                    >
                                        <button
                                            class="hover:text-white transition-colors"
                                        >
                                            <i class="fas fa-comment mr-2"></i
                                            >67 comments
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
                    </div>
                </div>

                <!-- Right Column - Sidebar -->
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
                                    >
                                        R
                                    </div>
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
