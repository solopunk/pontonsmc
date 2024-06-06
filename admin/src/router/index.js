import { createRouter, createWebHistory } from "vue-router";
import { store } from "../../store";

import MembersView from "@/views/MembersView.vue";
import MemberView from "@/views/MemberView.vue";
import LoginView from "@/views/LoginView.vue";
import AddMemberView from "@/views/add/AddMemberView.vue";
import ScoopsView from "@/views/ScoopsView.vue";
import MailsView from "@/views/MailsView.vue";
import ScoopView from "@/views/ScoopView.vue";

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
      meta: {
        title: "Connexion",
      },
    },
    {
      path: "/scoops",
      name: "scoops",
      component: ScoopsView,
      meta: {
        title: "Actualités",
      },
    },
    {
      path: "/scoops/:id",
      name: "scoop",
      component: ScoopView,
      meta: {
        title: "Actualité",
      },
    },
    {
      path: "/members",
      name: "members",
      component: MembersView,
      meta: {
        title: "Adhérents",
      },
    },
    {
      path: "/members/:id",
      name: "member",
      component: MemberView,
      meta: {
        title: "Adhérent",
      },
    },
    {
      path: "/add-member",
      name: "add-member",
      component: AddMemberView,
      meta: {
        title: "Ajouter un adhérent",
      },
    },
    {
      path: "/mails",
      name: "mails",
      component: MailsView,
      meta: {
        title: "Courriels",
      },
    },
  ],
});

router.beforeEach(async (to, from) => {
  // auth
  if (!store.isAuthenticated && to.name !== "login") {
    return { name: "login" };
  }
  // page titles
  if (to.meta.title) {
    document.title = to.meta.title;
  }
});

export default router;
