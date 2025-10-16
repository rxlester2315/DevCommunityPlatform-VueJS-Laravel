<template>
    <body class="bg-[#0a0a0a] text-gray-100 min-h-screen">
        <header
            class="border-b border-gray-800 bg-black/50 backdrop-blur-sm sticky top-0 z-50"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-code text-2xl text-[#ff6b35]"></i>
                        <span class="text-xl font-bold">DevBlog</span>
                    </div>
                    <button class="text-gray-400 hover:text-gray-300 text-sm">
                        Skip for now
                    </button>
                </div>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold mb-4">Welcome to DevBlog! 👋</h1>
                <p class="text-gray-400 text-lg">
                    Let's set up your profile to get started
                </p>
            </div>

            <div class="step-content active" id="step-1">
                <div class="bg-[#161616] border border-gray-800 rounded-lg p-8">
                    <h2 class="text-2xl font-bold mb-6">Basic Information</h2>

                    <div class="mb-8 text-center">
                        <div class="mb-4 flex justify-center">
                            <div class="relative">
                                <img
                                    src=""
                                    alt="Avatar"
                                    class="avatar-preview"
                                    id="avatar-preview"
                                />
                                <label
                                    for="avatar-upload"
                                    class="absolute bottom-0 right-0 bg-[#ff6b35] hover:bg-[#ff5722] w-10 h-10 rounded-full flex items-center justify-center cursor-pointer transition-colors"
                                >
                                    <i class="fas fa-camera text-white"></i>
                                </label>
                                <input
                                    type="file"
                                    id="avatar-upload"
                                    class="hidden"
                                    accept="image/*"
                                />
                            </div>
                        </div>
                        <p class="text-sm text-gray-400">
                            Upload a profile picture
                        </p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium mb-2"
                                >Username *</label
                            >
                            <input
                                type="text"
                                placeholder="Username"
                                class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-[#ff6b35] transition-colors"
                                required
                            />
                            <p class="text-xs text-gray-500 mt-1">
                                This will be your unique identifier
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2"
                                >Display Name *</label
                            >
                            <input
                                type="text"
                                placeholder="Name"
                                class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-[#ff6b35] transition-colors"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2"
                                >Bio</label
                            >
                            <textarea
                                rows="4"
                                placeholder="Tell us about yourself..."
                                class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-[#ff6b35] transition-colors resize-none"
                            ></textarea>
                            <p class="text-xs text-gray-500 mt-1">
                                Max 160 characters
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2"
                                >Location</label
                            >
                            <input
                                type="text"
                                placeholder="San Francisco, CA"
                                class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-[#ff6b35] transition-colors"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2"
                                >Website
                                <span class="text-sm text-gray-400"
                                    >(Optional)</span
                                ></label
                            >
                            <input
                                type="url"
                                placeholder="Porfolio"
                                class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-[#ff6b35] transition-colors"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2"
                                >Github
                                <span class="text-sm text-gray-400"
                                    >(Optional)</span
                                ></label
                            >
                            <input
                                type="url"
                                placeholder="Github Profile"
                                class="w-full bg-black border border-gray-700 rounded-lg px-4 py-3 focus:outline-none focus:border-[#ff6b35] transition-colors"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end mt-8">
                        <button
                            onclick="nextStep(2)"
                            class="bg-[#ff6b35] hover:bg-[#ff5722] text-white px-8 py-3 rounded-lg font-medium transition-colors"
                        >
                            Continue
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </body>
</template>
