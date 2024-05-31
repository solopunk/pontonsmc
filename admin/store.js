import { reactive } from "vue";

const isAuthenticated = localStorage.getItem("isAuthenticated") === "true";

export const store = reactive({
  isAuthenticated,
});

export function login() {
  store.isAuthenticated = true;
  localStorage.setItem("isAuthenticated", "true");
}

export function logout() {
  store.isAuthenticated = false;
  localStorage.removeItem("isAuthenticated");
}
