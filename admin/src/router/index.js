import { createRouter, createWebHistory } from "vue-router";
import { store } from "../../store";

import MembersView from "@/views/MembersView.vue";
import MemberView from "@/views/MemberView.vue";
import LoginView from "@/views/LoginView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/:pathMatch(.*)*",
      name: "not-found",
      component: () => import("../views/NotFoundView.vue"),
    },
    {
      path: "/login",
      name: "login",
      component: LoginView,
    },
    {
      path: "/members",
      name: "members",
      component: MembersView,
    },
    {
      path: "/members/:id",
      name: "member",
      component: MemberView,
    },
  ],
});

// auth
router.beforeEach(async (to, from) => {
  if (!store.isAuthenticated && to.name !== "login") {
    return { name: "login" };
  }
});

export default router;
