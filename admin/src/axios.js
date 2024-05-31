import axios from "axios";

axios.defaults.baseURL = "http://localhost:8000";
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;

axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response.status === 401) {
      // Déconnexion de l'utilisateur si le statut 401 est retourné
      logout();
    }
    return Promise.reject(error);
  }
);

export default axios;
