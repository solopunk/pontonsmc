import axios from "axios";
import { logout } from "../store";
import router from "./router";

axios.defaults.baseURL = "http://localhost:8000";
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;

axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      const status = error.response.status;
      if (status === 419 || status === 401) {
        router.push({ name: "login" });
        logout();
      }
    }
    return Promise.reject(error);
  },
);

export default axios;
