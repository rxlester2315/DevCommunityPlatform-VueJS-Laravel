<script setup>
import { useRouter } from "vue-router";

const router = useRouter();

const login = () => {
    router.push("/login");
};
</script>

<template>
    <body class="bg-background text-gray-200 font-sans antialiased">
        <!-- Header -->
        <header
            class="border-b border-border sticky top-0 bg-background/95 backdrop-blur-sm z-50"
        >
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-8">
                        <a
                            href="#"
                            class="flex items-center gap-2 text-xl font-semibold text-white"
                        >
                            <div
                                class="w-8 h-8 bg-primary rounded-full flex items-center justify-center"
                            >
                                <span class="text-background font-bold text-sm"
                                    >&lt;/&gt;</span
                                >
                            </div>
                            DevFeed
                        </a>
                        <div class="hidden md:flex items-center gap-6">
                            <a
                                href="#articles"
                                class="text-gray-300 hover:text-white transition-colors text-sm"
                                >Articles</a
                            >
                            <a
                                href="#topics"
                                class="text-gray-300 hover:text-white transition-colors text-sm"
                                >Topics</a
                            >
                            <a
                                href="#about"
                                class="text-gray-300 hover:text-white transition-colors text-sm"
                                >About</a
                            >
                            <a
                                href="#newsletter"
                                class="text-gray-300 hover:text-white transition-colors text-sm"
                                >Newsletter</a
                            >
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <button
                            class="hidden sm:flex items-center gap-2 px-3 py-1.5 text-sm text-gray-400 bg-surface border border-border rounded-md hover:border-gray-600 transition-colors"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                            Search
                            <kbd
                                class="ml-2 px-1.5 py-0.5 text-xs bg-background border border-border rounded"
                                >⌘K</kbd
                            >
                        </button>
                        <button
                            @click="login"
                            class="px-4 py-1.5 text-sm font-medium text-background bg-primary hover:bg-primary-hover rounded-md transition-colors"
                        >
                            Login
                        </button>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
            <div class="max-w-4xl">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 bg-surface border border-border rounded-full text-sm text-gray-300 mb-6"
                >
                    <span
                        class="w-2 h-2 bg-primary rounded-full animate-pulse"
                    ></span>
                    Weekly insights on web development
                </div>
                <h1
                    class="text-5xl sm:text-6xl lg:text-7xl font-bold text-white mb-6 leading-tight text-balance"
                >
                    Code, Learn, and Build Better
                </h1>
                <p
                    class="text-xl text-gray-400 mb-8 leading-relaxed max-w-2xl text-pretty"
                >
                    Deep dives into modern web development, JavaScript
                    frameworks, system design, and developer productivity.
                    Written by developers, for developers.
                </p>
                <div class="flex flex-wrap items-center gap-4">
                    <a
                        href="#articles"
                        class="px-6 py-3 bg-primary hover:bg-primary-hover text-background font-medium rounded-md transition-colors"
                    >
                        Read Articles
                    </a>
                    <a
                        href="#about"
                        class="px-6 py-3 bg-surface hover:bg-gray-800 text-white border border-border hover:border-gray-600 font-medium rounded-md transition-colors"
                    >
                        About the Author
                    </a>
                </div>
            </div>
        </section>

        <!-- Featured Articles -->
        <section
            id="articles"
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16"
        >
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-white">Featured Articles</h2>
                <a
                    href="#"
                    class="text-primary hover:text-primary-hover text-sm font-medium flex items-center gap-1"
                >
                    View all
                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>
                </a>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Article Card 1 -->
                <article
                    class="bg-surface border border-border rounded-lg overflow-hidden hover:border-gray-600 transition-colors group"
                >
                    <div
                        class="aspect-video bg-gradient-to-br from-primary/20 to-background border-b border-border flex items-center justify-center"
                    >
                        <svg
                            class="w-16 h-16 text-primary/40"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
                            />
                        </svg>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span
                                class="px-2 py-1 bg-primary/10 text-primary text-xs font-medium rounded"
                                >React</span
                            >
                            <span class="text-muted text-xs">5 min read</span>
                        </div>
                        <h3
                            class="text-xl font-semibold text-white mb-2 group-hover:text-primary transition-colors"
                        >
                            Building Scalable React Applications
                        </h3>
                        <p class="text-gray-400 text-sm mb-4 leading-relaxed">
                            Learn best practices for structuring large-scale
                            React applications with proper state management and
                            component architecture.
                        </p>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted">Jan 15, 2025</span>
                            <a
                                href="#"
                                class="text-primary hover:text-primary-hover font-medium"
                                >Read more →</a
                            >
                        </div>
                    </div>
                </article>

                <!-- Article Card 2 -->
                <article
                    class="bg-surface border border-border rounded-lg overflow-hidden hover:border-gray-600 transition-colors group"
                >
                    <div
                        class="aspect-video bg-gradient-to-br from-blue-500/20 to-background border-b border-border flex items-center justify-center"
                    >
                        <svg
                            class="w-16 h-16 text-blue-500/40"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"
                            />
                        </svg>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span
                                class="px-2 py-1 bg-blue-500/10 text-blue-400 text-xs font-medium rounded"
                                >DevOps</span
                            >
                            <span class="text-muted text-xs">8 min read</span>
                        </div>
                        <h3
                            class="text-xl font-semibold text-white mb-2 group-hover:text-primary transition-colors"
                        >
                            Mastering Docker for Development
                        </h3>
                        <p class="text-gray-400 text-sm mb-4 leading-relaxed">
                            A comprehensive guide to containerizing your
                            development environment and streamlining your
                            deployment workflow.
                        </p>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted">Jan 12, 2025</span>
                            <a
                                href="#"
                                class="text-primary hover:text-primary-hover font-medium"
                                >Read more →</a
                            >
                        </div>
                    </div>
                </article>

                <!-- Article Card 3 -->
                <article
                    class="bg-surface border border-border rounded-lg overflow-hidden hover:border-gray-600 transition-colors group"
                >
                    <div
                        class="aspect-video bg-gradient-to-br from-green-500/20 to-background border-b border-border flex items-center justify-center"
                    >
                        <svg
                            class="w-16 h-16 text-green-500/40"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M13 10V3L4 14h7v7l9-11h-7z"
                            />
                        </svg>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span
                                class="px-2 py-1 bg-green-500/10 text-green-400 text-xs font-medium rounded"
                                >Performance</span
                            >
                            <span class="text-muted text-xs">6 min read</span>
                        </div>
                        <h3
                            class="text-xl font-semibold text-white mb-2 group-hover:text-primary transition-colors"
                        >
                            Web Performance Optimization Tips
                        </h3>
                        <p class="text-gray-400 text-sm mb-4 leading-relaxed">
                            Practical techniques to improve your website's
                            loading speed and deliver a better user experience.
                        </p>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted">Jan 10, 2025</span>
                            <a
                                href="#"
                                class="text-primary hover:text-primary-hover font-medium"
                                >Read more →</a
                            >
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <!-- Topics Section -->
        <section
            id="topics"
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16"
        >
            <h2 class="text-3xl font-bold text-white mb-8">Browse by Topic</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <a
                    href="#"
                    class="p-4 bg-surface border border-border rounded-lg hover:border-primary hover:bg-surface/80 transition-all group"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center group-hover:bg-primary/20 transition-colors"
                        >
                            <span
                                class="text-primary font-mono text-sm font-bold"
                                >JS</span
                            >
                        </div>
                        <div>
                            <h3 class="text-white font-medium">JavaScript</h3>
                            <p class="text-muted text-xs">42 articles</p>
                        </div>
                    </div>
                </a>

                <a
                    href="#"
                    class="p-4 bg-surface border border-border rounded-lg hover:border-primary hover:bg-surface/80 transition-all group"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center group-hover:bg-blue-500/20 transition-colors"
                        >
                            <span
                                class="text-blue-400 font-mono text-sm font-bold"
                                >TS</span
                            >
                        </div>
                        <div>
                            <h3 class="text-white font-medium">TypeScript</h3>
                            <p class="text-muted text-xs">28 articles</p>
                        </div>
                    </div>
                </a>

                <a
                    href="#"
                    class="p-4 bg-surface border border-border rounded-lg hover:border-primary hover:bg-surface/80 transition-all group"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-cyan-500/10 rounded-lg flex items-center justify-center group-hover:bg-cyan-500/20 transition-colors"
                        >
                            <span
                                class="text-cyan-400 font-mono text-sm font-bold"
                                >⚛</span
                            >
                        </div>
                        <div>
                            <h3 class="text-white font-medium">React</h3>
                            <p class="text-muted text-xs">35 articles</p>
                        </div>
                    </div>
                </a>

                <a
                    href="#"
                    class="p-4 bg-surface border border-border rounded-lg hover:border-primary hover:bg-surface/80 transition-all group"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center group-hover:bg-green-500/20 transition-colors"
                        >
                            <span
                                class="text-green-400 font-mono text-sm font-bold"
                                >▲</span
                            >
                        </div>
                        <div>
                            <h3 class="text-white font-medium">Next.js</h3>
                            <p class="text-muted text-xs">24 articles</p>
                        </div>
                    </div>
                </a>

                <a
                    href="#"
                    class="p-4 bg-surface border border-border rounded-lg hover:border-primary hover:bg-surface/80 transition-all group"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-purple-500/10 rounded-lg flex items-center justify-center group-hover:bg-purple-500/20 transition-colors"
                        >
                            <span
                                class="text-purple-400 font-mono text-sm font-bold"
                                >DB</span
                            >
                        </div>
                        <div>
                            <h3 class="text-white font-medium">Databases</h3>
                            <p class="text-muted text-xs">18 articles</p>
                        </div>
                    </div>
                </a>

                <a
                    href="#"
                    class="p-4 bg-surface border border-border rounded-lg hover:border-primary hover:bg-surface/80 transition-all group"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-yellow-500/10 rounded-lg flex items-center justify-center group-hover:bg-yellow-500/20 transition-colors"
                        >
                            <span
                                class="text-yellow-400 font-mono text-sm font-bold"
                                >⚡</span
                            >
                        </div>
                        <div>
                            <h3 class="text-white font-medium">Performance</h3>
                            <p class="text-muted text-xs">15 articles</p>
                        </div>
                    </div>
                </a>

                <a
                    href="#"
                    class="p-4 bg-surface border border-border rounded-lg hover:border-primary hover:bg-surface/80 transition-all group"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-red-500/10 rounded-lg flex items-center justify-center group-hover:bg-red-500/20 transition-colors"
                        >
                            <span
                                class="text-red-400 font-mono text-sm font-bold"
                                >🔒</span
                            >
                        </div>
                        <div>
                            <h3 class="text-white font-medium">Security</h3>
                            <p class="text-muted text-xs">12 articles</p>
                        </div>
                    </div>
                </a>

                <a
                    href="#"
                    class="p-4 bg-surface border border-border rounded-lg hover:border-primary hover:bg-surface/80 transition-all group"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-pink-500/10 rounded-lg flex items-center justify-center group-hover:bg-pink-500/20 transition-colors"
                        >
                            <span
                                class="text-pink-400 font-mono text-sm font-bold"
                                >UI</span
                            >
                        </div>
                        <div>
                            <h3 class="text-white font-medium">UI/UX</h3>
                            <p class="text-muted text-xs">20 articles</p>
                        </div>
                    </div>
                </a>
            </div>
        </section>

        <!-- Newsletter Section -->
        <section
            id="newsletter"
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16"
        >
            <div
                class="bg-surface border border-border rounded-2xl p-8 md:p-12"
            >
                <div class="max-w-2xl mx-auto text-center">
                    <h2 class="text-3xl font-bold text-white mb-4">
                        Stay Updated
                    </h2>
                    <p class="text-gray-400 mb-8 leading-relaxed">
                        Get the latest articles and tutorials delivered to your
                        inbox every week. No spam, unsubscribe anytime.
                    </p>
                    <form
                        class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto"
                    >
                        <input
                            type="email"
                            placeholder="your@email.com"
                            class="flex-1 px-4 py-3 bg-background border border-border rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-primary transition-colors"
                        />
                        <button
                            type="submit"
                            class="px-6 py-3 bg-primary hover:bg-primary-hover text-background font-medium rounded-lg transition-colors whitespace-nowrap"
                        >
                            Subscribe
                        </button>
                    </form>
                    <p class="text-muted text-xs mt-4">
                        Join 5,000+ developers already subscribed
                    </p>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section
            id="about"
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16"
        >
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-white mb-4">
                        About the Author
                    </h2>
                    <p class="text-gray-400 mb-4 leading-relaxed">
                        Hi, I'm a full-stack developer passionate about building
                        modern web applications and sharing knowledge with the
                        developer community.
                    </p>
                    <p class="text-gray-400 mb-6 leading-relaxed">
                        With over 8 years of experience in web development, I've
                        worked with startups and enterprises to build scalable
                        applications. This blog is my way of giving back to the
                        community that helped me grow.
                    </p>
                    <div class="flex items-center gap-4">
                        <a
                            href="#"
                            class="text-gray-400 hover:text-primary transition-colors"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"
                                />
                            </svg>
                        </a>
                        <a
                            href="#"
                            class="text-gray-400 hover:text-primary transition-colors"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"
                                />
                            </svg>
                        </a>
                        <a
                            href="#"
                            class="text-gray-400 hover:text-primary transition-colors"
                        >
                            <svg
                                class="w-6 h-6"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"
                                />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="relative">
                    <div
                        class="aspect-square bg-gradient-to-br from-primary/20 to-background border border-border rounded-2xl flex items-center justify-center"
                    >
                        <div
                            class="w-32 h-32 bg-primary/10 rounded-full flex items-center justify-center"
                        >
                            <svg
                                class="w-16 h-16 text-primary"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                />
                            </svg>
                        </div>
                    </div>
                    <div
                        class="absolute -bottom-4 -right-4 w-24 h-24 bg-primary/5 border border-border rounded-lg"
                    ></div>
                    <div
                        class="absolute -top-4 -left-4 w-16 h-16 bg-primary/5 border border-border rounded-lg"
                    ></div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-border mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <div
                            class="flex items-center gap-2 text-xl font-semibold text-white mb-4"
                        >
                            <div
                                class="w-8 h-8 bg-primary rounded-full flex items-center justify-center"
                            >
                                <span class="text-background font-bold text-sm"
                                    >&lt;/&gt;</span
                                >
                            </div>
                            DevFeed
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Insights and tutorials for modern web developers.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-white font-semibold mb-4">Content</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <a
                                    href="#"
                                    class="text-gray-400 hover:text-primary transition-colors"
                                    >All Articles</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="text-gray-400 hover:text-primary transition-colors"
                                    >Topics</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="text-gray-400 hover:text-primary transition-colors"
                                    >Tutorials</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="text-gray-400 hover:text-primary transition-colors"
                                    >Newsletter</a
                                >
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-white font-semibold mb-4">Connect</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <a
                                    href="#"
                                    class="text-gray-400 hover:text-primary transition-colors"
                                    >GitHub</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="text-gray-400 hover:text-primary transition-colors"
                                    >Twitter</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="text-gray-400 hover:text-primary transition-colors"
                                    >LinkedIn</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="text-gray-400 hover:text-primary transition-colors"
                                    >RSS Feed</a
                                >
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-white font-semibold mb-4">Legal</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <a
                                    href="#"
                                    class="text-gray-400 hover:text-primary transition-colors"
                                    >Privacy Policy</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="text-gray-400 hover:text-primary transition-colors"
                                    >Terms of Service</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="text-gray-400 hover:text-primary transition-colors"
                                    >Cookie Policy</a
                                >
                            </li>
                        </ul>
                    </div>
                </div>

                <div
                    class="pt-8 border-t border-border flex flex-col sm:flex-row items-center justify-between gap-4"
                >
                    <p class="text-muted text-sm">
                        © 2025 DevFeed. All rights reserved.
                    </p>
                    <p class="text-muted text-sm">
                        Built with HTML & Tailwind CSS
                    </p>
                </div>
            </div>
        </footer>
    </body>
</template>
