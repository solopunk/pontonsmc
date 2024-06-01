import { reactive } from "vue";
import { useRouter } from "vue-router";

const isAuthenticated = localStorage.getItem("isAuthenticated") === "true";

export const store = reactive({
  isAuthenticated,
});

export function login(email) {
  store.isAuthenticated = true;
  localStorage.setItem("isAuthenticated", "true");
  localStorage.setItem("adminEmail", email);
}

export function logout() {
  store.isAuthenticated = false;
  localStorage.removeItem("isAuthenticated");
  localStorage.removeItem("adminEmail");

  const router = useRouter();
  router.push({ name: "login" });
}
