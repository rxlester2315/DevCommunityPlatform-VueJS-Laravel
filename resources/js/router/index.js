import { createRouter, createWebHistory } from "vue-router";
import UserIndex from "../components/user/index.vue";
import notFound from "../components/notFound.vue";
import userLogin from "../components/auth/login.vue";
import userRegister from "../components/auth/register.vue";
import userHome from "../components/user/home.vue";
import userProfiles from "../components/user/profiles.vue";
import { auth } from "../utils/auth";
import SetupProfileS from "../components/user/setupProfile.vue";
import postComments from "../components/user/comment.vue";
import axios from "axios";
import VisitProfiles from "../components/user/visitProfile.vue";
import followingUser from "../components/user/follows/following.vue";
import followersUser from "../components/user/follows/followers.vue";

const routes = [
    {
        path: "/",
        name: "user.index",
        component: UserIndex,
        meta: { requiresAuth: false },
    },
    {
        path: "/login",
        name: "auth.login",
        component: userLogin,
        meta: { requiresAuth: false, guestOnly: true },
    },
    {
        path: "/registers",
        name: "auth.register",
        component: userRegister,
        meta: { requiresAuth: false, guestOnly: true },
    },

    {
        path: "/profiles",
        name: "user.profiles",
        component: userProfiles,
        meta: { requiresAuth: true, requiresProfile: true },
    },
    {
        path: "/setupprofile",
        name: "user.setupprofile",
        component: SetupProfileS,
        meta: { requiresAuth: true, requiresProfile: false },
    },
    {
        path: "/home",
        name: "user.home",
        component: userHome,
        meta: { requiresAuth: true },
    },

    {
        path: "/comments/:id",
        name: "user.comment",
        component: postComments,
        meta: { requiresAuth: true, requiresProfile: true },
    },

    {
        path: "/profile/visit/:id",
        name: "user.visitProfile",
        component: VisitProfiles,
        meta: { requiresAuth: true, requiresProfile: true },
    },

    {
        path: "/profile/following",
        name: "user.follows.following",
        component: followingUser,
        meta: { requiresAuth: true, requiresProfile: true },
    },

    {
        path: "/profile/followers",
        name: "user.follows.followers",
        component: followersUser,
        meta: { requiresAuth: true, requiresProfile: true },
    },

    {
        path: "/:pathMatch(.*)",
        name: "notFound",
        component: notFound,
        meta: { requiresAuth: false },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

//router.beforeEach() is a function that runs before every page navigation (route change) in your Vue app.
// this like gate before enting to certain gate so more on guard siya/
//It’s like a security gate that checks if the user is allowed to go to a certain route (page).

// to → where you’re going (destination route)

// from → where you came from

// next() → a function you call to allow or block navigation

router.beforeEach(async (to, from, next) => {
    console.log("Navigation guard triggered");
    console.log("To:", to.path, "Requires auth:", to.meta.requiresAuth);

    // if yung page or yung route is may requireAuth
    // If the route (in router/index.js) has meta: { requiresAuth: true },
    // it means the page is protected
    if (to.meta.requiresAuth) {
        const user = localStorage.getItem("user");
        console.log("User found in localStorage:", user);

        // then here it check if naka login ba si user i naka login may console log
        //      if a user is saved in localStorage (or sometimes sessionStorage).
        //     If yes → proceed.
        //    If not → redirect to /login.
        if (user) {
            try {
                const userData = JSON.parse(user);
                console.log("Parsed user data:", userData);

                //It check if my profile siya before mag proceed
                if (to.meta.requiresProfile) {
                    console.log("Checking profile existence...");
                    try {
                        const response = await axios.get("/api/profile/check");

                        if (response.data.has_profile) {
                            console.log(
                                "User has profile, proceeding to profile page"
                            );
                            next();
                        } else {
                            console.log(
                                "User doesn't have profile, redirecting to setup"
                            );
                            next("/setupprofile");
                        }
                    } catch (error) {
                        console.error("Error checking profile:", error);

                        if (error.response && error.response.status === 401) {
                            console.log(
                                "Session expired, redirecting to login"
                            );
                            localStorage.removeItem("user");
                            next("/login");
                        } else {
                            next("/setupprofile");
                        }
                    }
                } else {
                    next();
                }
            } catch (error) {
                console.error("Error parsing user data:", error);
                localStorage.removeItem("user");
                next("/login");
            }
        } else {
            console.log("No user found, redirecting to login");
            next("/login");
        }
    } else {
        if (to.meta.guestOnly) {
            const user = localStorage.getItem("user");
            if (user) {
                console.log(
                    "User is authenticated, redirecting from guest route to home"
                );
                next("/home");
                return;
            }
        }

        next();
    }
});

export default router;
